<?php

namespace App\Http\Controllers\Election;

use App\Http\Controllers\Controller;
use App\Models\Elections\Election;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

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
    public function addNewElectionSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.create'),
            403
        );

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'voting_method' => ['required', 'in:standard,monetary'],

            'cost_per_vote' => [
                'nullable',
                'numeric',
                'min:0',
                'required_if:voting_method,monetary',
            ],

            'public_visibility' => 'required',
            'form_sales_starts' => 'required|date',
            'form_sales_ends' => 'required|date|after:form_sales_starts',

            'screening_starts' => 'nullable|date',
            'screening_ends' => [
                Rule::requiredIf($request->filled('screening_starts')),
                'nullable',
                'date',
                'after:screening_starts',
            ],

            'campaign_starts' => 'nullable|date',
            'campaign_ends' => [
                Rule::requiredIf($request->filled('campaign_starts')),
                'nullable',
                'date',
                'after:campaign_starts',
            ],

            'voting_starts' => 'required|date|after:form_sales_ends',
            'voting_ends' => 'required|date|after:voting_starts',
        ]);

        do {
            $electionId = 'election-' . now()->format('Y') . '-' . tenant()->id . random_int(100000000, 999999999);
        } while (Election::where('payment_transaction_id', $electionId)->exists());

        Election::create([
            'election_id' => $electionId,
            'voting_method' => $request->voting_method,
            'public_visibility' => $request->public_visibility,
            'title' => trim($request->title),
            'description' => trim($request->description),
            'status' => 'draft',
            'form_sales_starts' => $request->form_sales_starts,
            'form_sales_ends' => $request->form_sales_ends,
            'screening_starts' => $request->screening_starts,
            'screening_ends' => $request->screening_ends,
            'campaign_starts' => $request->campaign_starts,
            'campaign_ends' => $request->campaign_ends,
            'voting_starts' => $request->voting_starts,
            'voting_ends' => $request->voting_ends,
            'cost_per_vote' => trim($request->cost_per_vote),
            'initiated_by' => Auth::user()->username,
            'initiated_at' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
}
