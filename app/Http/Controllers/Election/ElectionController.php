<?php

namespace App\Http\Controllers\Election;

use App\Http\Controllers\Controller;
use App\Models\Elections\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ElectionController extends Controller
{
    public function allElections(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $elections = '';
        $totalElections = 0;


        if (Route::currentRouteName() == 'allElectionsSearch') {
            $searchKey = $request->input('searchKey');
            $elections = Election::when($searchKey, function ($query) use ($searchKey) {
                $query->where(function ($q) use ($searchKey) {
                    $q->where('election_id', 'like', "%{$searchKey}%")
                        ->orWhere('voting_method', 'like', "%{$searchKey}%")
                        ->orWhere('title', 'like', "%{$searchKey}%")
                        ->orWhere('description', 'like', "%{$searchKey}%")
                        ->orWhere('status', 'like', "%{$searchKey}%");
                });
            })
                ->orderByRaw("
        CASE
            WHEN status = 'voting' THEN 0
            ELSE 1
        END ")->orderByDesc('updated_at')->paginate(20)->withQueryString();

            $totalElections = $elections->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $elections = Election::query()
                ->orderByRaw("CASE WHEN approval_status = 'pending' THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->paginate(20);
            $totalElections = Election::count();
            $requestType = 'all';
            $pageTitle = 'Elections';
        }
        $data = [
            'elections' => $elections,
            'totalElections' => $totalElections,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.elections.elections', compact('data'));
    }
}
