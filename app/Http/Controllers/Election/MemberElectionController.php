<?php

namespace App\Http\Controllers\Election;

use App\Http\Controllers\Controller;
use App\Models\Elections\Election;
use App\Models\Elections\ElectionCandidateApplication;
use App\Models\Elections\ElectionPosition;
use App\Models\Members\MemberAcademicInfo;
use Carbon\Carbon;
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
    public function memberInitiateSelfApplyForPositionSubmit($uuid, Request $request)
    {
        $position = ElectionPosition::where('uuid', $uuid)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'Records not found!');
        }


        $request->validate([
            'bio' => 'required|string',
            'manifesto' => 'required|string',
        ]);



        // already applied for this position
        $hasAlreadyApplied = ElectionCandidateApplication::where('election_id', $position->election_id)->where('position_id', $position->position_id)->where('candidate', Auth::user()->username)->first();
        if ($hasAlreadyApplied) {
            return redirect()->route('myElectionApplications', ['uuid' => $position->election->uuid])->with('error', 'You have a pending application or you have already applied for this position!');
        }

        // check if member has applied for a position before and if he can apply for multiple position
        if ($position->election->one_candidate_multiple_positions !== true) {
            $haveSomeApplications = ElectionCandidateApplication::where('election_id', $position->election_id)->where('candidate', Auth::user()->username)->first();
            if ($haveSomeApplications) {
                return redirect()->route('myElectionApplications', ['uuid' => $position->election->uuid])->with('error', 'You have a pending application or you have already applied for a position!');
            }
        }

        // check if he qualifies
        $qualification = canApplyForPosition(Auth::user(), $position);
        if (!$qualification['qualified']) {
            return redirect()->back()->with('error', $qualification['message']);
        }


        // academic info start
        $academicInfo = MemberAcademicInfo::where('username', Auth::user()->username)->where('academic_status', 'student')->first();
        if (!$academicInfo) {
            return redirect()->route('myElectionApplications', ['uuid' => $position->election->uuid])->with('error', 'Academic info not found!');
        }
        $currentLevel = null;
        if ($academicInfo?->academic_status == 'student') {
            $currentLevel = currentLevel($academicInfo?->theProgram, studentCurrentLevel($academicInfo));
        }
        // academic info end

        ElectionCandidateApplication::updateOrCreate(
            [
                'candidate'   => Auth::user()->username,
                'position_id' => $position->id,
                'election_id' => $position->election_id,
                'application_type' => 'self'
            ],
            [
                'bio'         => $request->input('bio'),
                'manifesto'   => $request->input('manifesto'),
                'current_program' => $academicInfo->program,
                'current_program_type' => $academicInfo->program_type,
                'current_faculty' => $academicInfo->faculty,
                'current_department' => $academicInfo->department,
                'level_at_application' => $currentLevel,
                'initiated_by' => Auth::user()->username,
            ]
        );

        return redirect()->route('myElectionApplications', ['uuid' => $position->election->uuid])->with('success', 'Done!');
    }
    public function myElectionApplications($uuid)
    {
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $applications = ElectionCandidateApplication::where('election_id', $election->election_id)->where('initiated_by', Auth::user()->username)->orderBy('updated_at', 'desc')->paginate(20);
        if (!$applications) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'election' => $election,
            'applications' => $applications,
        ];
        return view('users.member.elections.myElectionApplications', compact('data'));
    }
    public function viewMyElectionApplication($uuid)
    {
        $application = ElectionCandidateApplication::where('uuid', $uuid)->where('initiated_by', Auth::user()->username)->first();
        if (!$application) {
            return redirect()->back()->with('error', 'Records not found!');
        }

          $data = [
            'application' => $application,
            'election' => $application->election,
        ];
        return view('users.member.elections.viewMyElectionApplications', compact('data'));
    }
}
