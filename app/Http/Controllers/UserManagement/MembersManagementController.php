<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberPersonalProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class MembersManagementController extends Controller
{
    public function studentMembers(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('member_profile.read'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $members = '';
        $totalMembers = 0;


        if (Route::currentRouteName() == 'studentMembersSearch') {
            $searchKey = $request->input('searchKey');

            $members = MemberAcademicInfo::query()
                ->where('member_academic_infos.academic_status', 'student')
                ->whereHas('userProfile', function ($query) {
                    $query->where('account_status', 2);
                })
                ->with([
                    'userProfile',
                    'personalProfile',
                    'theProgram',
                    'theDepartment',
                    'programType'
                ])
                ->when($searchKey, function ($query) use ($searchKey) {
                    // Split the input into individual words (example., "james", "oghenekevwe", "efekodo")
                    $terms = explode(' ', trim($searchKey));

                    $query->where(function ($q) use ($terms) {
                        foreach ($terms as $term) {
                            if (empty($term)) continue;
                            $keyword = "%{$term}%";

                            $q->where(function ($subQuery) use ($keyword) {
                                // Search on MemberAcademicInfo table (fully qualified to prevent ambiguity)
                                $subQuery->where('member_academic_infos.username', 'like', $keyword)
                                    ->orWhere('member_academic_infos.academic_status', 'like', $keyword)
                                    ->orWhere('member_academic_infos.matric_number', 'like', $keyword)

                                    // userProfile relationship
                                    ->orWhereHas('userProfile', function ($uq) use ($keyword) {
                                        $uq->where('phone', 'like', $keyword)
                                            ->orWhere('email', 'like', $keyword);
                                    })

                                    // personalProfile relationship
                                    ->orWhereHas('personalProfile', function ($pq) use ($keyword) {
                                        $pq->where('first_name', 'like', $keyword)
                                            ->orWhere('middle_name', 'like', $keyword)
                                            ->orWhere('last_name', 'like', $keyword)
                                            ->orWhere('phone1', 'like', $keyword)
                                            ->orWhere('email1', 'like', $keyword)
                                            ->orWhere('phone2', 'like', $keyword)
                                            ->orWhere('email2', 'like', $keyword)
                                            ->orWhere('gender', 'like', $keyword)
                                            ->orWhere('dob', 'like', $keyword)
                                            ->orWhere('nationality', 'like', $keyword)
                                            ->orWhere('state_of_origin', 'like', $keyword)
                                            ->orWhere('lga_of_origin', 'like', $keyword)
                                            ->orWhere('address1', 'like', $keyword)
                                            ->orWhere('address2', 'like', $keyword);
                                    })

                                    // theProgram relationship
                                    ->orWhereHas('theProgram', function ($prq) use ($keyword) {
                                        $prq->where('name', 'like', $keyword)
                                            ->orWhere('short_name', 'like', $keyword)
                                            ->orWhere('level_format', 'like', $keyword)
                                            ->orWhere('award', 'like', $keyword);
                                    })

                                    // theDepartment relationship
                                    ->orWhereHas('theDepartment', function ($dq) use ($keyword) {
                                        $dq->where('name', 'like', $keyword)
                                            ->orWhere('description', 'like', $keyword);
                                    })

                                    // programType relationship
                                    ->orWhereHas('programType', function ($ptq) use ($keyword) {
                                        $ptq->where('type_name', 'like', $keyword);
                                    });
                            });
                        }
                    });
                })
                // Explicit join with member_personal_profiles table for sorting
                ->join('member_personal_profiles', 'member_academic_infos.username', '=', 'member_personal_profiles.username')
                ->orderBy('member_personal_profiles.first_name', 'asc')
                ->select('member_academic_infos.*') // Prevent column name collisions from the join
                ->paginate(50)
                ->withQueryString();

            $totalMembers = $members->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $members = MemberAcademicInfo::where('academic_status', 'student')
                ->whereHas('userProfile', function ($query) {
                    $query->where('account_status', 2);
                })
                ->paginate(50);
            $totalMembers = MemberAcademicInfo::where('academic_status', 'student')->count();
            $requestType = 'all';
            $pageTitle = 'Student Members';
        }

        $data = [
            'members' => $members,
            'totalMembers' => $totalMembers,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];


        return view('users.admin.userManagement.members.students.studentMembers', compact('data'));
    }
    public function memberAwaitingVerification(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $members = '';
        $totalMembers = 0;


        if (Route::currentRouteName() == 'memberAwaitingVerificationSearch') {
            $searchKey = $request->input('searchKey');

            $members = MemberAcademicInfo::query()
                ->whereHas('userProfile', function ($query) {
                    $query->where('account_status', 1);
                })
                ->with([
                    'userProfile',
                    'personalProfile',
                    'theProgram',
                    'theDepartment',
                    'programType'
                ])
                ->when($searchKey, function ($query) use ($searchKey) {
                    // Split the input into individual words (example., "james", "oghenekevwe", "efekodo")
                    $terms = explode(' ', trim($searchKey));

                    $query->where(function ($q) use ($terms) {
                        foreach ($terms as $term) {
                            if (empty($term)) continue;
                            $keyword = "%{$term}%";

                            $q->where(function ($subQuery) use ($keyword) {
                                // Search on MemberAcademicInfo table (fully qualified to prevent ambiguity)
                                $subQuery->where('member_academic_infos.username', 'like', $keyword)
                                    ->orWhere('member_academic_infos.academic_status', 'like', $keyword)
                                    ->orWhere('member_academic_infos.matric_number', 'like', $keyword)

                                    // userProfile relationship
                                    ->orWhereHas('userProfile', function ($uq) use ($keyword) {
                                        $uq->where('phone', 'like', $keyword)
                                            ->orWhere('email', 'like', $keyword);
                                    })

                                    // personalProfile relationship
                                    ->orWhereHas('personalProfile', function ($pq) use ($keyword) {
                                        $pq->where('first_name', 'like', $keyword)
                                            ->orWhere('middle_name', 'like', $keyword)
                                            ->orWhere('last_name', 'like', $keyword)
                                            ->orWhere('phone1', 'like', $keyword)
                                            ->orWhere('email1', 'like', $keyword)
                                            ->orWhere('phone2', 'like', $keyword)
                                            ->orWhere('email2', 'like', $keyword)
                                            ->orWhere('gender', 'like', $keyword)
                                            ->orWhere('dob', 'like', $keyword)
                                            ->orWhere('nationality', 'like', $keyword)
                                            ->orWhere('state_of_origin', 'like', $keyword)
                                            ->orWhere('lga_of_origin', 'like', $keyword)
                                            ->orWhere('address1', 'like', $keyword)
                                            ->orWhere('address2', 'like', $keyword);
                                    })

                                    // theProgram relationship
                                    ->orWhereHas('theProgram', function ($prq) use ($keyword) {
                                        $prq->where('name', 'like', $keyword)
                                            ->orWhere('short_name', 'like', $keyword)
                                            ->orWhere('level_format', 'like', $keyword)
                                            ->orWhere('award', 'like', $keyword);
                                    })

                                    // theDepartment relationship
                                    ->orWhereHas('theDepartment', function ($dq) use ($keyword) {
                                        $dq->where('name', 'like', $keyword)
                                            ->orWhere('description', 'like', $keyword);
                                    })

                                    // programType relationship
                                    ->orWhereHas('programType', function ($ptq) use ($keyword) {
                                        $ptq->where('type_name', 'like', $keyword);
                                    });
                            });
                        }
                    });
                })
                // Join using username instead of personal_profile_id (removed duplicate join)
                ->join('member_personal_profiles', 'member_academic_infos.username', '=', 'member_personal_profiles.username')
                ->orderBy('member_personal_profiles.first_name', 'asc')
                ->select('member_academic_infos.*') // Prevent column name collisions from the join
                ->paginate(50)
                ->withQueryString();

            $totalMembers = $members->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $members = MemberAcademicInfo::whereHas('userProfile', function ($query) {
                $query->where('account_status', 1);
            })
                ->paginate(50);
            $totalMembers = MemberAcademicInfo::count();
            $requestType = 'all';
            $pageTitle = 'Verify Users';
        }

        $data = [
            'members' => $members,
            'totalMembers' => $totalMembers,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];


        return view('users.admin.userManagement.members.verification.awaitingVerification', compact('data'));
    }
    public function viewMemberAwaitingVerificationIndex($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );

        $member = User::where('uuid', $uuid)->where('account_status', '1')->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Records not found');
        }

        $data = [
            'member' => $member,
        ];
        return view('users.admin.userManagement.members.verification.verificationIndex', compact('data'));
    }
    public function returnProfileFromVerToReg($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );
        $member = User::where('uuid', $uuid)->where('account_status', '1')->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $member->update([
            'account_status' => 0,
        ]);

        return redirect()->route('memberAwaitingVerification')->with('success', 'Done!');
    }

    public function updateBasicAccountInfo($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('member_profile.update'),
            403
        );
        $member = User::where('uuid', $uuid)->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        request()->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($member->username, 'username'),
                Rule::unique('member_personal_profiles', 'email2')->ignore($member->username, 'username'),
            ],
            'phone' => [
                'required',
                'string',
                Rule::unique('users', 'phone')->ignore($member->username, 'username'),
                Rule::unique('member_personal_profiles', 'phone2')->ignore($member->username, 'username'),
            ],
        ]);

        if ($member->email !== $request->email) {
            $member->email_verified_at = null;
            $member->email_verification = false;
            $member->email_verification_code = null;
        }

        $member->email = $request->email;
        $member->phone = $request->phone;
        $member->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function verifyMemberPersonalProfile($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );
        $member = User::where('uuid', $uuid)->where('account_status', '1')->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'member' => $member,
        ];
        return view('users.admin.userManagement.members.verification.personalProfile', compact('data'));
    }
    public function verifyMemberPersonalProfileSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );
        $member = User::where('uuid', $uuid)->where('account_status', '1')->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }



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
                'is_verified' => true,
                'verified_by' => $user->username,
                'verified_at' => Carbon::now(),
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

        return redirect()->route('viewMemberAwaitingVerificationIndex', ['uuid' => $member->uuid])->with('success', 'Done!');
    }

    public function verifyMemberAcademicInfo($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );
        $member = User::where('uuid', $uuid)->where('account_status', '1')->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $activeProgram = Program::where('is_active', true)->orderBy('name')->get();
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $step = 1;
        $academicStatus = [];

        if (Route::currentRouteName() == 'verifyMemberAcademicInfoStep2') {

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
            'academicStatus' => $academicStatus,
            'step' => $step,
        ];
        return view('users.admin.userManagement.members.verification.academicInfo', compact('data'));
    }
    public function verifyMemberAcademicInfoSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );
        $member = User::where('uuid', $uuid)->where('account_status', '1')->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }


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
            return redirect()->route('verifyMemberAcademicInfoStep2', ['uuid' => $member->uuid])->with('success', 'Done!');
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
                'verified_by' => $user->username,
                'verified_at' => Carbon::now(),
            ]);
        }

        return redirect()->route('viewMemberAwaitingVerificationIndex', ['uuid' => $member->uuid])->with('success', 'Done!');
    }
    public function disableMemberAccount($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('member_profile.update'),
            403
        );

        $member = User::where('uuid', $uuid)->where('account_status', '1')->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $member->update([
            'account_status' => 3,
        ]);
        return redirect()->route('studentMembers')->with('success', 'Done!');
    }
    public function verifyAndactivateStudentAccount($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );
        $member = User::where('uuid', $uuid)->where('account_status', '1')->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        if ($member->memberPersonalProfile->is_verified !== true) {
            return redirect()->back()->with('error', 'Personal profile not verified yet!');
        }
        if ($member->memberAcademicInfo->is_verified !== true) {
            return redirect()->back()->with('error', 'Academic info not verified yet!');
        }


        $member->update([
            'account_status' => 2,
        ]);
        return redirect()->route('memberAwaitingVerification')->with('success', 'Done!');
    }

    public function viewMMember($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('member_profile.read'),
            403
        );

        $member = User::where('uuid', $uuid)->first();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'member' => $member,
        ];
        return view('users.admin.userManagement.members.profile.profileIndex', compact('data'));
    }
    public function viewMemberPersonalProfile($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('member_profile.read'),
            403
        );

        $member = User::where('uuid', $uuid)->first();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'member' => $member,
        ];
        return view('users.admin.userManagement.members.profile.profileInfo', compact('data'));
    }
    public function viewMemberAcademicInfo($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('member_profile.read'),
            403
        );

        $member = User::where('uuid', $uuid)->first();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }


        $data = [
            'member' => $member,
        ];
        return view('users.admin.userManagement.members.profile.academicInfo', compact('data'));
    }
}
