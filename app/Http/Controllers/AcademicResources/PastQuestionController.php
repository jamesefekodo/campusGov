<?php

namespace App\Http\Controllers\AcademicResources;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicResources\PastQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PastQuestionController extends Controller
{
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
        /*
    if ($user->tenant_id) {
        $pastQuestionsQuery->where('tenant_id', $user->tenant_id);
    }
    */

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
}
