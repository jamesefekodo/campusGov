<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Members\MemberAcademicHistory;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Members\MemberSkill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class MemberSelfProfileController extends Controller
{
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

        $hasExistingPhoto = !empty($member->photo) && file_exists(public_path('assets/uploads/users/members/photo/' . $member->photo));

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
                $oldPath = public_path('assets/uploads/users/members/photo/' . $member->photo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo_cropped);

            $decoded = base64_decode($base64);

            $filename = $profile->userProfile->username . uniqid() . '.jpg';

            $path = public_path('assets/uploads/users/members/photo/' . $filename);

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
        $record->institution = $request->institution;
        $record->address = $request->address;
        $record->certificate = $request->certificate;
        $record->date = $request->date;
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

        $record->institution = $request->institution;
        $record->address = $request->address;
        $record->certificate = $request->certificate;
        $record->date = $request->date;
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
        $skill->skill = $request->skill;
        $skill->description = $request->description;
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
    public function myBusiness(){

    }
}
