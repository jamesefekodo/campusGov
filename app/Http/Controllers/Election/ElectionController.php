<?php

namespace App\Http\Controllers\Election;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\Elections\Election;
use App\Models\Elections\ElectionGeneralQualifiedCandidateGroup;
use App\Models\Elections\ElectionGeneralQualifiedVoterGroup;
use App\Models\Elections\ElectionPosition;
use App\Models\Elections\ElectionPositionQualifiedCandidateGroup;
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
            'form_sales_starts' => 'required|date|after:' . now()->addHours(48),
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
            'academic_session' => 'required|integer|digits:4',
            'voting_starts' => 'required|date|after:form_sales_ends',
            'voting_ends' => 'required|date|after:voting_starts',
        ]);

        do {
            $electionId = 'election-' . now()->format('Y') . '-' . tenant()->id . random_int(100000000, 999999999);
        } while (Election::where('election_id', $electionId)->exists());

        Election::create([
            'election_id' => $electionId,
            'voting_method' => $request->voting_method,
            'public_visibility' => $request->public_visibility,
            'title' => trim($request->title),
            'academic_session' => trim($request->academic_session),
            'description' => trim($request->description),
            'status' => 'draft',
            'form_sales_starts' => $request->form_sales_starts,
            'form_sales_ends' => $request->form_sales_ends,
            'screening_starts' => $request->screening_starts ?? null,
            'screening_ends' => $request->screening_ends ?? null,
            'campaign_starts' => $request->campaign_starts ?? null,
            'campaign_ends' => $request->campaign_ends ?? null,
            'voting_starts' => $request->voting_starts,
            'voting_ends' => $request->voting_ends,
            'cost_per_vote' => trim($request->cost_per_vote) !== ''
                ? trim($request->cost_per_vote)
                : null,
            'initiated_by' => Auth::user()->username,
            'initiated_at' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function viewElection($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.read'),
            403
        );

        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $activeProgram = Program::where('is_active', true)->orderBy('name')->get();
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();

        $data = [
            'election' => $election,
            'activeProgram' => $activeProgram,
            'activeFaculties' => $activeFaculties,
        ];

        return view('users.admin.elections.viewElection', compact('data'));
    }
    public function updateElectionSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.update'),
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
            'form_sales_starts' => 'required|date|after:' . now()->addHours(5),
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
            'academic_session' => 'required|integer|digits:4',
            'voting_starts' => 'required|date|after:form_sales_ends',
            'voting_ends' => 'required|date|after:voting_starts',
        ]);

        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $election->update([
            'voting_method' => $request->voting_method,
            'public_visibility' => $request->public_visibility,
            'title' => trim($request->title),
            'academic_session' => trim($request->academic_session),
            'description' => trim($request->description),
            'form_sales_starts' => $request->form_sales_starts,
            'form_sales_ends' => $request->form_sales_ends,
            'screening_starts' => $request->screening_starts ?? null,
            'screening_ends' => $request->screening_ends ?? null,
            'campaign_starts' => $request->campaign_starts ?? null,
            'campaign_ends' => $request->campaign_ends ?? null,
            'voting_starts' => $request->voting_starts,
            'voting_ends' => $request->voting_ends,
            'cost_per_vote' => trim($request->cost_per_vote) !== ''
                ? trim($request->cost_per_vote)
                : null,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }

    public function addNewQualifiedVoterGroupSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.create'),
            403
        );
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $request->validate([
            'program' => 'required|exists:programs,slug',
            'program_type' => 'nullable|exists:program_types,slug',
            'faculty' => 'nullable|exists:faculties,slug',
            'department' => 'nullable|exists:departments,slug',
            'start_level' => 'nullable|numeric',
            'end_level' => 'nullable|numeric|gte:start_level',
        ]);

        ElectionGeneralQualifiedVoterGroup::create([
            'election_id' => $election->election_id,
            'program' => $request->program,
            'program_type' => $request->program_type,
            'faculty' => $request->faculty,
            'department' => $request->department,
            'start_level' => $request->start_level,
            'end_level' => $request->end_level,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function removeQualifiedVoterGroup($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.delete'),
            403
        );
        $group = ElectionGeneralQualifiedVoterGroup::where('uuid', $uuid)->first();
        if (!$group) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $group->delete();
        return redirect()->back()->with('success', 'Done!');
    }
    public function updateQualifiedVoterGroupSubmit(Request $request, $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.update'),
            403
        );
        $group = ElectionGeneralQualifiedVoterGroup::where('uuid', $uuid)->first();
        if (!$group) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $request->validate([
            'program' => 'required|exists:programs,slug',
            'program_type' => 'nullable|exists:program_types,slug',
            'faculty' => 'nullable|exists:faculties,slug',
            'department' => 'nullable|exists:departments,slug',
            'start_level' => 'nullable|numeric',
            'end_level' => 'nullable|numeric|gte:start_level',
        ]);

        $group->update([
            'program' => $request->program,
            'program_type' => $request->program_type,
            'faculty' => $request->faculty,
            'department' => $request->department,
            'start_level' => $request->start_level,
            'end_level' => $request->end_level,
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
    public function addNewElectionPositionSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.create'),
            403
        );
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $request->validate([
            'position' => 'required|string',
            'description' => 'required|string',
            'application_fee' => 'required|numeric|min:0',
        ]);

        ElectionPosition::create([
            'election_id' => $election->election_id,
            'position' => $request->position,
            'description' => $request->description,
            'application_fee' => $request->application_fee,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function addNewPositionQualifiedCandidateGroupSubmit(Request $request, $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.create'),
            403
        );
        $position = ElectionPosition::where('uuid', $uuid)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $request->validate([
            'program' => 'required|exists:programs,slug',
            'program_type' => 'nullable|exists:program_types,slug',
            'faculty' => 'nullable|exists:faculties,slug',
            'department' => 'nullable|exists:departments,slug',
            'start_level' => 'nullable|numeric',
            'end_level' => 'nullable|numeric|gte:start_level',
        ]);

        ElectionPositionQualifiedCandidateGroup::create([
            'election_id' => $position->election_id,
            'position_id' => $position->id,
            'program' => $request->program,
            'program_type' => $request->program_type,
            'faculty' => $request->faculty,
            'department' => $request->department,
            'start_level' => $request->start_level,
            'end_level' => $request->end_level,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function updateQualifiedCandidateGroupSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.update'),
            403
        );

        $group = ElectionPositionQualifiedCandidateGroup::where('uuid', $uuid)->first();
        if (!$group) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $request->validate([
            'program' => 'required|exists:programs,slug',
            'program_type' => 'nullable|exists:program_types,slug',
            'faculty' => 'nullable|exists:faculties,slug',
            'department' => 'nullable|exists:departments,slug',
            'start_level' => 'nullable|numeric',
            'end_level' => 'nullable|numeric|gte:start_level',
        ]);

        $group->update([
            'program' => $request->program,
            'program_type' => $request->program_type,
            'faculty' => $request->faculty,
            'department' => $request->department,
            'start_level' => $request->start_level,
            'end_level' => $request->end_level,
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
    public function removeQualifiedCandidateGroup($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.delete'),
            403
        );

        $group = ElectionPositionQualifiedCandidateGroup::where('uuid', $uuid)->first();
        if (!$group) {
            return redirect()->back()->with('error', 'Record not found!');
        }
        $group->delete();
        return redirect()->back()->with('success', 'Done!');
    }
    public function submitElectionForApproval($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.read'),
            403
        );


        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        if (!$election->positions) {
            return redirect()->back()->with('error', 'Create at least one position!');
        }

        $election->update([
            'status' => 'awaiting_approval',
        ]);
        return redirect()->back()->with('success', 'Done!');
    }

    public function printElectionConcentAwarenessForm($uuid){
    /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.read'),
            403
        );

    }

    public function electionOperationRequests($uuid, Request $request) {}
}
