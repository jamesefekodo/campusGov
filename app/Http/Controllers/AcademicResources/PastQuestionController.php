<?php

namespace App\Http\Controllers\AcademicResources;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicResources\PastQuestion;
use App\Models\AcademicResources\PastQuestionContribution;
use App\Models\AcademicResources\PastQuestionContributionLike;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;
use Throwable;


class PastQuestionController extends Controller
{
    public function storeContribution(Request $request, PastQuestion $pastQuestion)
    {
        $user = Auth::user();

        abort_unless($user, 401);

        $request->validate([
            'content' => [
                'required',
                'string',
                'max:30000',
            ],
        ]);

        $rawContent = $request->input('content');

        $plainText = trim(
            strip_tags(
                html_entity_decode($rawContent ?? '')
            )
        );

        if ($plainText === '') {
            return response()->json([
                'message' => 'Please enter a contribution.',
            ], 422);
        }

        /*
         * The private function is called here.
         */
        $cleanContent = $this->cleanContributionHtml($rawContent);

        $contribution = PastQuestionContribution::create([
            'uuid' => (string) Str::uuid(),
            'past_question_id' => $pastQuestion->id,
            'username' => $user->username,
            'content' => $cleanContent,
        ]);

        $contribution->load([
            'creator.memberPersonalProfile',
        ]);

        return response()->json([
            'message' => 'Contribution added successfully.',
            'contribution' => $this->formatContribution(
                $contribution,
                $user
            ),
            'contributions_count' => $pastQuestion
                ->contributions()
                ->count(),
        ], 201);
    }

    public function updateContribution(Request $request, PastQuestionContribution $contribution)
    {
        $user = Auth::user();

        abort_unless($user, 401);

        abort_unless(
            $contribution->username === $user->username,
            403
        );

        $request->validate([
            'content' => [
                'required',
                'string',
                'max:30000',
            ],
        ]);

        $rawContent = $request->input('content');

        $plainText = trim(
            strip_tags(
                html_entity_decode($rawContent ?? '')
            )
        );

        if ($plainText === '') {
            return response()->json([
                'message' => 'Please enter a contribution.',
            ], 422);
        }

        /*
         * Use the same private function during update.
         */
        $contribution->update([
            'content' => $this->cleanContributionHtml($rawContent),
            'edited_at' => now(),
        ]);

        $contribution->load([
            'creator.memberPersonalProfile',
        ]);

        return response()->json([
            'message' => 'Contribution updated successfully.',
            'contribution' => $this->formatContribution(
                $contribution,
                $user
            ),
        ]);
    }

    public function destroyContribution(
        PastQuestionContribution $contribution
    ) {
        $user = Auth::user();

        abort_unless($user, 401);

        abort_unless(
            $contribution->username === $user->username,
            403
        );

        $pastQuestion = $contribution->pastQuestion;

        $contribution->delete();

        return response()->json([
            'message' => 'Contribution deleted successfully.',
            'contributions_count' => $pastQuestion
                ->contributions()
                ->count(),
        ]);
    }

    public function toggleLikeContribution(
        PastQuestionContribution $contribution
    ) {
        $user = Auth::user();

        abort_unless($user, 401);

        $like = PastQuestionContributionLike::query()
            ->where('contribution_id', $contribution->id)
            ->where('username', $user->username)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            PastQuestionContributionLike::create([
                'contribution_id' => $contribution->id,
                'username' => $user->username,
            ]);

            $liked = true;
        }

