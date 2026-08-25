<?php

namespace App\Http\Controllers\Election;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Elections\Election;
use App\Models\Elections\ElectionCandidateApplication;
use App\Models\Elections\ElectionGeneralQualifiedCandidateGroup;
use App\Models\Elections\ElectionGeneralQualifiedVoterGroup;
use App\Models\Elections\ElectionMonetaryVote;
use App\Models\Elections\ElectionPosition;
use App\Models\Elections\ElectionPositionQualifiedCandidateGroup;
use App\Models\Elections\ElectionStandardVote;
use App\Models\Members\MemberPersonalProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mpdf\Mpdf;


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

        if ($election->status == 'voting') {
            $election->update(
                [
                    'qualified_voters' => totalQualifiedVoters($election),
                ]
            );
        }

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
            // 'form_sales_starts' => 'required|date|after:' . now()->addHours(5),
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
            'academic_session' => 'required|integer|digits:4',
            'voting_starts' => 'required|date|after:form_sales_ends',
            'voting_ends' => 'required|date|after:voting_starts',
            'one_candidate_multiple_positions' => 'required',
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
            'one_candidate_multiple_positions' => $request->one_candidate_multiple_positions,
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
    public function updateElectionPositionSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.update'),
            403
        );

        $position = ElectionPosition::where('uuid', $uuid)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'Record not found!');
        }
        $request->validate([
            'position' => 'required|string',
            'description' => 'required|string',
            'application_fee' => 'required|numeric|min:0',
        ]);

        $position->update([
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

        if (!$election->positions()->exists()) {
            return redirect()->back()->with('error', 'Create at least one position!');
        }

        $election->update([
            'status' => 'awaiting_approval',
        ]);
        return redirect()->back()->with('success', 'Done!');
    }

    public function printElectionConsentAwarenessForm($uuid)
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

        $electionSession = $election->academic_session . '/' . $election->academic_session + 1;


        ini_set('pcre.backtrack_limit', 10000000);
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');


        $mpdf = new Mpdf([
            'default_font_size'  => 10,
            'default_font'       => 'Arial',
            'margin_left'        => 5,
            'margin_right'       => 5,
            'margin_top'         => 10,
            'margin_bottom'      => 15,
            'format'             => 'A4',
            'autoLangToFont'     => true,
            'autoScriptToLang'   => true,
            'tempDir'            => storage_path('app/mpdf-temp'),
        ]);

        $mpdf->setFooter('Page {PAGENO} of {nb} >> Election Consent/Awareness Form : ' . $election->title . ' ' . $electionSession);
        $mpdf->showImageErrors = true;

        $basicSystemInfo = basicSystemInfo();
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $onlyFaculty = null;
        if ($basicSystemInfo->operational_level == 'faculty') {
            $onlyFaculty = Faculty::where('is_active', true)->first();
        }

        $onlyDepartment = null;
        if ($basicSystemInfo->operational_level == 'department') {
            $onlyDepartment = Department::where('is_active', true)->first();
        }

        $data = [
            'election' => $election,
            'faculties' => $faculties,
            'departments' => $departments,
            'basicSystemInfo' => $basicSystemInfo,
            'onlyFaculty' => $onlyFaculty,
            'onlyDepartment' => $onlyDepartment,

        ];

        $html = view('printFiles.elections.awarenessAndConsentForm', compact('data'))->render();

        $mpdf->WriteHTML($html); // AddPage() not needed — WriteHTML handles it

        $fileName = 'Election Consent/Awareness Form :' . $election->title . '_' . $electionSession . '_' . date('Y-m-d_H-i-s') . '.pdf';

        while (ob_get_level()) ob_end_clean();

        $mpdf->Output($fileName, 'I'); // 'I' for inline, 'D' for download
        exit;
    }

    public function electionOperationRequests()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.approve'),
            403
        );

        $electionApprovalRequests = Election::where('status', 'awaiting_approval')->orderBy('title')->get();
        $candidatesAwaitingApproval = ElectionCandidateApplication::where('status', 'pending_approval')->get();



        $data = [
            'electionApprovalRequests' => $electionApprovalRequests,
            'candidatesAwaitingApproval' => $candidatesAwaitingApproval,
        ];

        return view('users.admin.elections.electionRequests', compact('data'));
    }
    public function processedElectionOperationRequests()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.approve'),
            403
        );
        $alreadyProcessedCandidateRequests = ElectionCandidateApplication::where('status', '!=', 'pending_approval')->orderBy('created_at', 'desc')->paginate(20);

        $data = [
            'alreadyProcessedCandidateRequests' => $alreadyProcessedCandidateRequests,
        ];
        return view('users.admin.elections.processedElectionRequests', compact('data'));
    }
    public function approveElectionSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.approve'),
            403
        );

        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $request->validate([
            'status' => ['required', 'in:approved,declined'],
        ]);

        if ($request->status === 'approved') {
            $election->update([
                'status' => 'new',
                'approval_status' => 'approved',
            ]);
        } elseif ($request->status === 'declined') {
            $election->update([
                'status' => 'cancelled',
                'approval_status' => 'declined',
            ]);
        }

        $election->update([
            'processed_by' => Auth::user()->username,
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
    public function viewElectionCandidateApplication($uuid)
    {
        $application = ElectionCandidateApplication::where('uuid', $uuid)->first();
        if (!$application) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $data = [
            'application' => $application,
            'election' => $application->election,
        ];

        return view('users.admin.elections.viewElectionCandidateRequest', compact('data'));
    }
    public function processElectionCandidateRequestSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.approve'),
            403
        );

        $application = ElectionCandidateApplication::where('uuid', $uuid)->first();
        if (!$application) {
            return redirect()->back()->with('error', 'No records found!');
        }
        $request->validate([
            'status' => ['required', 'in:approved,declined'],
            'application_form' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3145728',
            'stakeholders_remarks' => 'nullable',
        ]);

        $application->update([
            'status' => $request->status,
            'stakeholders_remarks' => $request->stakeholders_remarks,
            'approved_at' => Carbon::now(),
            'processed_by' => Auth::user()->username,
        ]);


        if ($request->hasFile('application_form')) {

            $file = $request->file('application_form');
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $uploadPath = public_path('assets/tenant_' . tenant()->id . '/uploads/elections/applicationForm');
            File::ensureDirectoryExists($uploadPath);

            // Base filename
            $baseName = strtolower($application->uuid);

            $baseName = Str::of($baseName)
                ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                ->trim('_')
                ->toString();
            $fallbackName = $baseName . '.' . $ext;
            $counter = 1;

            // Delete existing file(s) with same base name regardless of extension
            foreach (glob($uploadPath . '/' . $baseName . '.*') as $existingFile) {
                if (file_exists($existingFile)) {
                    unlink($existingFile);
                }
            }

            // Avoid overwriting existing files
            while (file_exists($uploadPath . '/' . $fallbackName)) {
                $fallbackName = $baseName . '-' . $counter . '.' . $ext;
                $counter++;
            }

            $fallbackPath = $uploadPath . '/' . $fallbackName;

            /*
            |--------------------------------------------------------------------------
            | GD + WebP processing
            |--------------------------------------------------------------------------
            */
            $processed = false;

            if (function_exists('imagewebp') && function_exists('imagecreatetruecolor')) {

                $tmp = $file->getPathname();

                $src = match ($mime) {
                    'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($tmp) : null,
                    'image/png'  => function_exists('imagecreatefrompng')  ? @imagecreatefrompng($tmp)  : null,
                    'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : null,
                    default => null,
                };

                if ($src) {
                    $width  = imagesx($src);
                    $height = imagesy($src);

                    // Resize if needed
                    $maxSize = 800;
                    $scale = min(1, $maxSize / max($width, $height));

                    if ($scale < 1) {
                        $newW = (int)($width * $scale);
                        $newH = (int)($height * $scale);
                        $dst = imagecreatetruecolor($newW, $newH);

                        // Preserve transparency
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                        imagefill($dst, 0, 0, $transparent);

                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
                        imagedestroy($src);
                        $src = $dst;
                    }

                    // WebP conversion with quality loop
                    $webpName = $baseName . '.webp';
                    $webpCounter = 1;
                    $webpPath = $uploadPath . '/' . $webpName;

                    while (file_exists($webpPath)) {
                        $webpName = $baseName . '-' . $webpCounter . '.webp';
                        $webpPath = $uploadPath . '/' . $webpName;
                        $webpCounter++;
                    }

                    $quality = 90;
                    do {
                        imagewebp($src, $webpPath, $quality);
                        clearstatcache();

                        if (filesize($webpPath) <= 1048576) {
                            $processed = true;
                            $application->application_form = $webpName;
                            imagedestroy($src);
                            break;
                        }

                        $quality -= 5;
                    } while ($quality >= 40);

                    if (!$processed) {
                        @unlink($webpPath);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback: move original file
            |--------------------------------------------------------------------------
            */
            if (!$processed) {
                $file->move($uploadPath, $fallbackName);
                $application->application_form = $fallbackName;
            }
        }

        $application->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function electionCandidates($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.read'),
            403
        );
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $data = [
            'election' => $election,
        ];
        return view('users.admin.elections.candidates.candidates', compact('data'));
    }
    public function viewElectionCandidatesForPosition($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.read'),
            403
        );

        $position = ElectionPosition::where('uuid', $uuid)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'No records found!');
        }
        $data = [
            'position' => $position,
            'election' => $position->election,
        ];

        return view('users.admin.elections.candidates.viewCandidates', compact('data'));
    }

    public function electionResults($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.results'),
            403
        );

        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $standardVoteResults = ElectionStandardVote::where('election_id', $election->election_id)->get();



        // =====================================================
        // OVERALL VOTER PARTICIPATION
        // =====================================================

        $qualifiedVoters = $election->qualified_voters;

        $totalVoters = $standardVoteResults
            ->pluck('voter')
            ->unique()
            ->count();

        $totalNotVoted = max(0, $qualifiedVoters - $totalVoters);

        $participationRate = $qualifiedVoters > 0
            ? round(($totalVoters / $qualifiedVoters) * 100, 2)
            : 0;


        // =====================================================
        // VOTERS BY FACULTY
        // =====================================================

        $facultyNames = Faculty::pluck('name', 'slug');

        $votersByFaculty = $standardVoteResults
            ->groupBy('current_faculty')
            ->map(function ($votes) {
                return $votes->pluck('voter')->unique()->count();
            })
            ->sortDesc()
            ->mapWithKeys(function ($count, $slug) use ($facultyNames) {
                return [$facultyNames->get($slug, $slug) => $count];
            });


        // =====================================================
        // VOTERS BY DEPARTMENT
        // =====================================================

        $departmentNames = Department::pluck('name', 'slug');

        $votersByDepartment = $standardVoteResults
            ->groupBy('current_department')
            ->map(function ($votes) {
                return $votes->pluck('voter')->unique()->count();
            })
            ->sortDesc()
            ->mapWithKeys(function ($count, $slug) use ($departmentNames) {
                return [$departmentNames->get($slug, $slug) => $count];
            });

        // =====================================================
        // VOTERS BY PROGRAM
        // =====================================================

        $programNames = Program::pluck('name', 'slug');

        $votersByProgram = $standardVoteResults
            ->groupBy('current_program')
            ->map(function ($votes) {
                return $votes->pluck('voter')->unique()->count();
            })
            ->sortDesc()
            ->mapWithKeys(function ($count, $slug) use ($programNames) {
                return [$programNames->get($slug, $slug) => $count];
            });

        // =====================================================
        // VOTERS BY PROGRAM TYPE
        // =====================================================

        $programTypeNames = ProgramType::pluck('type_name', 'slug');

        $votersByProgramType = $standardVoteResults
            ->groupBy('current_program_type')
            ->map(function ($votes) {
                return $votes->pluck('voter')->unique()->count();
            })
            ->sortDesc()
            ->mapWithKeys(function ($count, $slug) use ($programTypeNames) {
                return [$programTypeNames->get($slug, $slug) => $count];
            });


        // =====================================================
        // VOTERS BY LEVEL
        // =====================================================

        $votersByLevel = $standardVoteResults
            ->groupBy('level_at_application')
            ->map(function ($votes) {
                return $votes->pluck('voter')->unique()->count();
            })
            ->sortKeys();


        // =====================================================
        // VOTES BY POSITION
        // =====================================================

        $positionNames = ElectionPosition::pluck('position', 'id');

        $votesByPosition = $standardVoteResults
            ->groupBy('position')
            ->map(function ($votes) {
                return $votes->count();
            })
            ->sortDesc()
            ->mapWithKeys(function ($count, $positionId) use ($positionNames) {
                return [$positionNames->get($positionId, $positionId) => $count];
            });

        // voting ttrends
        $votingTrend = $standardVoteResults
            ->groupBy(function ($vote) {
                return $vote->created_at->format('Y-m-d');
            })
            ->map(function ($votes) {
                return $votes->pluck('voter')->unique()->count();
            });


        // =====================================================
        // VOTES BY CANDIDATE
        // =====================================================



        $candidateAppIds = $standardVoteResults->pluck('candidate')->unique();

        $candidateNames = ElectionCandidateApplication::with('theCandidate.memberPersonalProfile')
            ->whereIn('application_id', $candidateAppIds)
            ->get()
            ->mapWithKeys(function ($application) {
                $profile = optional($application->theCandidate)->memberPersonalProfile;
                $name = optional($profile)->first_name;
                return [$application->application_id => $name ?? $application->application_id];
            });

        $votesByCandidate = $standardVoteResults
            ->groupBy('candidate')
            ->map(function ($votes) {
                return $votes->count();
            })
            ->sortDesc()
            ->mapWithKeys(function ($count, $applicationId) use ($candidateNames) {
                return [$candidateNames->get($applicationId, $applicationId) => $count];
            });

        // per gender
        $uniqueVoterUsernames = $standardVoteResults->pluck('voter')->unique();

        $votersByGender = MemberPersonalProfile::whereIn('username', $uniqueVoterUsernames)
            ->get()
            ->groupBy(function ($profile) {
                return $profile->gender ?: 'Unspecified';
            })
            ->map(function ($profiles) {
                return $profiles->count();
            })
            ->sortDesc();


        $data = [
            'election' => $election,
            'standardVoteResults' => $standardVoteResults,


            'qualifiedVoters' => $qualifiedVoters,
            'totalVoters' => $totalVoters,
            'totalNotVoted' => $totalNotVoted,
            'participationRate' => $participationRate,

            'votersByFaculty' => $votersByFaculty,
            'votersByDepartment' => $votersByDepartment,
            'votersByProgram' => $votersByProgram,
            'votersByProgramType' => $votersByProgramType,
            'votersByLevel' => $votersByLevel,

            'votesByPosition' => $votesByPosition,
            'votesByCandidate' => $votesByCandidate,

            'votingTrend' => $votingTrend,
            'votersByGender' => $votersByGender,
        ];
        return view('users.admin.elections.results.results', compact('data'));
    }
    public function viewElectionResultForPosition($election, $position)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.results'),
            403
        );

        $theElection = Election::where('uuid', $election)->first();
        if (!$theElection) {
            if (!$theElection) {
                return redirect()->back()->with('error', 'Election records not found!');
            }
        }

        $thePosition = ElectionPosition::where('uuid', $position)->first();
        if (!$thePosition) {
            if (!$thePosition) {
                return redirect()->back()->with('error', 'Position records not found!');
            }
        }


        // #########################################
        if ($theElection->voting_method == 'monetary') {
            $candidates = ElectionCandidateApplication::with([
                'theCandidate.memberPersonalProfile',
                'election',
            ])
                ->where('election_id', $theElection->election_id)
                ->where('position_id', $thePosition->id)
                ->get();

            $candidateResults = $candidates->map(function ($candidate) {

                $candidate->voteCount = $candidate->totalMonetaryVote();

                return $candidate;
            })->sortByDesc('voteCount')->values();
        } else {
            $candidates = ElectionCandidateApplication::with('theCandidate.memberPersonalProfile')
                ->where('election_id', $theElection->election_id)
                ->where('position_id', $thePosition->id)
                ->get();

            $voteCounts = ElectionStandardVote::where('election_id', $theElection->election_id)
                ->where('position', $thePosition->id)
                ->selectRaw('candidate, count(*) as votes')
                ->groupBy('candidate')
                ->pluck('votes', 'candidate');

            $candidateResults = $candidates->map(function ($candidate) use ($voteCounts) {
                $candidate->voteCount = $voteCounts->get($candidate->application_id, 0);
                return $candidate;
            })->sortByDesc('voteCount')->values();
        }

        $highestVotes = $candidateResults->max('voteCount');
        $totalVotes = $candidateResults->sum('voteCount');

        $data = [
            'election' => $theElection,
            'position' => $thePosition,

            'candidateResults'  => $candidateResults,
            'highestVotes'      => $highestVotes,
            'totalVotes'        => $totalVotes,
        ];
        return view('users.admin.elections.results.resultForPosition', compact('data'));
    }
    public function printElectionResults($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('elections.results'),
            403
        );

        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Record not found!');
        }
        if ($election->status !== 'completed') {
            return redirect()->back()->with('error', 'Election result is not available!');
        }

        $positions = $election->positions()
            ->with([
                'qualifiedCandidates.theCandidate.memberPersonalProfile',
            ])
            ->get();
        $standardVoteCounts = ElectionStandardVote::where(
            'election_id',
            $election->election_id
        )
            ->selectRaw('position, candidate, COUNT(*) as votes')
            ->groupBy('position', 'candidate')
            ->get()
            ->keyBy(function ($row) {
                return $row->position . '|' . $row->candidate;
            });

        $totalUniqueVoters = ElectionStandardVote::where(
            'election_id',
            $election->election_id
        )->distinct('voter')->count('voter');

        $totalQualifiedVoters = $election->qualified_voters;

        $participationPercentage = $totalQualifiedVoters > 0
            ? round(($totalUniqueVoters / $totalQualifiedVoters) * 100, 2)
            : 0;


        $electionSession = $election->academic_session . '/' . $election->academic_session + 1;


        ini_set('pcre.backtrack_limit', 10000000);
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');


        $mpdf = new Mpdf([
            'default_font_size'  => 10,
            'default_font'       => 'Arial',
            'margin_left'        => 5,
            'margin_right'       => 5,
            'margin_top'         => 10,
            'margin_bottom'      => 15,
            'format'             => 'A4',
            'autoLangToFont'     => true,
            'autoScriptToLang'   => true,
            'tempDir'            => storage_path('app/mpdf-temp'),
        ]);

        $mpdf->setFooter('Page {PAGENO} of {nb} >> Election Results : ' . $election->title . ' ' . $electionSession);
        $mpdf->showImageErrors = true;

        $basicSystemInfo = basicSystemInfo();
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $onlyFaculty = null;
        if ($basicSystemInfo->operational_level == 'faculty') {
            $onlyFaculty = Faculty::where('is_active', true)->first();
        }

        $onlyDepartment = null;
        if ($basicSystemInfo->operational_level == 'department') {
            $onlyDepartment = Department::where('is_active', true)->first();
        }


        // result analysis start
        $standardVoteCounts = ElectionStandardVote::where(
            'election_id',
            $election->election_id
        )
            ->selectRaw('position, candidate, COUNT(*) as votes')
            ->groupBy('position', 'candidate')
            ->get()
            ->keyBy(function ($row) {
                return $row->position . '|' . $row->candidate;
            });
        $monetaryVoteAmounts = ElectionMonetaryVote::where(
            'election_id',
            $election->election_id
        )
            ->where('votes_counted', true)
            ->selectRaw('position, candidate, SUM(amount_paid) as total_amount')
            ->groupBy('position', 'candidate')
            ->get()
            ->keyBy(function ($row) {
                return $row->position . '|' . $row->candidate;
            });
        ######################################################
        $costPerVote = (float) $election->cost_per_vote;

        $positionResults = collect();

        foreach ($positions as $position) {

            $candidateResults = $position->qualifiedCandidates;

            /*
    |--------------------------------------------------------------------------
    | Calculate vote count WITHOUT querying the database
    |--------------------------------------------------------------------------
    */

            $candidateResults = $candidateResults->map(function ($candidate) use (
                $election,
                $position,
                $standardVoteCounts,
                $monetaryVoteAmounts,
                $costPerVote
            ) {

                $key = $position->id . '|' . $candidate->application_id;

                if ($election->voting_method === 'monetary') {

                    $totalAmount =
                        (float) ($monetaryVoteAmounts->get($key)->total_amount ?? 0);

                    $candidate->voteCount = $costPerVote > 0
                        ? (int) floor($totalAmount / $costPerVote)
                        : 0;
                } else {

                    $candidate->voteCount =
                        (int) ($standardVoteCounts->get($key)->votes ?? 0);
                }

                return $candidate;
            });

            /*
    |--------------------------------------------------------------------------
    | Sort candidates
    |--------------------------------------------------------------------------
    */

            $candidateResults = $candidateResults
                ->sortByDesc('voteCount')
                ->values();

            /*
    |--------------------------------------------------------------------------
    | Total votes
    |--------------------------------------------------------------------------
    */

            $totalVotes = $candidateResults->sum('voteCount');

            /*
    |--------------------------------------------------------------------------
    | Highest vote
    |--------------------------------------------------------------------------
    */

            $highestVotes = $candidateResults->max('voteCount') ?? 0;

            /*
    |--------------------------------------------------------------------------
    | Find candidates sharing highest vote
    |--------------------------------------------------------------------------
    */

            $leadingCandidates = $candidateResults
                ->where('voteCount', $highestVotes)
                ->values();

            /*
    |--------------------------------------------------------------------------
    | Tie
    |--------------------------------------------------------------------------
    */

            $isTie =
                $highestVotes > 0 &&
                $leadingCandidates->count() > 1;

            /*
    |--------------------------------------------------------------------------
    | Winner
    |--------------------------------------------------------------------------
    */

            $winner =
                (!$isTie && $highestVotes > 0)
                ? $leadingCandidates->first()
                : null;

            /*
    |--------------------------------------------------------------------------
    | Store result for this position
    |--------------------------------------------------------------------------
    */

            $positionResults->push([
                'position' => $position,
                'candidateResults' => $candidateResults,
                'totalVotes' => $totalVotes,
                'highestVotes' => $highestVotes,
                'isTie' => $isTie,
                'winner' => $winner,
                'tiedCandidates' => $isTie
                    ? $leadingCandidates
                    : collect(),
            ]);
        }
        // result analysis end

        $data = [
            'election' => $election,
            'faculties' => $faculties,
            'departments' => $departments,
            'basicSystemInfo' => $basicSystemInfo,
            'onlyFaculty' => $onlyFaculty,
            'onlyDepartment' => $onlyDepartment,

            'totalUniqueVoters' => $totalUniqueVoters,
            'totalQualifiedVoters' => $totalQualifiedVoters,
            'participationPercentage' => $participationPercentage,


            'positionResults' => $positionResults,
        ];

        $html = view('printFiles.elections.electionResults', compact('data'))->render();

        $mpdf->WriteHTML($html); // AddPage() not needed — WriteHTML handles it

        $fileName = 'Election Results :' . $election->title . '_' . $electionSession . '_' . date('Y-m-d_H-i-s') . '.pdf';

        while (ob_get_level()) ob_end_clean();

        $mpdf->Output($fileName, 'I'); // 'I' for inline, 'D' for download
        exit;
    }
}
