<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\HeadOfDepartment;
use App\Models\AcademicConfig\HeadOfFaculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramDurationOverride;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Config\BasicSystemSetting;
use App\Models\Members\MemberAcademicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class AcademicConfigController extends Controller
{
    public function fetchProgramTypes(Request $request)
    {
        $programTypes = ProgramType::where('program_slug', $request->program_slug)
            ->where('is_active', true)
            ->orderBy('type_name')
            ->get([
                'slug',
                'type_name'
            ]);

        return response()->json([
            'programTypes' => $programTypes
        ]);
    }
    public function fetchDepartments(Request $request)
    {
        $departments = Department::where('faculty', $request->faculty_slug)
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'slug',
                'name'
            ]);

        return response()->json([
            'departments' => $departments
        ]);
    }

    public function faculties(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $faculties = '';
        $totalFaculties = 0;
        $operationalLevel = BasicSystemSetting::value('operational_level') ?? 'departnemt';

        if (Route::currentRouteName() == 'facultiesSearch') {
            $searchKey = $request->input('searchKey');
            $faculties = Faculty::where('name', 'like', "%$searchKey%")
                ->orWhere('acronym', 'like', "%$searchKey%")
                ->orWhere('description', 'like', "%$searchKey%")
                ->orderBy('name')
                ->paginate(20)->withQueryString();


            $totalFaculties = $faculties->total();
            $requestType = 'search';
            $pageTitle = 'Search Results:: ' . $searchKey;
        } else {
            $faculties = Faculty::orderBy('name')->paginate(20);
            $totalFaculties = Faculty::count();
            $requestType = 'all';
            $pageTitle = 'Faculties';
        }
        $data = [
            'faculties' => $faculties,
            'totalFaculties' => $totalFaculties,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'operationalLevel' => $operationalLevel,
        ];


        return view('users.admin.academicConfig.faculties.faculties', compact('data'));
    }
    public function addFacultySubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.create'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'acronym' => 'nullable|string',
            'description' => 'required',
            'administrative_head_title' => 'nullable|string',
        ]);


        do {
            $facultySlug = 'faculty-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (Faculty::where('slug', $facultySlug)->exists());


        $faculty = new Faculty();
        $faculty->name = trim($request->name);
        $faculty->slug = $facultySlug;
        $faculty->acronym = trim($request->acronym);
        $faculty->description = $request->description;
        $faculty->administrative_head_title = trim($request->administrative_head_title);
        $faculty->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function viewFaculty($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.read'),
            403
        );

        $faculty = Faculty::where('uuid', $uuid)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $studentMembers = MemberAcademicInfo::where('faculty', $faculty->slug)->where('academic_status', 'student')->count();
        $alumniMembers = MemberAcademicInfo::where('faculty', $faculty->slug)->where('academic_status', 'alumni')->count();


        $data = [
            'faculty' => $faculty,
            'studentMembers' => $studentMembers,
            'alumniMembers' => $alumniMembers,
        ];

        return view('users.admin.academicConfig.faculties.viewFaculties', compact('data'));
    }
    public function updateFacultySubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.update'),
            403
        );

        $faculty = Faculty::where('uuid', $uuid)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $request->validate([
            'name' => 'required|string',
            'acronym' => 'nullable|string',
            'description' => 'required',
            'administrative_head_title' => 'nullable|string',
            'is_active' => 'required',
        ]);

        $faculty->update([
            'name' => $request->name,
            'acronym' => $request->acronym,
            'description' => $request->description,
            'administrative_head_title' => $request->administrative_head_title,
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
    public function addFacultyDeanSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.update'),
            403
        );
        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);
        $faculty = Faculty::where('uuid', $request->faculty)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }


        $head = new HeadOfFaculty();
        $head->faculty = $faculty->slug;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->profile = $request->profile;
        if (!$faculty->facultyHead) {
            $head->is_active = true;

            // process the image
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $logoExtension = $image->getClientOriginalExtension();
                $uploadPath = public_path('assets/uploads/faculty/head/photo');
                $baseName = $faculty->slug;
                $imageName = $baseName . '.' . $logoExtension;
                $counter = 1;

                while (File::exists($uploadPath . '/' . $imageName)) {
                    $imageName = $baseName . '-' . $counter . '.' . $logoExtension;
                    $counter++;
                }

                $image->move($uploadPath, $imageName);
                $head->photo = $imageName;
            }
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editFacultyDeanSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.update'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $faculty = Faculty::where('uuid', $request->faculty)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $head = $faculty->facultyHead;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->profile = $request->profile;

        // Process the image
        if ($request->hasFile('photo')) {

            // Delete old if exists
            $filePath = public_path('assets/uploads/faculty/head/photo/' . $head->photo);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $uploadPath = public_path('assets/uploads/faculty/head/photo');
            $baseName = $faculty->slug;
            $newImageName = $baseName . '.' . $newImageExtension;
            $counter = 1;
            while (File::exists($uploadPath . '/' . $newImageName)) {
                $newImageName = $baseName . '-' . $counter . '.' . $newImageExtension;
                $counter++;
            }
            $newImage->move($uploadPath, $newImageName);
            $head->photo = $newImageName;
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editEndFacultyDeanTenureSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.update'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'required',
            'tenure_end_on' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $faculty = Faculty::where('uuid', $request->faculty)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $head = $faculty->facultyHead;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->tenure_end_on = $request->tenure_end_on;
        $head->profile = $request->profile;
        $head->is_active = false;

        // Process the image
        if ($request->hasFile('photo')) {
            $filePath = public_path('assets/uploads/faculty/head/photo/' . $head->photo);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();

            $uploadPath = public_path('assets/uploads/faculty/head/photo');
            $baseName = $faculty->slug;
            $newImageName = $baseName . '.' . $newImageExtension;

            $counter = 1;

            while (file_exists($uploadPath . '/' . $newImageName)) {
                $newImageName = $baseName . '-' . $counter . '.' . $newImageExtension;
                $counter++;
            }
            $newImage->move($uploadPath, $newImageName);
            $head->photo = $newImageName;
        }

        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function pastFacultyHeads($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.read'),
            403
        );

        $faculty = Faculty::where('uuid', $uuid)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $pastHeads = '';
        $totalPastHeads = 0;

        if (Route::currentRouteName() == 'pastFacultyHeadsSearch') {
            $searchKey = $request->input('searchKey');
            $pastHeads = HeadOfFaculty::where('faculty', $faculty->slug)->where('name', 'like', "%$searchKey%")
                ->orWhere('qualifications', 'like', "%$searchKey%")
                ->orWhere('phone', 'like', "%$searchKey%")
                ->orWhere('email', 'like', "%$searchKey%")
                ->orWhere('profile', 'like', "%$searchKey%")
                ->orderBy('name')
                ->paginate(20)->withQueryString();


            $totalPastHeads = $pastHeads->total();
            $requestType = 'search';
            $pageTitle = 'Search Results:: ' . $searchKey;
        } else {
            $pastHeads = HeadOfFaculty::where('faculty', $faculty->slug)
                ->orderBy('tenure_end_on', 'desc')
                ->paginate(20);
            $totalPastHeads = $faculty->pastFacultyHeads->count();
            $requestType = 'all';
            $pageTitle = 'Past Faculty Heads';
        }
        $data = [
            'faculty' => $faculty,
            'pastHeads' => $pastHeads,
            'totalPastHeads' => $totalPastHeads,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.academicConfig.faculties.pastFacultyHeads', compact('data'));
    }
    public function addNewPastFacultyHead(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.create'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'required',
            'tenure_end_on' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'required',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $faculty = Faculty::where('uuid', $request->faculty)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $head = new HeadOfFaculty();
        $head->faculty = $faculty->slug;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->profile = $request->profile;
        $head->is_active = false;
        $head->assumed_office_on = trim($request->assumed_office_on);
        $head->tenure_end_on = trim($request->tenure_end_on);

        if ($request->hasFile('photo')) {
            $filePath = public_path('assets/uploads/faculty/head/photo/' . $head->photo);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();

            $uploadPath = public_path('assets/uploads/faculty/head/photo');
            $baseName = $faculty->slug;
            $newImageName = $baseName . '.' . $newImageExtension;

            $counter = 1;

            while (file_exists($uploadPath . '/' . $newImageName)) {
                $newImageName = $baseName . '-' . $counter . '.' . $newImageExtension;
                $counter++;
            }
            $newImage->move($uploadPath, $newImageName);
            $head->photo = $newImageName;
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function updatePastFacultyHeawdProfileSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.update'),
            403
        );


        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'required',
            'tenure_end_on' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $head = HeadOfFaculty::where('uuid', $uuid)->first();
        if (!$head) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $faculty = $head->hisFaculty;
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->tenure_end_on = $request->tenure_end_on;
        $head->profile = $request->profile;
        // Process the image
        if ($request->hasFile('photo')) {
            $filePath = 'assets/uploads/faculty/head/photo/' . $head->photo;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();
            // $newImageName = $head->slug . '.' . $newImageExtension;
            // $newImage->move(public_path('assets/uploads/faculty/head/photo'), $newImageName);
            // $head->photo = $newImageName;
            $uploadPath = public_path('assets/uploads/faculty/head/photo');
            $baseName = $faculty->slug;
            $newImageName = $baseName . '.' . $newImageExtension;

            $counter = 1;

            while (file_exists($uploadPath . '/' . $newImageName)) {
                $newImageName = $baseName . '-' . $counter . '.' . $newImageExtension;
                $counter++;
            }

            $newImage->move($uploadPath, $newImageName);

            $head->photo = $newImageName;
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function deletePastFacultyHead($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.delete'),
            403
        );

        $head = HeadOfFaculty::where('uuid', $uuid)->first();
        if (!$head) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $filePath = public_path('assets/uploads/faculty/head/photo/' . $head->photo);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $head->delete();
        return redirect()->back()->with('success', 'Done!');
    }
    public function departments(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $departments = '';
        $totalDepartments = 0;
        $operationalLevel = BasicSystemSetting::value('operational_level') ?? 'departnemt';
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();

        if (Route::currentRouteName() == 'departmentsSearch') {
            $searchKey = $request->input('searchKey');
            $departments = Department::with('theFaculty')
                ->where(function ($query) use ($searchKey) {
                    $query->where('name', 'like', "%{$searchKey}%")
                        ->orWhere('description', 'like', "%{$searchKey}%")
                        ->orWhereHas('theFaculty', function ($faculty) use ($searchKey) {
                            $faculty->where('name', 'like', "%{$searchKey}%")
                                ->orWhere('acronym', 'like', "%{$searchKey}%")
                                ->orWhere('administrative_head_title', 'like', "%{$searchKey}%")
                                ->orWhere('description', 'like', "%{$searchKey}%");
                        });
                })
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString();

            $totalDepartments = $departments->total();
            $requestType = 'search';
            $pageTitle = 'Search Results:: ' . $searchKey;
        } else {
            $departments = Department::orderBy('name')->paginate(20);
            $totalDepartments = Department::count();
            $requestType = 'all';
            $pageTitle = 'Departments';
        }

        $data = [
            'departments' => $departments,
            'totalDepartments' => $totalDepartments,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'operationalLevel' => $operationalLevel,
            'activeFaculties' => $activeFaculties,
        ];


        return view('users.admin.academicConfig.departments.departments', compact('data'));
    }
    public function addNewDepartmentSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.create'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'acronym' => 'nullable|string',
            'description' => 'required',
            'administrative_head_title' => 'nullable|string',
            'faculty' => 'required|exists:faculties,slug'
        ]);


        do {
            $departmentSlug = 'department-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (Department::where('slug', $departmentSlug)->exists());


        $department = new Department();
        $department->slug = $departmentSlug;
        $department->faculty = $request->faculty;
        $department->name = trim($request->name);
        $department->description = $request->description;
        $department->administrative_head_title = trim($request->administrative_head_title);
        $department->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function viewDepartment($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.read'),
            403
        );

        $department = Department::where('uuid', $uuid)->first();
        if (!$department) {
            return redirect()->back()->with('error', 'Department record not found!');
        }

        $studentMembers = MemberAcademicInfo::where('department', $department->slug)->where('academic_status', 'student')->count();
        $alumniMembers = MemberAcademicInfo::where('department', $department->slug)->where('academic_status', 'alumni')->count();


        $data = [
            'department' => $department,
            'activeFaculties' => Faculty::where('is_active', true)->orderBy('name')->get(),
            'studentMembers' => $studentMembers,
            'alumniMembers' => $alumniMembers,
        ];
        return view('users.admin.academicConfig.departments.viewDepartment', compact('data'));
    }
    public function updateDepartmentSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.update'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'acronym' => 'nullable|string',
            'description' => 'required',
            'administrative_head_title' => 'nullable|string',
            'is_active' => 'required',
            'faculty' => 'required|exists:faculties,slug',
        ]);

        $department = Department::where('uuid', $uuid)->first();
        if (!$department) {
            return redirect()->back()->with('error', 'Department record not found!');
        }


        $department->faculty = $request->faculty;
        $department->name = trim($request->name);
        $department->description = $request->description;
        $department->administrative_head_title = trim($request->administrative_head_title);
        $department->is_active = $request->is_active;
        $department->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function addDepartmentHodSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.update'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);
        $department = Department::where('uuid', $request->department)->first();
        if (!$department) {
            return redirect()->back()->with('error', 'Department record not found!');
        }

        $head = new HeadOfDepartment();
        $head->department = $department->slug;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->profile = $request->profile;
        if (!$department->hod) {
            $head->is_active = true;

            // process the image
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $logoExtension = $image->getClientOriginalExtension();
                $uploadPath = public_path('assets/uploads/department/head/photo');
                $baseName = $department->slug;
                $imageName = $baseName . '.' . $logoExtension;
                $counter = 1;

                while (File::exists($uploadPath . '/' . $imageName)) {
                    $imageName = $baseName . '-' . $counter . '.' . $logoExtension;
                    $counter++;
                }

                $image->move($uploadPath, $imageName);
                $head->photo = $imageName;
            }
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editDepartmentHeadSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.update'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $department = Faculty::where('uuid', $request->department)->first();
        if (!$department) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $head = $department->hod;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->profile = $request->profile;

        // Process the image
        if ($request->hasFile('photo')) {

            // Delete old if exists
            $filePath = public_path('assets/uploads/department/head/photo/' . $head->photo);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $uploadPath = public_path('assets/uploads/faculty/head/photo');
            $baseName = $department->slug;
            $newImageName = $baseName . '.' . $newImageExtension;
            $counter = 1;
            while (File::exists($uploadPath . '/' . $newImageName)) {
                $newImageName = $baseName . '-' . $counter . '.' . $newImageExtension;
                $counter++;
            }
            $newImage->move($uploadPath, $newImageName);
            $head->photo = $newImageName;
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function endDepertmentHeadTenureSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.update'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'required',
            'tenure_end_on' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $department = Department::where('uuid', $request->department)->first();
        if (!$department) {
            return redirect()->back()->with('error', 'Department record not found!');
        }

        $head = $department->hod;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->tenure_end_on = $request->tenure_end_on;
        $head->profile = $request->profile;
        $head->is_active = false;

        // Process the image
        if ($request->hasFile('photo')) {
            $filePath = public_path('assets/uploads/department/head/photo/' . $head->photo);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();

            $uploadPath = public_path('assets/uploads/faculty/head/photo');
            $baseName = $department->slug;
            $newImageName = $baseName . '.' . $newImageExtension;

            $counter = 1;

            while (file_exists($uploadPath . '/' . $newImageName)) {
                $newImageName = $baseName . '-' . $counter . '.' . $newImageExtension;
                $counter++;
            }
            $newImage->move($uploadPath, $newImageName);
            $head->photo = $newImageName;
        }

        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function pastDepartmentHeads($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.read'),
            403
        );

        $department = Department::where('uuid', $uuid)->first();
        if (!$department) {
            return redirect()->back()->with('error', 'Department record not found!');
        }

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $pastHeads = '';
        $totalPastHeads = 0;

        if (Route::currentRouteName() == 'pastDepartmentHeadsSearch') {
            $searchKey = $request->input('searchKey');
            $pastHeads = HeadOfDepartment::where('department', $department->slug)->where('name', 'like', "%$searchKey%")
                ->orWhere('qualifications', 'like', "%$searchKey%")
                ->orWhere('phone', 'like', "%$searchKey%")
                ->orWhere('email', 'like', "%$searchKey%")
                ->orWhere('profile', 'like', "%$searchKey%")
                ->orderBy('name')
                ->paginate(20)->withQueryString();


            $totalPastHeads = $pastHeads->total();
            $requestType = 'search';
            $pageTitle = 'Search Results:: ' . $searchKey;
        } else {
            $pastHeads = HeadOfDepartment::where('department', $department->slug)
                ->orderBy('tenure_end_on', 'desc')
                ->paginate(20);
            $totalPastHeads = $department->pastHods->count();
            $requestType = 'all';
            $pageTitle = 'Past Department Heads';
        }
        $data = [
            'department' => $department,
            'pastHeads' => $pastHeads,
            'totalPastHeads' => $totalPastHeads,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.academicConfig.departments.pastDepartmentHeads', compact('data'));
    }
    public function updatePastDepartmentHeadProfileSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.update'),
            403
        );


        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'required',
            'tenure_end_on' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $head = HeadOfDepartment::where('uuid', $uuid)->first();
        if (!$head) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $department = $head->hisDepartment;
        if (!$department) {
            return redirect()->back()->with('error', 'Department record not found!');
        }
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->tenure_end_on = $request->tenure_end_on;
        $head->profile = $request->profile;
        // Process the image
        if ($request->hasFile('photo')) {
            $filePath = 'assets/uploads/department/head/photo/' . $head->photo;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();
            // $newImageName = $head->slug . '.' . $newImageExtension;
            // $newImage->move(public_path('assets/uploads/department/head/photo'), $newImageName);
            // $head->photo = $newImageName;
            $uploadPath = public_path('assets/uploads/department/head/photo');
            $baseName = $department->slug;
            $newImageName = $baseName . '.' . $newImageExtension;

            $counter = 1;

            while (file_exists($uploadPath . '/' . $newImageName)) {
                $newImageName = $baseName . '-' . $counter . '.' . $newImageExtension;
                $counter++;
            }

            $newImage->move($uploadPath, $newImageName);

            $head->photo = $newImageName;
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function deletePastDepartmentHead($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('department.delete'),
            403
        );

        $head = HeadOfDepartment::where('uuid', $uuid)->first();
        if (!$head) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $filePath = public_path('assets/uploads/department/head/photo/' . $head->photo);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $head->delete();
        return redirect()->back()->with('success', 'Done!');
    }
    public function addNewPastDepartmentHead(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.create'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'required',
            'tenure_end_on' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'required',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $department = Department::where('uuid', $request->department)->first();
        if (!$department) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $head = new HeadOfDepartment();
        $head->department = $department->slug;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->profile = $request->profile;
        $head->is_active = false;
        $head->assumed_office_on = trim($request->assumed_office_on);
        $head->tenure_end_on = trim($request->tenure_end_on);

        if ($request->hasFile('photo')) {
            $filePath = public_path('assets/uploads/department/head/photo/' . $head->photo);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();

            $uploadPath = public_path('assets/uploads/department/head/photo');
            $baseName = $department->slug;
            $newImageName = $baseName . '.' . $newImageExtension;

            $counter = 1;

            while (file_exists($uploadPath . '/' . $newImageName)) {
                $newImageName = $baseName . '-' . $counter . '.' . $newImageExtension;
                $counter++;
            }
            $newImage->move($uploadPath, $newImageName);
            $head->photo = $newImageName;
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }

    public function programs(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('program.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $programs = '';
        $totalPrograms = 0;

        if (Route::currentRouteName() == 'programsSearch') {
            $searchKey = $request->input('searchKey');
            $programs = Program::where('name', 'like', "%$searchKey%")
                ->orWhere('short_name', 'like', "%$searchKey%")
                ->orWhere('level_format', 'like', "%$searchKey%")
                ->orWhere('numbering', 'like', "%$searchKey%")
                ->orWhere('increment', 'like', "%$searchKey%")
                ->orWhere('duration', 'like', "%$searchKey%")
                ->orWhere('award', 'like', "%$searchKey%")
                ->orderBy('name')
                ->paginate(20)->withQueryString();


            $totalPrograms = $programs->total();
            $requestType = 'search';
            $pageTitle = 'Search Results:: ' . $searchKey;
        } else {
            $programs = Program::orderBy('name')->paginate(20);
            $totalPrograms = Program::count();
            $requestType = 'all';
            $pageTitle = 'Programs';
        }
        $data = [
            'programs' => $programs,
            'totalPrograms' => $totalPrograms,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.academicConfig.programs.programs', compact('data'));
    }
    public function addAcademicProgramSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('program.create'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'short_name' => 'required|string',
            'duration' => 'required|integer',
            'level_format' => 'required|string',
            'numbering' => 'required|string',
            'increment' => 'required|integer',
            'award' => 'required|string',
        ]);

        do {
            $programSlug = 'prog-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (Program::where('slug', $programSlug)->exists());


        $program = new Program();
        $program->slug = $programSlug;
        $program->name = trim($request->name);
        $program->short_name = trim($request->short_name);
        $program->level_format = trim($request->level_format);
        $program->numbering = trim($request->numbering);
        $program->increment = trim($request->increment);
        $program->duration = trim($request->duration);
        $program->award = trim($request->award);
        $program->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function viewProgram($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('program.read'),
            403
        );

        $program = Program::where('uuid', $uuid)->first();
        if (!$program) {
            return redirect()->back()->with('error', 'Program record not found!');
        }
        $activeDepartments = Department::where('is_active', true)->orderBy('name')->get();

        $studentMembers = MemberAcademicInfo::where('program', $program->slug)->where('academic_status', 'student')->count();
        $alumniMembers = MemberAcademicInfo::where('program', $program->slug)->where('academic_status', 'alumni')->count();


        $data = [
            'program' => $program,
            'activeDepartments' => $activeDepartments,
            'studentMembers' => $studentMembers,
            'alumniMembers' => $alumniMembers,
        ];

        return view('users.admin.academicConfig.programs.viewProgram', compact('data'));
    }
    public function updateAcademicProgramSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('program.update'),
            403
        );

        $program = Program::where('uuid', $uuid)->first();
        if (!$program) {
            return redirect()->back()->with('error', 'Program record not found!');
        }

        $request->validate([
            'name' => 'required|string',
            'short_name' => 'required|string',
            'duration' => 'required|integer',
            'level_format' => 'required|string',
            'numbering' => 'required|string',
            'increment' => 'required|integer',
            'award' => 'required|string',
            'is_active' => 'required',
        ]);
        $program->name = trim($request->name);
        $program->short_name = trim($request->short_name);
        $program->level_format = trim($request->level_format);
        $program->numbering = trim($request->numbering);
        $program->increment = trim($request->increment);
        $program->duration = trim($request->duration);
        $program->award = trim($request->award);
        $program->is_active = $request->is_active;
        $program->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function addNewProgramType($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('program.update'),
            403
        );

        $program = Program::where('uuid', $uuid)->first();
        if (!$program) {
            return redirect()->back()->with('error', 'Program record not found!');
        }
        $request->validate([
            'name' => 'required|string',
        ]);

        do {
            $typeSlug = 'progtp-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (ProgramType::where('slug', $typeSlug)->exists());


        ProgramType::create(
            [
                'slug' => $typeSlug,
                'program_slug' => $program->slug,
                'type_name' => $request->name,
            ]
        );
        return redirect()->back()->with('success', 'Done!');
    }
    public function editProgramType($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('program.update'),
            403
        );
        $request->validate([
            'name' => 'required|string',
            'is_active' => 'required',
        ]);

        $type = ProgramType::where('uuid', $uuid)->first();
        if (!$type) {
            return redirect()->back()->with('error', 'Program record not found!');
        }
        $type->update(
            [
                'type_name' => $request->name,
                'is_active' => $request->is_active,
            ]
        );
        return redirect()->back()->with('success', 'Done!');
    }
    public function removeProgramModal($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('program.delete'),
            403
        );

        $type = ProgramType::where('uuid', $uuid)->first();
        if (!$type) {
            return redirect()->back()->with('error', 'Program record not found!');
        }
        $typeExists = MemberAcademicInfo::where('program_type', $type->slug)->exists();
        if ($typeExists) {
            return redirect()->back()->with('error', 'Already used in members academic info, please remove first!');
        }
        $type->delete();
        return redirect()->back()->with('success', 'Done!');
    }
    public function addNewProgramProgramDurationDurationSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('program.update'),
            403
        );

        $program = Program::where('uuid', $uuid)->first();
        if (!$program) {
            return redirect()->back()->with('error', 'Program record not found!');
        }
        $request->validate([
            'duration' => 'required|numeric',
            'program_type_slug' => 'required_without:department_slug|nullable|string|exists:program_types,slug',
            'department_slug' => 'required_without:program_type_slug|nullable|string|exists:departments,slug',
        ], [
            'program_type_slug.required_without' => 'Either Program Type or Department must be filled.',
            'department_slug.required_without' => 'Either Program Type or Department must be filled.',
        ]);

        $program = Program::where('uuid', $uuid)->first();
        if (!$program) {
            return redirect()->back()->with('error', 'Program record not found!');
        }

        $duration = new ProgramDurationOverride();
        $duration->program_slug = $program->slug;
        $duration->program_type_slug = $request->program_type_slug;
        $duration->department_slug = $request->department_slug;
        $duration->duration = $request->duration;
        $duration->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editProgramDurationOverrideSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('program.update'),
            403
        );
        $request->validate([
            'duration' => 'required|numeric',
            'is_active' => 'required',
            'program_type_slug' => 'required_without:department_slug|nullable|string|exists:program_types,slug',
            'department_slug' => 'required_without:program_type_slug|nullable|string|exists:departments,slug',
        ], [
            'program_type_slug.required_without' => 'Either Program Type or Department must be filled.',
            'department_slug.required_without' => 'Either Program Type or Department must be filled.',
        ]);

        $duration = ProgramDurationOverride::where('uuid', $uuid)->first();
        if (!$duration) {
            return redirect()->back()->with('error', 'Program record not found!');
        }

        $duration->update([
            'program_type_slug' => $request->program_type_slug,
            'department_slug' => $request->department_slug,
            'duration' => $request->duration,
            'is_active' => $request->is_active,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function removeProgramDurationOverride($uuid)
    {
        $duration = ProgramDurationOverride::where('uuid', $uuid)->first();
        if (!$duration) {
            return redirect()->back()->with('error', 'Program record not found!');
        }
        $duration->delete();
        return redirect()->back()->with('success', 'Done!');
    }
}
