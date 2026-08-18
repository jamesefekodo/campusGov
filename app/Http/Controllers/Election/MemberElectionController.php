<?php

namespace App\Http\Controllers\Election;

use App\Http\Controllers\Controller;
use App\Models\Elections\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class MemberElectionController extends Controller
{
    public function memberNewElections(Request $request)
    {
        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $elections = '';
        $totalElections = 0;


        if (Route::currentRouteName() == 'memberNewElectionsSearch') {
            $searchKey = $request->input('searchKey');
            $elections = Election::where('approval_status', 'approved')
                ->whereIn('status', ['new', 'registration', 'screening', 'campaign'])
                // ->whereIn('status', [
                //     'new',
                //     'registration',
                //     'screening',
                //     'campaign',
                //     'voting',
                //     'result_pending',
                //     'completed',
                // ])
                ->when($searchKey, function ($query) use ($searchKey) {
                    // Group the search conditions to ensure they apply cleanly alongside other constraints
                    $query->where(function ($q) use ($searchKey) {
                        $q->where('election_id', 'like', "%{$searchKey}%")
                            ->orWhere('voting_method', 'like', "%{$searchKey}%")
                            ->orWhere('title', 'like', "%{$searchKey}%")
                            ->orWhere('description', 'like', "%{$searchKey}%")
                            ->orWhere('status', 'like', "%{$searchKey}%");
                    });
                })
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();

            $totalElections = $elections->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $elections = Election::where('approval_status', 'approved')
                ->whereIn('status', ['new', 'registration', 'screening', 'campaign'])
                // ->whereIn('status', [
                //     'new',
                //     'registration',
                //     'screening',
                //     'campaign',
                //     'voting',
                //     'result_pending',
                //     'completed',
                // ])
                ->orderByDesc('created_at')
                ->paginate(20);

            $totalElections = $elections->total();
            $requestType = 'all';
            $pageTitle = 'New Elections';
        }
        $data = [
            'elections' => $elections,
            'totalElections' => $totalElections,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.member.elections.elections', compact('data'));
    }
    public function memberViewElection($uuid)
    {
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'election' => $election,
        ];
        return view('users.member.elections.viewElection', compact('data'));
    }
}