        return response()->json([
            'message' => $liked
                ? 'Contribution liked.'
                : 'Like removed.',

            'liked' => $liked,

            'likes_count' => $contribution
                ->likes()
                ->count(),
        ]);
    }


    private function cleanContributionHtml(?string $html): string
    {
        try {
            return Purifier::clean($html ?? '');
        } catch (\Throwable $exception) {
            Log::error('Purifier error', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            throw $exception;
        }
    }

    private function formatContribution(
        PastQuestionContribution $contribution,
        $user
    ): array {
        $liked = false;

        if ($user) {
            $liked = $contribution
                ->likes()
                ->where('username', $user->username)
                ->exists();
        }

        return [
            'uuid' => $contribution->uuid,
            'content' => $contribution->content,
            'username' => $contribution->username,
            'creator_name' => $contribution->creator_name,

            'created_at' => $contribution->created_at
                ? $contribution->created_at->diffForHumans()
                : '',

            'edited_at' => $contribution->edited_at
                ? $contribution->edited_at->diffForHumans()
                : null,

            'edited' => (bool) $contribution->edited_at,

            'likes_count' => $contribution
                ->likes()
                ->count(),

            'liked' => $liked,

            'is_owner' => $user &&
                $contribution->username === $user->username,
        ];
    }



    public function pastQuestionsHome(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user && $user->hasPermission('past_questions.read'),
            403
        );

        $selectedFaculty = $request->input('faculty');
        $selectedDepartment = $request->input('department');
        $selectedProgram = $request->input('program');
        $selectedProgramType = $request->input('program_type');
        $selectedSemester = $request->input('semester');
        $selectedAcademicYear = $request->input('academic_year');
        $selectedQuestionType = $request->input('question_type');
        $searchKey = trim((string) $request->input('searchKey'));

        $isFiltering = $request->filled('faculty')
            || $request->filled('department')
            || $request->filled('program')
            || $request->filled('program_type')
            || $request->filled('semester')
            || $request->filled('academic_year')
            || $request->filled('question_type')
            || $request->filled('searchKey');

        $pastQuestionsQuery = PastQuestion::query()
            ->where('approval_status', 'approved');

        /*
     | If your application uses tenant scoping and past_questions
     | should be limited to the signed-in user's tenant, uncomment
     | and adapt this based on your User model.
     */

    if ($user->tenant_id) {
        $pastQuestionsQuery->where('tenant_id', $user->tenant_id);
    }
    

        if ($selectedFaculty) {
            $pastQuestionsQuery->where('faculty', $selectedFaculty);
        }

        if ($selectedDepartment) {
            $pastQuestionsQuery->where('department', $selectedDepartment);
        }

        if ($selectedProgram) {
            $pastQuestionsQuery->where('program', $selectedProgram);
        }

        if ($selectedProgramType) {
            $pastQuestionsQuery->where('program_type', $selectedProgramType);
        }

        if ($selectedSemester) {
            $pastQuestionsQuery->where('semester', $selectedSemester);
        }

        if ($selectedAcademicYear) {
            $pastQuestionsQuery->where('academic_year', $selectedAcademicYear);
        }

        if ($selectedQuestionType !== null && $selectedQuestionType !== '') {
            $pastQuestionsQuery->where(
                'is_exam',
                $selectedQuestionType === 'exam'
            );
        }

        if ($searchKey !== '') {
            $pastQuestionsQuery->where(function ($query) use ($searchKey) {
                $query->where('course_code', 'like', '%' . $searchKey . '%')
                    ->orWhere('course_title', 'like', '%' . $searchKey . '%')
                    ->orWhere('content', 'like', '%' . $searchKey . '%');
            });
        }

        $pastQuestions = $pastQuestionsQuery
            ->with([
                'programRelation',
                'programTypeRelation',
                'facultyRelation',
                'departmentRelation',
            ])
            ->latest('academic_year')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $activeFaculties = Faculty::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $activePrograms = Program::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $academicYears = PastQuestion::query()
            ->where('approval_status', 'approved')
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');

        $data = [
            'pastQuestions' => $pastQuestions,
            'totalPastQuestions' => $pastQuestions->total(),
            'requestType' => $isFiltering ? 'filter' : 'all',

            'searchKey' => $searchKey,

            'selectedFaculty' => $selectedFaculty,
            'selectedDepartment' => $selectedDepartment,
            'selectedProgram' => $selectedProgram,
            'selectedProgramType' => $selectedProgramType,
            'selectedSemester' => $selectedSemester,
            'selectedAcademicYear' => $selectedAcademicYear,
            'selectedQuestionType' => $selectedQuestionType,

            'activeFaculties' => $activeFaculties,
            'activePrograms' => $activePrograms,
            'academicYears' => $academicYears,

            'pageTitle' => $isFiltering
                ? 'Filtered Past Questions'
                : 'Past Questions',
        ];

        return view('users.admin.academicResources.pastQuestions.pastQuestionsHome', compact('data'));
    }
    public function createPastQuestion()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user && $user->hasPermission('past_questions.create'),
            403
        );

        $activeProgram = Program::where('is_active', true)->orderBy('name')->get();
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();

        $data = [
            'activeProgram' => $activeProgram,
            'activeFaculties' => $activeFaculties,
        ];

        return view('users.admin.academicResources.pastQuestions.newPastQuestion', compact('data'));
    }
    public function addNewPastQuestionSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user && $user->hasPermission('past_questions.create'),
            403
        );
        $request->validate([
            'program' => 'required|string|exists:programs,slug',
            'program_type' => 'required|string|exists:program_types,slug',
            'faculty' => 'required|string|exists:faculties,slug',
            'department' => 'required|string|exists:departments,slug',
            'academic_year' => 'required|numeric',
            'semester' => 'required',
            'is_exam' => 'required',
            'course_code' => 'required',
            'course_title' => 'required',
            'content' => [
                'nullable',
                'string',
                'required_without:image_1',
            ],

            'image_1' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'required_without:content',
            ],

            'image_2' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_3' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_4' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_5' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_6' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        do {
            $questionSLug = 'pqst-' . now()->format('Ym') . '-' . Str::lower(Str::random(10));
        } while (PastQuestion::where('slug', $questionSLug)->exists());

        $pastQuestion = new PastQuestion();

        $pastQuestion->slug = $questionSLug;


        $pastQuestion->program = $request->input('program') ?? null;
        $pastQuestion->program_type = $request->input('program_type') ?? null;
        $pastQuestion->faculty = $request->input('faculty') ?? null;
        $pastQuestion->department = $request->input('department') ?? null;

        $pastQuestion->semester = $request->input('semester');
        $pastQuestion->course_code = $request->input('course_code');
        $pastQuestion->course_title = $request->input('course_title');

        $pastQuestion->academic_year = $request->input('academic_year');
        $pastQuestion->is_exam = $request->boolean('is_exam');
        $pastQuestion->content = $request->input('content');

        $pastQuestion->created_by = $user->username;
        $pastQuestion->approval_status = 'pending';


        $uploadPath = public_path(
            'assets/tenant_' . tenant()->id . '/uploads/academicResources/pastQuestions'
        );

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        for ($index = 1; $index <= 6; $index++) {
            $fieldName = 'image_' . $index;

            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);

                $extension = strtolower(
                    $file->getClientOriginalExtension()
                );

                $fileName = $pastQuestion->slug . '_page_' . $index . '.' . $extension;

                $file->move($uploadPath, $fileName);

                $pastQuestion->{$fieldName} =
                    'assets/tenant_' . tenant()->id .
                    '/uploads/academicResources/pastQuestions/' .
                    $fileName;
            }
        }

        $pastQuestion->save();

        return redirect()
            ->route('pastQuestionsHome')
            ->with('success', 'Past question submitted successfully.');
    }

    public function pastQuestionsApproval()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user && $user->hasPermission('past_questions.update'),
            403
        );

        $pastQuestions = PastQuestion::where('approval_status', 'pending')->orderBy('updated_at')->paginate(50)->withQueryString();

        $data = [
            'pastQuestions' => $pastQuestions,
        ];
        return view('users.admin.academicResources.pastQuestions.pastQuestionsPendingApproval', compact('data'));
    }
    public function viewPastQuestion($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user && $user->hasPermission('past_questions.read'),
            403
        );

        $pastQuestion = PastQuestion::with([
            'creator.memberPersonalProfile',
            'contributions.creator.memberPersonalProfile',
        ])
            ->withCount('contributions')
            ->where('uuid', $uuid)->first();
        if (!$pastQuestion) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'pastQuestion' => $pastQuestion,
        ];

        return view('users.admin.academicResources.pastQuestions.viewPastQuestions', compact('data'));
    }
    public function reviewPastQuestionSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user && $user->hasPermission('past_questions.update'),
            403
        );

        $pastQuestion = PastQuestion::where('uuid', $uuid)->first();
        if (!$pastQuestion) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $request->validate([
            'approval_status' => 'required',
        ]);


        $pastQuestion->update([
            'approval_status' => $request->approval_status,
            'approved_by' => Auth::user()->username,
            'last_approval_update' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function editPastQuestion($uuid)
    {
        $pastQuestion = PastQuestion::where('uuid', $uuid)->first();
        if (!$pastQuestion) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            (
                $user &&
                $user->hasPermission('past_questions.update')
            ) || (
                $pastQuestion->created_by === Auth::user()->username
            ),
            403
        );

        $activeProgram = Program::where('is_active', true)->orderBy('name')->get();
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();


        $data = [
            'pastQuestion' => $pastQuestion,
            'activeProgram' => $activeProgram,
            'activeFaculties' => $activeFaculties,
        ];
        return view('users.admin.academicResources.pastQuestions.editPastQuestion', compact('data'));
    }
    public function updatePastQuestionSubmit($uuid, Request $request)
    {
        $pastQuestion = PastQuestion::where('uuid', $uuid)->first();
        if (!$pastQuestion) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            (
                $user &&
                $user->hasPermission('past_questions.update')
            ) || (
                $pastQuestion->created_by === Auth::user()->username
            ),
            403
        );


        $request->validate([
            'program' => 'required|string|exists:programs,slug',
            'program_type' => 'required|string|exists:program_types,slug',
            'faculty' => 'required|string|exists:faculties,slug',
            'department' => 'required|string|exists:departments,slug',
            'academic_year' => 'required|numeric',
            'semester' => 'required',
            'is_exam' => 'required',
            'course_code' => 'required',
            'course_title' => 'required',
            'content' => [
                'nullable',
                'string',
                'required_without:image_1',
            ],

            'image_1' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'required_without:content',
            ],

            'image_2' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_3' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_4' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_5' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_6' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);


        $pastQuestion->program = $request->input('program') ?? null;
        $pastQuestion->program_type = $request->input('program_type') ?? null;
        $pastQuestion->faculty = $request->input('faculty') ?? null;
        $pastQuestion->department = $request->input('department') ?? null;

        $pastQuestion->semester = $request->input('semester');
        $pastQuestion->course_code = $request->input('course_code');
        $pastQuestion->course_title = $request->input('course_title');

        $pastQuestion->academic_year = $request->input('academic_year');
        $pastQuestion->is_exam = $request->boolean('is_exam');
        $pastQuestion->content = $request->input('content');

        $pastQuestion->last_update_by = $user->username;
        $pastQuestion->last_update = Carbon::now();


        $uploadPath = public_path(
            'assets/tenant_' . tenant()->id .
                '/uploads/academicResources/pastQuestions'
        );

        $databasePath =
            'assets/tenant_' . tenant()->id .
            '/uploads/academicResources/pastQuestions';

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        for ($index = 1; $index <= 6; $index++) {
            $fieldName = 'image_' . $index;
            $removeField = 'remove_image_' . $index;

            $oldDatabasePath = $pastQuestion->{$fieldName};

            /*
     * CASE 1:
     * A new image was uploaded.
     *
     * Delete the old image first, then save the replacement.
     */
            if ($request->hasFile($fieldName)) {
                if (!empty($oldDatabasePath)) {
                    $oldFullPath = public_path(
                        ltrim($oldDatabasePath, '/')
                    );

                    if (File::exists($oldFullPath)) {
                        File::delete($oldFullPath);
                    }
                }

                $file = $request->file($fieldName);

                $extension = strtolower(
                    $file->getClientOriginalExtension()
                );

                $fileName = $pastQuestion->slug .
                    '_page_' . $index .
                    '.' . $extension;

                $file->move($uploadPath, $fileName);

                $pastQuestion->{$fieldName} =
                    $databasePath . '/' . $fileName;

                continue;
            }

            /*
     * CASE 2:
     * The user clicked Remove and did not upload
     * a replacement image.
     */
            if ($request->boolean($removeField)) {
                if (!empty($oldDatabasePath)) {
                    $oldFullPath = public_path(
                        ltrim($oldDatabasePath, '/')
                    );

                    if (File::exists($oldFullPath)) {
                        File::delete($oldFullPath);
                    }
                }

                $pastQuestion->{$fieldName} = null;

                continue;
            }

            /*
     * CASE 3:
     * No new image and no remove request.
     * Keep the old database value unchanged.
     */
        }


        $pastQuestion->save();
        return redirect()->route('viewPastQuestion', ['uuid' => $pastQuestion->uuid])->with('success', 'Done!');
    }

    public function deletePastQuestion($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user && $user->hasPermission('past_questions.delete'),
            403
        );
        $pastQuestion = PastQuestion::where('uuid', $uuid)->first();
        if (!$pastQuestion) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        try {
            /*
         * Save the database paths before deleting
         * the Past Question record.
         */
            $imagePaths = [];

            for ($index = 1; $index <= 6; $index++) {
                $imageColumn = 'image_' . $index;

                if (!empty($pastQuestion->{$imageColumn})) {
                    $imagePaths[] = $pastQuestion->{$imageColumn};
                }
            }

            DB::transaction(function () use ($pastQuestion) {
                /*
             * Find all contributions belonging to
             * this Past Question.
             */
                $contributionIds = PastQuestionContribution::query()
                    ->where(
                        'past_question_id',
                        $pastQuestion->id
                    )
                    ->pluck('id');

                /*
             * Delete likes first.
             */
                if ($contributionIds->isNotEmpty()) {
                    PastQuestionContributionLike::query()
                        ->whereIn(
                            'contribution_id',
                            $contributionIds
                        )
                        ->delete();
                }

                /*
             * Delete contributions.
             */
                PastQuestionContribution::query()
                    ->where(
                        'past_question_id',
                        $pastQuestion->id
                    )
                    ->delete();

                /*
             * Delete the Past Question.
             */
                $pastQuestion->delete();
            });

            /*
         * Delete the physical image files.
         */
            foreach ($imagePaths as $imagePath) {
                $fullImagePath = public_path(
                    ltrim($imagePath, '/')
                );

                if (File::exists($fullImagePath)) {
                    File::delete($fullImagePath);
                }
            }

            return redirect()
                ->route('pastQuestionsHome')
                ->with(
                    'success',
                    'Past question, contributions, likes and images deleted successfully.'
                );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to delete the past question.'
                );
        }
    }
    public function memberPastQuestions(Request $request){
       $selectedFaculty = $request->input('faculty');
        $selectedDepartment = $request->input('department');
        $selectedProgram = $request->input('program');
        $selectedProgramType = $request->input('program_type');
        $selectedSemester = $request->input('semester');
        $selectedAcademicYear = $request->input('academic_year');
        $selectedQuestionType = $request->input('question_type');
        $searchKey = trim((string) $request->input('searchKey'));

        $isFiltering = $request->filled('faculty')
            || $request->filled('department')
            || $request->filled('program')
            || $request->filled('program_type')
            || $request->filled('semester')
            || $request->filled('academic_year')
            || $request->filled('question_type')
            || $request->filled('searchKey');

        $pastQuestionsQuery = PastQuestion::query()
            ->where('approval_status', 'approved');

        /*
     | If your application uses tenant scoping and past_questions
     | should be limited to the signed-in user's tenant, uncomment
     | and adapt this based on your User model.
     */
    $user = Auth::user();
    if ($user->tenant_id) {
        $pastQuestionsQuery->where('tenant_id', $user->tenant_id);
    }

        if ($selectedFaculty) {
            $pastQuestionsQuery->where('faculty', $selectedFaculty);
        }

        if ($selectedDepartment) {
            $pastQuestionsQuery->where('department', $selectedDepartment);
        }

        if ($selectedProgram) {
            $pastQuestionsQuery->where('program', $selectedProgram);
        }

        if ($selectedProgramType) {
            $pastQuestionsQuery->where('program_type', $selectedProgramType);
        }

        if ($selectedSemester) {
            $pastQuestionsQuery->where('semester', $selectedSemester);
        }

        if ($selectedAcademicYear) {
            $pastQuestionsQuery->where('academic_year', $selectedAcademicYear);
        }

        if ($selectedQuestionType !== null && $selectedQuestionType !== '') {
            $pastQuestionsQuery->where(
                'is_exam',
                $selectedQuestionType === 'exam'
            );
        }

        if ($searchKey !== '') {
            $pastQuestionsQuery->where(function ($query) use ($searchKey) {
                $query->where('course_code', 'like', '%' . $searchKey . '%')
                    ->orWhere('course_title', 'like', '%' . $searchKey . '%')
                    ->orWhere('content', 'like', '%' . $searchKey . '%');
            });
        }

        $pastQuestions = $pastQuestionsQuery
            ->with([
                'programRelation',
                'programTypeRelation',
                'facultyRelation',
                'departmentRelation',
            ])
            ->latest('academic_year')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $activeFaculties = Faculty::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $activePrograms = Program::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $academicYears = PastQuestion::query()
            ->where('approval_status', 'approved')
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');

        $data = [
            'pastQuestions' => $pastQuestions,
            'totalPastQuestions' => $pastQuestions->total(),
            'requestType' => $isFiltering ? 'filter' : 'all',

            'searchKey' => $searchKey,

            'selectedFaculty' => $selectedFaculty,
            'selectedDepartment' => $selectedDepartment,
            'selectedProgram' => $selectedProgram,
            'selectedProgramType' => $selectedProgramType,
            'selectedSemester' => $selectedSemester,
            'selectedAcademicYear' => $selectedAcademicYear,
            'selectedQuestionType' => $selectedQuestionType,

            'activeFaculties' => $activeFaculties,
            'activePrograms' => $activePrograms,
            'academicYears' => $academicYears,

            'pageTitle' => $isFiltering
                ? 'Filtered Past Questions'
                : 'Past Questions',
        ];
        return view('users.member.academicResources.pastQuestions.pastQuestions', compact('data'));
    }
    public function memberViewPastQuestion($uuid){
        $pastQuestion = PastQuestion::where('uuid', $uuid)->where('approval_status', 'approved')->first();
        if(!$pastQuestion){
            return redirect()->back()->with('error', 'Recods not found!');
        }
        $data = [
            'pastQuestion' => $pastQuestion,
        ];

        return view('users.member.academicResources.pastQuestions.viewPastQuestion', compact('data'));
    }
}
