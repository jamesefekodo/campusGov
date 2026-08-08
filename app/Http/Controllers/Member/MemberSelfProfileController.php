<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Media\MemberSocialMediaHandle;
use App\Models\Media\RecognizedSocialMediaPlatform;
use App\Models\Members\MemberAcademicHistory;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberAward;
use App\Models\Members\MemberEmploymentAndBusiness;
use App\Models\Members\MemberLeadershipPosition;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Members\MemberReAdmissionApplication;
use App\Models\Members\MemberSkill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class MemberSelfProfileController extends Controller
{
    public function memberApplyForReAdmission()
    {
        $user = Auth::user();
        $member = User::where('username', $user->username)->first();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $academicInfo = $member->memberAcademicInfo;
        $academicStatus = $academicInfo?->academic_status;

        // check accademic info
        if (!in_array($academicStatus, ['alumni', 'withdrawn', 'expelled'])) {
            return redirect()->back()->with(
                'error',
                'You are not eligible to apply for re-admission!'
            );
        }

        // ##########################
        // check outstanding dues
        $outstandingDue = null;

        foreach ($member->memberAcademicInfo->associationDues() as $due) {
            $transaction = $member->successfulPaymentForDue($due->fee_id);
        }
        // Check outstanding dues
        foreach ($member->memberAcademicInfo->associationDues() as $due) {
            $transaction = $member->successfulPaymentForDue($due->fee_id);

            if (!$transaction) {
                return redirect()->back()->with(
                    'error',
                    'Please pay all your outstanding dues before applying for re-admission.'
                );
            }
        }
        ############################

        // check for existing application
        $existingApplication = MemberReAdmissionApplication::where('username', $member->username)
            ->where('status', 'pending')
            ->first();
        if ($existingApplication) {
            return redirect()->back()->with(
                'error',
                'You already have a pending re-admission application.'
            );
        }
        $activeProgram = Program::where('is_active', true)->orderBy('name')->get();
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();



        $data = [
            'member' => $member,
            'activeProgram' => $activeProgram,
            'activeFaculties' => $activeFaculties,
        ];

        return view('users.member.registration.reAdmissionApplication', compact('data'));
    }
    public function memberApplyForReAdmissionSubmit(Request $request)
    {
        $member = Auth::user();
        request()->validate([
            'program' => 'required|string|exists:programs,slug',
            'program_type' => 'required|string|exists:program_types,slug',
            'faculty' => 'required|string|exists:faculties,slug',
            'department' => 'required|string|exists:departments,slug',
            'admission_session' => 'required|numeric',
            'matric_number' => [
                'required',
                'string',
                'unique:member_academic_infos,matric_number',
            ],
        ]);

        $previousAcademicInfo = MemberAcademicInfo::where('username', $member->username)->first();
        if (!$previousAcademicInfo) {
            return redirect()->back()->with('error', 'Academic Info Not Found!');
        }

        MemberReAdmissionApplication::create([
            'username' => $member->username,
            'previous_program' => $previousAcademicInfo->program,
            'previous_program_type' => $previousAcademicInfo->program_type,
            'previous_faculty' => $previousAcademicInfo->faculty,
            'previous_department' => $previousAcademicInfo->department,
            'previous_admission_session' => $previousAcademicInfo->admission_session,
            'previous_graduation_session' => $previousAcademicInfo->graduation_session,
            'previous_matric_number' => $previousAcademicInfo->matric_number,
            'previous_academic_status' => $previousAcademicInfo->academic_status,


            'new_program' => $request->program,
            'new_program_type' => $request->program_type,
            'new_faculty' => $request->faculty,
            'new_department' => $request->department,
            'new_admission_session' => $request->admission_session,
            'new_matric_number' => strtoupper(trim($request->matric_number)),

            'academic_status' => $request->academic_status,
            'status' => 'pending',
        ]);

        return redirect()->route('memberDashboard')->with('success', 'Done!');
    }
    public function memberUpdateAcademicStatus()
    {
        $member = Auth::user();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $activeProgram = Program::where('is_active', true)->orderBy('name')->get();
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $step = 1;
        $academicStatus = [];

        if (Route::currentRouteName() == 'memberUpdateAcademicStatusStep2') {
            $step = 2;

            $academicInfo = MemberAcademicInfo::where('username', $member->username)
                ->first();


            if (!$academicInfo) {

                return redirect()
                    ->back()
                    ->with('error', 'Please complete academic information first.');
            }


            $program = Program::whereSlug($academicInfo->program)
                ->firstOrFail();


            $programType = ProgramType::whereSlug($academicInfo->program_type)
                ->first();


            $department = Department::whereSlug($academicInfo->department)
                ->first();

            $academicStatus = academicStatusOptions(
                $academicInfo->admission_session,
                $program,
                $programType,
                $department
            );


            session([
                // 'academic_status_options' => $statuses
                'academicStatus' => $academicStatus
            ]);
        }

        $data = [
            'member' => $member,
            'activeProgram' => $activeProgram,
            'activeFaculties' => $activeFaculties,
            'step' => $step,
            'academicStatus' => $academicStatus,
        ];

        return view('users.member.registration.forceUpdateAcademicInfo', compact('data'));
    }
    public function memberUpdateAcademicStatusSubmit(Request $request)
    {
        $member = Auth::user();

        if ($request->step == 1) {

            request()->validate([
                'program' => 'required|string|exists:programs,slug',
                'program_type' => 'required|string|exists:program_types,slug',
                'faculty' => 'required|string|exists:faculties,slug',
                'department' => 'required|string|exists:departments,slug',
                'admission_session' => 'required|numeric',
                'matric_number' => [
                    'required',
                    'string',
                    Rule::unique('member_academic_infos', 'matric_number')->ignore($member?->memberAcademicInfo->id),
                ],
            ]);



            $info = MemberAcademicInfo::updateOrCreate(
                ['username' => $member->username],
                [
                    'username' => $member->username,
                    'program' => $request->program,
                    'program_type' => $request->program_type,
                    'faculty' => $request->faculty,
                    'department' => $request->department,
                    'admission_session' => $request->admission_session,
                    'matric_number' => strtoupper(trim($request->matric_number)),
                ]
            );
            return redirect()->route('memberUpdateAcademicStatusStep2')->with('success', 'Done!');
        } elseif ($request->step == 2) {
            request()->validate([
                'academic_status' => 'required|not_in:unknown',
                'graduation_session' => 'nullable|numeric',
            ]);
            $info = MemberAcademicInfo::where('username', $member->username)->first();
            if (!$info) {
                return redirect()->back()->with('error', 'Please fill section one first!');
            }
            $info->update([
                'academic_status' => $request->academic_status,
                'graduation_session' => $request->graduation_session,
                'is_verified' => true,
            ]);
        }
        return redirect()->route('memberDashboard')->with('success', 'Done!');
    }
    public function studentAccountSetup()
    {
        $member = Auth::user();

        $data = [
            'member' => $member,
        ];
        return view('users.member.registration.registrationIndex', compact('data'));
    }

    public function memberCreatePersonalProfile()
    {
        $member = Auth::user();
        $data = [
            'member' => $member,
        ];
        return view('users.member.registration.registerPersonalProfile', compact('data'));
    }
    public function memberSelfCreatePersonalProfileSubmit(Request $request)
    {
        $member = Auth::user();

        $hasExistingPhoto = !empty($member->photo) && file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $member->photo));

        request()->validate([
            // 'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:300',
            'photo_cropped' => $hasExistingPhoto ? 'nullable' : 'required',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required',
            'dob' => ['required', 'date', 'before:' . now()->subYears(12)->toDateString()],
            'nationality' => 'required|string',
            'state_of_origin' => 'required|string',
            'lga_of_origin' => 'required|string',
            'email2' => [
                'nullable',
                'email',
                'unique:users,email',
                Rule::unique('member_personal_profiles', 'email2')->ignore($member->username, 'username'),
            ],
            'phone2' => [
                'nullable',
                'string',
                'unique:users,phone',
                Rule::unique('member_personal_profiles', 'phone2')->ignore($member->username, 'username'),
            ],
            'address1' => 'required|string',
            'address2' => 'nullable|string',
        ]);

        $profile = MemberPersonalProfile::updateOrCreate(
            ['username' => $member->username],
            [
                'username' => $member->username,
                'phone1' => $member->phone,
                'email1' => $member->email,
                'phone2' => trim($request->phone2),
                'email2' => trim($request->email2),
                'first_name' => trim($request->first_name),
                'middle_name' => trim($request->middle_name),
                'last_name' => trim($request->last_name),
                'gender' => trim($request->gender),
                'dob' => trim($request->dob),
                'nationality' => trim($request->nationality),
                'state_of_origin' => trim($request->state_of_origin),
                'lga_of_origin' => trim($request->lga_of_origin),
                'address1' => trim($request->address1),
                'address2' => trim($request->address2),
            ]
        );

        if ($request->filled('photo_cropped')) {
            // Delete existing photo if it exists
            if (!empty($profile->userProfile->photo)) {
                $oldPath = public_path('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $member->photo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo_cropped);

            $decoded = base64_decode($base64);

            $filename = $profile->userProfile->username . uniqid() . '.jpg';

            $path = public_path('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $filename);

            file_put_contents($path, $decoded);

            $profile->userProfile->photo = $filename;
            $profile->userProfile->save();
        }


        if (Route::currentRouteName() == 'memberSelfCreatePersonalProfileSubmit') {
            return redirect()->route('studentAccountSetup')->with('success', 'Done!');
        } elseif (Route::currentRouteName() == 'memberEditPersonalProfileSubmit') {
            return redirect()->route('myPersonalProfile')->with('success', 'Done!');
        }
    }
    public function memberCreateAcademicInfo()
    {
        $member = Auth::user();
        $activeProgram = Program::where('is_active', true)->orderBy('name')->get();
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $step = 1;
        // $statuses = [];
        $academicStatus = [];

        if (Route::currentRouteName() == 'memberCreateAcademicInfoStep2') {

            $step = 2;

            $academicInfo = MemberAcademicInfo::where('username', $member->username)
                ->first();


            if (!$academicInfo) {

                return redirect()
                    ->route('memberCreateAcademicInfo')
                    ->with('error', 'Please complete academic information first.');
            }


            $program = Program::whereSlug($academicInfo->program)
                ->firstOrFail();


            $programType = ProgramType::whereSlug($academicInfo->program_type)
                ->first();


            $department = Department::whereSlug($academicInfo->department)
                ->first();



            // $statuses = academicStatusOptions(
            //     $academicInfo->admission_session,
            //     $program,
            //     $programType,
            //     $department
            // );
            $academicStatus = academicStatusOptions(
                $academicInfo->admission_session,
                $program,
                $programType,
                $department
            );


            session([
                // 'academic_status_options' => $statuses
                'academicStatus' => $academicStatus
            ]);
        }

        $data = [
            'member' => $member,
            'activeProgram' => $activeProgram,
            'activeFaculties' => $activeFaculties,
            'step' => $step,
            // 'statuses' => $statuses,
            'academicStatus' => $academicStatus,
        ];

        return view('users.member.registration.registerAcademicInfo', compact('data'));
    }
    public function memberSelfCreateAcademicInfoSubmit(Request $request)
    {
        $member = Auth::user();
        if ($request->step == 1) {

            request()->validate([
                'program' => 'required|string|exists:programs,slug',
                'program_type' => 'required|string|exists:program_types,slug',
                'faculty' => 'required|string|exists:faculties,slug',
                'department' => 'required|string|exists:departments,slug',
                'admission_session' => 'required|numeric',
                'matric_number' => 'required|string',
            ]);



            $info = MemberAcademicInfo::updateOrCreate(
                ['username' => $member->username],
                [
                    'username' => $member->username,
                    'program' => $request->program,
                    'program_type' => $request->program_type,
                    'faculty' => $request->faculty,
                    'department' => $request->department,
                    'admission_session' => $request->admission_session,
                    'matric_number' => strtoupper(trim($request->matric_number)),
                ]
            );
            return redirect()->route('memberCreateAcademicInfoStep2')->with('success', 'Done!');
        } elseif ($request->step == 2) {
            request()->validate([
                'academic_status' => 'required|not_in:unknown',
                'graduation_session' => 'nullable|numeric',
            ]);
            $info = MemberAcademicInfo::where('username', $member->username)->first();
            if (!$info) {
                return redirect()->back()->with('error', 'Please fill section one first!');
            }
            $info->update([
                'academic_status' => $request->academic_status,
                'graduation_session' => $request->graduation_session,
            ]);
        }

        return redirect()->route('studentAccountSetup')->with('success', 'Done!');
    }
    public function memberSubmitSelfAccountForVerification()
    {
        $member = Auth::user();

        if ((int) $member->account_type == 2 && (int) $member->account_status == 0) {

            // Check personal profile
            if (!$member->memberPersonalProfile) {
                return redirect()->back()
                    ->with('error', 'Please fill in your personal profile first!');
            }


            // Check academic information
            $academicInfo = $member->memberAcademicInfo;

            if (
                !$academicInfo ||
                !filled($academicInfo->academic_status) ||
                !filled($academicInfo->program) ||
                !filled($academicInfo->program_type) ||
                !filled($academicInfo->department) ||
                !filled($academicInfo->faculty) ||
                !filled($academicInfo->admission_session) ||
                !filled($academicInfo->matric_number)
            ) {
                return redirect()->back()
                    ->with('error', 'Please complete your academic info first!');
            }


            // Submit account for verification
            $userProfile = User::where('username', $member->username)->first();
            if ($userProfile) {
                $userProfile->update([
                    'account_status' => 1,
                ]);
            }


            return redirect()->back()
                ->with('success', 'Account submitted for verification successfully!');
        } else {

            return redirect()->route('homepage');
        }
    }
    public function awaitingVerification()
    {
        $member = Auth::user();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        if ((int)$member->account_type == 2 && (int)$member->account_status == 1) {
            $data = [
                'member' => $member,
            ];
            return view('users.member.verification.awaitingVerification', compact('data'));
        } else {
            return redirect()->route('homepage');
        }
    }

    public function memberViewSelfProfile()
    {
        $member = Auth::user();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'member' => $member,
        ];
        return view('users.member.myProfile.myProfile', compact('data'));
    }
    public function myPersonalProfile()
    {
        $member = Auth::user();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'member' => $member,
            'display' => 'view',
            'pageTitle' => 'Personal Profile',
        ];
        return view('users.member.myProfile.myPersonalProfile', compact('data'));
    }
    public function memberEditPersonalProfile()
    {
        $member = Auth::user();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'member' => $member,
            'display' => 'edit',
            'pageTitle' => 'Update Personal Profile',
        ];
        return view('users.member.myProfile.myPersonalProfile', compact('data'));
    }
    public function myAcademicInfo()
    {
        $member = Auth::user();

        $data = [
            'member' => $member,
        ];
        return view('users.member.myProfile.myAcademicInfo', compact('data'));
    }
    public function myAcademicHistory()
    {
        $member = Auth::user();

        $data = [
            'member' => $member,
        ];
        return view('users.member.myProfile.myAcademicHistory', compact('data'));
    }
    public function addNewAcademicRecordSubmit(Request $request)
    {
        $member = Auth::user();

        $request->validate([
            'institution' => 'required|string',
            'address' => 'required|string',
            'certificate' => 'required|string',
            'date' => 'required',
        ]);

        $record = new MemberAcademicHistory();
        $record->username = $member->username;
        $record->institution = trim($request->institution);
        $record->address = trim($request->address);
        $record->certificate = trim($request->certificate);
        $record->date = trim($request->date);
        $record->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function editAcademicRecordSubmit($id, Request $request)
    {
        $member = Auth::user();

        $record = MemberAcademicHistory::where('id', $id)->where('username', $member->username)->first();
        if (!$record) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $request->validate([
            'institution' => 'required|string',
            'address' => 'required|string',
            'certificate' => 'required|string',
            'date' => 'required',
        ]);

        $record->institution = trim($request->institution);
        $record->address = trim($request->address);
        $record->certificate = trim($request->certificate);
        $record->date = trim($request->date);
        $record->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function deleteAcademicRecordSubmit($id)
    {
        $member = Auth::user();

        $record = MemberAcademicHistory::where('id', $id)->where('username', $member->username)->first();
        if (!$record) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $record->delete();
        return redirect()->back()->with('success', 'Done!');
    }
    public function myAcademicSkills()
    {
        $member = Auth::user();

        $data = [
            'member' => $member,
        ];
        return view('users.member.myProfile.mySkills', compact('data'));
    }
    public function addSkillSubmit(Request $request)
    {
        $member = Auth::user();

        $request->validate([
            'skill' => 'required|string',
            'description' => 'required|string',
        ]);
        $skill = new MemberSkill();
        $skill->username = $member->username;
        $skill->skill = trim($request->skill);
        $skill->description = trim($request->description);
        $skill->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function editSkillSubmit($id, Request $request)
    {
        $request->validate([
            'skill' => 'required|string',
            'description' => 'required|string',
        ]);
        $member = Auth::user();

        $skill = MemberSkill::where('id', $id)->where('username', $member->username)->first();
        if (!$skill) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $skill->update([
            'skill' => $request->skill,
            'description' => $request->description,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function memberSelfDeleteSkill($id)
    {
        $member = Auth::user();

        $skill = MemberSkill::where('id', $id)->where('username', $member->username)->first();
        if (!$skill) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $skill->delete();
        return redirect()->back()->with('success', 'Done!');
    }
    public function myBusiness()
    {
        $member = Auth::user();

        $data = [
            'member' => $member,
        ];
        return view('users.member.myProfile.myBusiness', compact('data'));
    }
    public function addNewBusinessSubmit(Request $request)
    {
        $member = Auth::user();

        $request->validate([
            'business' => 'required',
            'address' => 'required',
            'position' => 'required',
            'start_date' => 'required',
            'end_date' => 'nullable',
        ]);


        $record = new MemberEmploymentAndBusiness();
        $record->username = $member->username;
        $record->business = trim($request->business);
        $record->address = trim($request->address);
        $record->position = trim($request->position);
        $record->start_date = $request->start_date;
        $record->end_date = $request->end_date;
        $record->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function editNewBusinessSubmit($id, Request $request)
    {
        $request->validate([
            'business' => 'required',
            'address' => 'required',
            'position' => 'required',
            'start_date' => 'required',
            'end_date' => 'nullable',
        ]);

        $member = Auth::user();

        $record = MemberEmploymentAndBusiness::where('id', $id)->where('username', $member->username)->first();
        if (!$record) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $record->update([
            'business' => trim($request->business),
            'address' => trim($request->address),
            'position' => trim($request->position),
            'start_date' => trim($request->start_date),
            'end_date' => trim($request->end_date),
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function deleteBusinessRecordSubmit($id)
    {
        $member = Auth::user();

        $record = MemberEmploymentAndBusiness::where('id', $id)->where('username', $member->username)->first();
        if (!$record) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $record->delete();
        return redirect()->back()->with('success', 'Done!');
    }

    public function myLeadershipPosition()
    {
        $member = Auth::user();

        $data = [
            'member' => $member,
        ];
        return view('users.member.myProfile.myLeadershipPosition', compact('data'));
    }
    public function addLeadershipPositionSubmit(Request $request)
    {
        $member = Auth::user();

        $request->validate([
            'position' => 'required|string',
            'organization' => 'required|string',
            'address' => 'required|string',
            'is_current' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $position = new MemberLeadershipPosition();
        $position->username = $member->username;
        $position->position = trim($request->position);
        $position->organization = trim($request->organization);
        $position->address = trim($request->address);
        $position->is_current = trim($request->is_current);
        $position->start_date = trim($request->start_date);
        $position->end_date = trim($request->end_date);
        $position->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function editLeadershipPositionSubmit($id, Request $request)
    {
        $member = Auth::user();

        $position = MemberLeadershipPosition::where('id', $id)->where('username', $member->username)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $position->update([
            'position' => trim($request->position),
            'organization' => trim($request->organization),
            'address' => trim($request->address),
            'is_current' => trim($request->is_current),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function deleteLeadershipPositionSubmit($id)
    {
        $member = Auth::user();

        $position = MemberLeadershipPosition::where('id', $id)->where('username', $member->username)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $position->delete();
        return redirect()->back()->with('success', 'Done!');
    }
    public function myAwards()
    {
        $member = Auth::user();

        $data = [
            'member' => $member,
        ];
        return view('users.member.myProfile.myAwards', compact('data'));
    }
    public function addAwardSubmit(Request $request)
    {

        $member = Auth::user();

        $request->validate([
            'award' => 'required|string',
            'organization' => 'required|string',
            'date' => 'required|date',
        ]);
        $award = new MemberAward();
        $award->username = $member->username;
        $award->award = trim($request->award);
        $award->organization = trim($request->organization);
        $award->date = $request->date;
        $award->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editAwardSubmit($id, Request $request)
    {
        $member = Auth::user();

        $request->validate([
            'award' => 'required|string',
            'organization' => 'required|string',
            'date' => 'required|date',
        ]);
        $award = MemberAward::where('id', $id)->where('username', $member->username)->first();
        if (!$award) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $award->update([
            'award' => trim($request->award),
            'organization' => trim($request->organization),
            'date' => trim($request->date),
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function deleteAwardSubmit($id)
    {
        $member = Auth::user();
        $award = MemberAward::where('id', $id)->where('username', $member->username)->first();
        if (!$award) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $award->delete();
        return redirect()->back()->with('success', 'Done!');
    }

    public function mySocialMediaHandles()
    {
        $member = Auth::user();
        $recognizedSocialMediaPlatforms = RecognizedSocialMediaPlatform::where('is_active', true)->orderBy('name')->get();

        $data = [
            'member' => $member,
            'recognizedSocialMediaPlatforms' => $recognizedSocialMediaPlatforms,
        ];
        return view('users.member.myProfile.mySocialMediaHandles', compact('data'));
    }
    public function addSocialMediaHandleSubmit(Request $request)
    {
        $member = Auth::user();

        $request->validate([
            'platform' => 'required|string|exists:recognized_social_media_platforms,slug',
            'handle' => 'required|string',
            'link' => 'required|url',
        ]);
        $media = new MemberSocialMediaHandle();
        $media->username = $member->username;
        $media->platform = $request->platform;
        $media->handle = trim($request->handle);
        $media->link = $request->link;
        $media->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editSocialMediaHandleSubmit($id, Request $request)
    {
        $member = Auth::user();
        $media = MemberSocialMediaHandle::where('id', $id)->where('username', $member->username)->first();
        if (!$media) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $request->validate([
            'platform' => 'required|string|exists:recognized_social_media_platforms,slug',
            'handle' => 'required|string',
            'link' => 'required|url',
        ]);
        $media->update([
            'platform' => $request->platform,
            'handle' => trim($request->handle),
            'link' => $request->link,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function employeeDeleteSocialMediaHandleSubmit($id)
    {
        $member = Auth::user();
        $media = MemberSocialMediaHandle::where('id', $id)->where('username', $member->username)->first();
        if (!$media) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $media->delete();
        return redirect()->back()->with('success', 'Done!');
    }

    public function myMisconducts()
    {
        $member = Auth::user();

        $data = [
            'member' => $member,
        ];
        return view('users.member.myProfile.myMisconducts', compact('data'));
    }
    public function myAssociationDues()
    {
        $member = Auth::user();

        $data = [
            'member' => $member,
        ];

        return view('users.member.dues.dues', compact('data'));
    }
}
