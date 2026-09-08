<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Config\AccessControl\Role;
use App\Models\Config\AccessControl\RoleUser;
use App\Models\Finance\Claims\Invoice;
use App\Models\Finance\Dues\AssociationDue;
use App\Models\Finance\Transactions\PaymentRegularization;
use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Finance\Transactions\PaymentWaiver;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Members\MemberReAdmissionApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mpdf\Mpdf;

class MembersManagementController extends Controller
{
    public function disableUserAccount()
    {

        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('member_profile.update'),
            403
        );

        $uuid = request()->route('uuid');
        $user = User::where('uuid', $uuid)->firstOrFail();

        if ($user->account_status === 3) {
            return redirect()->back()->with('error', 'User account is already disabled.');
        }

        $user->account_status = 3;
        $user->save();

        return redirect()->back()->with('success', 'User account has been disabled successfully.');
    }
    public function reEnableUserAccount($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('member_profile.update'),
            403
        );

        $uuid = request()->route('uuid');
        $user = User::where('uuid', $uuid)->firstOrFail();

        if ($user->account_status !== 3) {
            return redirect()->back()->with('error', 'Only disabled accounts can be activated.');
        }

        $user->account_status = 0;
        $user->save();

        return redirect()->back()->with('success', 'User account has been activated successfully.');
    }
    public function memberAwaitingRegistration(Request $request)
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
        $users = '';
        $totalUsers = 0;


        if (Route::currentRouteName() == 'memberAwaitingRegistrationSearch') {
            $searchKey = $request->input('searchKey');

            $users = User::query()
                ->where('account_type', 2)->where('account_status', 0)
                ->when($searchKey, function ($query) use ($searchKey) {
                    $query->where(function ($q) use ($searchKey) {
                        $q->where('username', 'like', "%{$searchKey}%")
                            ->orWhere('email', 'like', "%{$searchKey}%");
                    });
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(50)
                ->withQueryString();

            $totalUsers = $users->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $users = User::where('account_type', 2)->where('account_status', 0)->orderBy('updated_at', 'desc')->paginate(50);
            $totalUsers = $users->total();
            $requestType = 'all';
            $pageTitle = 'Members Awaiting Registration';
        }
        $data = [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.userManagement.members.awaitingRegistration', compact('data'));
    }
    public function inActiveUserAccounts(Request $request)
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
        $users = '';
        $totalUsers = 0;


        if (Route::currentRouteName() == 'inActiveUserAccountsSearch') {
            $searchKey = $request->input('searchKey');

            $users = User::query()
                ->where('account_type', 2)->where('account_status', 3)
                ->when($searchKey, function ($query) use ($searchKey) {
                    $query->where(function ($q) use ($searchKey) {
                        $q->where('username', 'like', "%{$searchKey}%")
                            ->orWhere('email', 'like', "%{$searchKey}%");
                    });
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(50)
                ->withQueryString();

            $totalUsers = $users->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $users = User::where('account_type', 2)->where('account_status', 3)->orderBy('updated_at', 'desc')->paginate(50);
            $totalUsers = $users->total();
            $requestType = 'all';
            $pageTitle = 'Inactive User Accounts';
        }
        $data = [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.userManagement.members.awaitingRegistration', compact('data'));
    }
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
        $selectedProgram = $request->input('program', null);
        $selectedProgramType = $request->input('program_type', null);
        $selectedFaculty = $request->input('faculty', null);
        $selectedDepartment = $request->input('department', null);
        $selectedLevel = $request->input('level', null);
        $selectedGender = $request->input('gender', null);


        if (Route::currentRouteName() == 'studentMembersSearch') {
            $searchKey = $request->input('searchKey');

            $members = MemberAcademicInfo::query()
                ->where('member_academic_infos.academic_status', 'student')
                ->whereHas('userProfile', function ($query) {
                    $query->where('account_type', 2)->where('account_status', 2);
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
        } elseif (Route::currentRouteName() == 'filterStudentMembers') {
            $targettedAdmissionYear = currentAcademicYear() - $selectedLevel + 1;


            $members = MemberAcademicInfo::query()
                ->where('member_academic_infos.academic_status', 'student')

                ->whereHas('userProfile', function ($query) {
                    $query->where('account_type', 2)->where('account_status', 2);
                })

                ->when(!empty($selectedProgram) && $selectedProgram !== 'all', function ($query) use ($selectedProgram) {
                    $query->where('program', $selectedProgram);
                })

                ->when(!empty($selectedProgramType) && $selectedProgramType !== 'all', function ($query) use ($selectedProgramType) {
                    $query->where('program_type', $selectedProgramType);
                })

                ->when(!empty($selectedFaculty) && $selectedFaculty !== 'all', function ($query) use ($selectedFaculty) {
                    $query->where('faculty', $selectedFaculty);
                })

                ->when(!empty($selectedDepartment) && $selectedDepartment !== 'all', function ($query) use ($selectedDepartment) {
                    $query->where('department', $selectedDepartment);
                })

                ->when(!empty($selectedLevel), function ($query) use ($targettedAdmissionYear) {
                    $query->where('admission_session', $targettedAdmissionYear);
                })

                ->when(!empty($selectedGender) && $selectedGender !== 'all', function ($query) use ($selectedGender) {
                    $query->whereHas('personalProfile', function ($q) use ($selectedGender) {
                        $q->where('gender', $selectedGender);
                    });
                })

                ->orderBy(
                    MemberPersonalProfile::select('first_name')
                        ->whereColumn(
                            'member_personal_profiles.username',
                            'member_academic_infos.username'
                        )
                        ->limit(1)
                )

                ->paginate(50);

            $totalMembers = $members->total();
            $requestType = 'filter';
            $pageTitle = 'Student Members  :: Filtered Results';
        } else {
            $members = MemberAcademicInfo::where('academic_status', 'student')
                ->whereHas('userProfile', function ($query) {
                    $query->where('account_type', 2)->where('account_status', 2);
                })
                ->orderBy(
                    MemberPersonalProfile::select('first_name')
                        ->whereColumn('member_personal_profiles.username', 'member_academic_infos.username')
                        ->limit(1)
                )
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
            'activePrograms' => Program::where('is_active', true)->orderBy('name')->get(),
            'selectedProgram' => $selectedProgram ?? null,
            'selectedProgramType' => $selectedProgramType ?? null,
            'activeFaculties' => Faculty::where('is_active', true)->orderBy('name')->get(),
            'selectedFaculty' => $selectedFaculty ?? null,
            'selectedDepartment' => $selectedDepartment ?? null,
            'selectedLevel' => $selectedLevel ?? null,
            'selectedGender' => $selectedGender ?? null,
        ];


        return view('users.admin.userManagement.members.students.studentMembers', compact('data'));
    }
    public function nonStudentMembers(Request $request)
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
        $selectedProgram = $request->input('program', null);
        $selectedProgramType = $request->input('program_type', null);
        $selectedFaculty = $request->input('faculty', null);
        $selectedDepartment = $request->input('department', null);
        $selectedLevel = $request->input('level', null);
        $selectedGender = $request->input('gender', null);
        $selectedAcademicStatus = $request->input('academic_status', null);
        $selectedAdmissionSession = $request->input('admission_session', null);
        $selectedGraduationSession = $request->input('graduation_session', null);


        if (Route::currentRouteName() == 'nonStudentMembersSearch') {
            $searchKey = $request->input('searchKey');

            $members = MemberAcademicInfo::query()
                ->where('member_academic_infos.academic_status', '!=', 'student')
                ->whereHas('userProfile', function ($query) {
                    $query->where('account_type', 2)->where('account_status', 2);
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
        } elseif (Route::currentRouteName() == 'filterNonStudentMembers') {

            $members = MemberAcademicInfo::query()
                ->where('member_academic_infos.academic_status', '!=', 'student')

                ->whereHas('userProfile', function ($query) {
                    $query->where('account_type', 2)->where('account_status', 2);
                })

                ->when(!empty($selectedAcademicStatus) && $selectedAcademicStatus !== 'all', function ($query) use ($selectedAcademicStatus) {
                    $query->where('academic_status', $selectedAcademicStatus);
                })

                ->when(!empty($selectedProgram) && $selectedProgram !== 'all', function ($query) use ($selectedProgram) {
                    $query->where('program', $selectedProgram);
                })

                ->when(!empty($selectedProgramType) && $selectedProgramType !== 'all', function ($query) use ($selectedProgramType) {
                    $query->where('program_type', $selectedProgramType);
                })

                ->when(!empty($selectedFaculty) && $selectedFaculty !== 'all', function ($query) use ($selectedFaculty) {
                    $query->where('faculty', $selectedFaculty);
                })

                ->when(!empty($selectedDepartment) && $selectedDepartment !== 'all', function ($query) use ($selectedDepartment) {
                    $query->where('department', $selectedDepartment);
                })

                ->when(!empty($selectedAdmissionSession) && $selectedAdmissionSession !== 'all', function ($query) use ($selectedAdmissionSession) {
                    $query->where('admission_session', $selectedAdmissionSession);
                })

                ->when(!empty($selectedGraduationSession) && $selectedGraduationSession !== 'all', function ($query) use ($selectedGraduationSession) {
                    $query->where('graduation_session', $selectedGraduationSession);
                })

                ->when(!empty($selectedLevel) && $selectedLevel !== 'all', function ($query) use ($selectedLevel) {
                    $query->where('current_level', $selectedLevel);
                })

                ->when(!empty($selectedGender) && $selectedGender !== 'all', function ($query) use ($selectedGender) {
                    $query->whereHas('personalProfile', function ($q) use ($selectedGender) {
                        $q->where('gender', $selectedGender);
                    });
                })

                ->orderBy(
                    MemberPersonalProfile::select('first_name')
                        ->whereColumn(
                            'member_personal_profiles.username',
                            'member_academic_infos.username'
                        )
                        ->limit(1)
                )

                ->paginate(50);

            $totalMembers = $members->total();
            $requestType = 'filter';
            $pageTitle = 'Non-Student Members  :: Filtered Results';
        } else {
            $members = MemberAcademicInfo::where('academic_status', '!=', 'student')
                ->whereHas('userProfile', function ($query) {
                    $query->where('account_type', 2)->where('account_status', 2);
                })
                ->orderBy(
                    MemberPersonalProfile::select('first_name')
                        ->whereColumn('member_personal_profiles.username', 'member_academic_infos.username')
                        ->limit(1)
                )
                ->paginate(50);
            $totalMembers = MemberAcademicInfo::where('academic_status', '!=', 'student')->count();
            $requestType = 'all';
            $pageTitle = 'Non-Student Members';
        }

        $data = [
            'members' => $members,
            'totalMembers' => $totalMembers,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'activePrograms' => Program::where('is_active', true)->orderBy('name')->get(),
            'selectedProgram' => $selectedProgram ?? null,
            'selectedProgramType' => $selectedProgramType ?? null,
            'activeFaculties' => Faculty::where('is_active', true)->orderBy('name')->get(),
            'selectedFaculty' => $selectedFaculty ?? null,
            'selectedDepartment' => $selectedDepartment ?? null,
            'selectedLevel' => $selectedLevel ?? null,
            'selectedGender' => $selectedGender ?? null,
            'selectedAcademicStatus' => $selectedAcademicStatus ?? null,
            'selectedAdmissionSession' => $selectedAdmissionSession ?? null,
            'selectedGraduationSession' => $selectedGraduationSession ?? null,
        ];


        return view('users.admin.userManagement.members.students.studentMembers', compact('data'));
    }
    public function reAdmissionRequests(Request $request)
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
        $reAdmissionRequests = '';
        $totalReAdmissionRequests = 0;


        if (Route::currentRouteName() == 'reAdmissionRequestsSearch') {
            $searchKey = $request->input('searchKey');

            $reAdmissionRequests = MemberReAdmissionApplication::query()
                ->where('status', '!=', 'awaiting_submission')
                ->with([
                    'userProfile',
                    'personalProfile',
                    'previousProgram',
                    'newProgram',
                    'previousDepartment',
                    'newDepartment',
                    'previousProgramType',
                    'newProgramType',
                ])
                ->when($searchKey, function ($query) use ($searchKey) {

                    // Split the input into individual words
                    // e.g. "james efekodo" → ["james", "efekodo"]
                    $terms = explode(' ', trim($searchKey));

                    $query->where(function ($q) use ($terms) {

                        foreach ($terms as $term) {

                            if (empty($term)) {
                                continue;
                            }

                            $keyword = "%{$term}%";

                            $q->where(function ($subQuery) use ($keyword) {

                                // Main application/member fields
                                $subQuery
                                    ->where('username', 'like', $keyword)
                                    ->orWhere('status', 'like', $keyword)

                                    // User profile
                                    ->orWhereHas('userProfile', function ($uq) use ($keyword) {
                                        $uq->where('phone', 'like', $keyword)
                                            ->orWhere('email', 'like', $keyword);
                                    })

                                    // Personal profile
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

                                    // Program
                                    ->orWhereHas('previousProgram', function ($prq) use ($keyword) {
                                        $prq->where('name', 'like', $keyword)
                                            ->orWhere('short_name', 'like', $keyword)
                                            ->orWhere('level_format', 'like', $keyword)
                                            ->orWhere('award', 'like', $keyword);
                                    })
                                    ->orWhereHas('previousProgram', function ($prq) use ($keyword) {
                                        $prq->where('name', 'like', $keyword)
                                            ->orWhere('short_name', 'like', $keyword)
                                            ->orWhere('level_format', 'like', $keyword)
                                            ->orWhere('award', 'like', $keyword);
                                    })

                                    // Department
                                    ->orWhereHas('previousDepartment', function ($dq) use ($keyword) {
                                        $dq->where('name', 'like', $keyword)
                                            ->orWhere('description', 'like', $keyword);
                                    })
                                    ->orWhereHas('newDepartment', function ($dq) use ($keyword) {
                                        $dq->where('name', 'like', $keyword)
                                            ->orWhere('description', 'like', $keyword);
                                    })

                                    // Program type
                                    ->orWhereHas('previousProgramType', function ($ptq) use ($keyword) {
                                        $ptq->where('type_name', 'like', $keyword);
                                    })
                                    ->orWhereHas('newProgramType', function ($ptq) use ($keyword) {
                                        $ptq->where('type_name', 'like', $keyword);
                                    });
                            });
                        }
                    });
                })
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->paginate(50)
                ->withQueryString();

            $totalReAdmissionRequests = $reAdmissionRequests->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $reAdmissionRequests = MemberReAdmissionApplication::where('status', '!=', 'awaiting_submission')
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->paginate(50);
            $totalReAdmissionRequests = $reAdmissionRequests->count();
            $requestType = 'all';
            $pageTitle = 'Re-Admission Request';
        }

        $data = [
            'reAdmissionRequests' => $reAdmissionRequests,
            'totalReAdmissionRequests' => $totalReAdmissionRequests,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.userManagement.reAdmission.reAdmissionRequests', compact('data'));
    }
    public function viewReAdmissionRequest($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );

        $request = MemberReAdmissionApplication::where('uuid', $uuid)->first();
        if (!$request) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $activeProgram = Program::where('is_active', true)->orderBy('name')->get();
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();

        $data = [
            'request' => $request,
            'activeProgram' => $activeProgram,
            'activeFaculties' => $activeFaculties,
        ];
        return view('users.admin.userManagement.reAdmission.viewReAdmissionRequest', compact('data'));
    }
    public function processReAdmissionRequestSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('verify_users'),
            403
        );

        $reAdmissionRequest = MemberReAdmissionApplication::where('uuid', $uuid)->first();
        if (!$reAdmissionRequest) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $academicInfo = MemberAcademicInfo::where('username', $reAdmissionRequest->username)->first();
        if (!$academicInfo) {
            return redirect()->back()->with('error', 'Academic records not found!');
        }

        request()->validate([
            'new_program' => 'required|exists:programs,slug',
            'new_program_type' => 'required|exists:program_types,slug',
            'new_faculty' => 'required|exists:faculties,slug',
            'new_department' => 'required|exists:departments,slug',
            'new_admission_session' => 'required|numeric',
            'new_matric_number' => [
                'required',
                Rule::unique('member_academic_infos', 'matric_number')
                    ->where(function ($query) use ($academicInfo) {
                        $query->where('tenant_id', $academicInfo->tenant_id);
                    }),
            ],
            'status' => 'required|in:approved,declined',
        ]);
        // update academic rec0rds
        if ($request->status == 'approved') {
            $academicInfo->update(
                [
                    'academic_status' => 'student',
                    'program' => $request->new_program,
                    'program_type' => $request->new_program_type,
                    'department' => $request->new_department,
                    'faculty' => $request->new_faculty,
                    'admission_session' => trim($request->new_admission_session),
                    'matric_number' => trim(strtoupper($request->new_matric_number)),
                    'is_verified' => true,
                    'verified_by' => Auth::user()->username,
                    'verified_at' => Carbon::now(),
                ]
            );
        }

        // update request record
        $reAdmissionRequest->update([
            'new_program' => $request->new_program,
            'new_program_type' => $request->new_program_type,
            'new_faculty' => $request->new_faculty,
            'new_department' => $request->new_department,
            'new_admission_session' => trim($request->new_admission_session),
            'new_matric_number' => trim(strtoupper($request->new_matric_number)),
            'status' => $request->status,
            'processed_by' => Auth::user()->username,
            'processed_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Done!');
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
                'is_verified' => true,
                'verified_by' => $user->username,
                'verified_at' => Carbon::now(),
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
                'matric_number' => [
                    'required',
                    'string',
                    Rule::unique('member_academic_infos', 'matric_number')
                        ->ignore($member?->memberAcademicInfo?->id)
                        ->where(function ($query) use ($member) {
                            $query->where('tenant_id', $member->tenant_id);
                        }),
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

    public function viewMember($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('member_profile.read'),
            403
        );

        $member = User::where('uuid', $uuid)->where('account_type', 2)->first();
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
    public function editMemberAcademicInfo($uuid)
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

        $activeProgram = Program::where('is_active', true)->orderBy('name')->get();
        $activeFaculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $step = 1;
        $academicStatus = [];

        if (Route::currentRouteName() == 'editMemberAcademicInfoStep2') {

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
            'academicStatus' => $academicStatus,
            'step' => $step,
        ];

        return view('users.admin.userManagement.members.profile.update.editAcademicInfo', compact('data'));
    }

    public function updateMemberAcademicInfoSubmit($uuid, Request $request)
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
                    Rule::unique('member_academic_infos', 'matric_number')
                        ->ignore($member?->memberAcademicInfo?->id)
                        ->where(function ($query) use ($member) {
                            $query->where('tenant_id', $member->tenant_id);
                        }),
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
            return redirect()->route('editMemberAcademicInfoStep2', ['uuid' => $member->uuid])->with('success', 'Done!');
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

        return redirect()->route('viewMember', ['uuid' => $member->uuid])->with('success', 'Done!');
    }
    public function viewMemberAcademicHistory($uuid, Request $request)
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
        return view('users.admin.userManagement.members.profile.academicHistory', compact('data'));
    }
    public function viewMemberSkills($uuid)
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
        return view('users.admin.userManagement.members.profile.skills', compact('data'));
    }
    public function viewMemberBusiness($uuid)
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
        return view('users.admin.userManagement.members.profile.businessAndEmployment', compact('data'));
    }
    public function viewMemberLeadershipPosition($uuid)
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
        return view('users.admin.userManagement.members.profile.leadershipPosition', compact('data'));
    }
    public function viewMemberAward($uuid)
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
        return view('users.admin.userManagement.members.profile.awards', compact('data'));
    }
    public function viewMemberSocialMediaHandles($uuid)
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
        return view('users.admin.userManagement.members.profile.socialMediaHandles', compact('data'));
    }
    public function viewMemberMisconducts($uuid)
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
        return view('users.admin.userManagement.members.profile.misconducts', compact('data'));
    }
    public function viewMemberAssociationDues($uuid)
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
        return view('users.admin.userManagement.members.profile.associationDues', compact('data'));
    }
    public function initiatePaymentRegularization($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.create'),
            403
        );
        $member = User::where('uuid', $uuid)->first();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        request()->validate([
            'fee_id' => 'required|exists:association_dues,fee_id',
            'payment_date' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
            'manual_payment_method' => 'nullable',
            'bank_account_id' => 'nullable',
            'electronic_payment_method' => 'nullable',
            'description' => 'required',
            'proof_of_payment' => 'nullable',
        ]);




        $due = AssociationDue::where('fee_id', $request->fee_id)->first();



        if (!$due) {
            return redirect()->back()->with('error', 'Association due not found.');
        }

        $academicInfo = $member->memberAcademicInfo;

        $memberDue = $academicInfo
            ->associationDues()
            ->firstWhere('fee_id', $request->fee_id);

        if (!$memberDue) {
            $memberDue = $academicInfo
                ->pastProgramsAssociationDues()
                ->flatten(1)
                ->firstWhere('fee_id', $request->fee_id);
        }

        if (!$memberDue) {
            return redirect()->back()->with(
                'error',
                'This due does not apply to your account.'
            );
        }

        $due = $memberDue;


        // check if it has been paid
        $alreadyPaid = PaymentTransaction::where('registered_payer', $member->username)->where('due_id', $due->fee_id)->where('approval_status', 'approved')->first();

        if ($alreadyPaid) {
            return redirect()->back()->with(
                'error',
                'This association due has already been paid.'
            );
        }

        // check for pending transaction
        $pendingTransaction = PaymentTransaction::where('registered_payer', $member->username)
            ->where('due_id', $due->fee_id)
            ->where(function ($query) {
                $query->where('payment_status', 'pending')
                    ->orWhere('approval_status', 'pending');
            })
            ->first();

        if ($pendingTransaction) {
            return redirect()->back()->with(
                'error',
                'You already have a pending payment transaction. Please reconfirm it or wait for an administrator to process it before creating another payment.'
            );
        }


        // check for pending payment regularization
        $pendingRegularization = PaymentRegularization::where('username', $member->username)->where('due_id', $due->fee_id)->where('approval_status', 'pending')->first();

        if ($pendingRegularization) {
            return redirect()->back()->with(
                'error',
                'A regularization request for this due is already pending.'
            );
        }

        do {
            $regularizationId = 'pymt-regu-' . now()->format('Ymds') . '-' . tenant()->id . random_int(100000000, 999999999);
        } while (PaymentRegularization::where('regularization_id', $regularizationId)->exists());

        $regularizationRequest = new PaymentRegularization();

        $regularizationRequest->regularization_id = $regularizationId;
        $regularizationRequest->username = $member->username;
        $regularizationRequest->due_id = $due->fee_id;
        $regularizationRequest->amount_paid = $due->payable_amount;
        $regularizationRequest->bank_account_id = $request->bank_account_id;
        $regularizationRequest->payment_date = $request->payment_date;
        $regularizationRequest->electronic_payment_method = $request->electronic_payment_method;
        $regularizationRequest->manual_payment_method = $request->manual_payment_method;
        $regularizationRequest->payment_mode = filled($request->electronic_payment_method)
            ? 'electronic'
            : 'manual';

        $regularizationRequest->description = $request->description;
        $regularizationRequest->initiated_by = Auth::user()->username;
        $regularizationRequest->initiated_at = Carbon::now();
        $regularizationRequest->approval_status = 'pending';


        // 'proof_of_payment' => ,

        if ($request->hasFile('proof_of_payment')) {

            $file = $request->file('proof_of_payment');
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $uploadPath = public_path('assets/tenant_' . tenant()->id . '/uploads/paymentTransactions/proof');
            File::ensureDirectoryExists($uploadPath);

            // Base filename
            $baseName = strtolower($regularizationId);

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
                            $regularizationRequest->proof_of_payment = $webpName;
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
                $regularizationRequest->proof_of_payment = $fallbackName;
            }
        }
        $regularizationRequest->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function initiatePaymentWaiver($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.create'),
            403
        );
        $member = User::where('uuid', $uuid)->first();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        request()->validate([
            'fee_id' => 'required|exists:association_dues,fee_id',
            'reason' => 'required',
        ]);




        $due = AssociationDue::where('fee_id', $request->fee_id)->first();
        if (!$due) {
            return redirect()->back()->with('error', 'Association due not found.');
        }

        $academicInfo = $member->memberAcademicInfo;

        $memberDue = $academicInfo
            ->associationDues()
            ->firstWhere('fee_id', $request->fee_id);

        if (!$memberDue) {
            $memberDue = $academicInfo
                ->pastProgramsAssociationDues()
                ->flatten(1)
                ->firstWhere('fee_id', $request->fee_id);
        }

        if (!$memberDue) {
            return redirect()->back()->with(
                'error',
                'This due does not apply to your account.'
            );
        }

        $due = $memberDue;


        // check if it has been paid
        $alreadyPaid = PaymentTransaction::where('registered_payer', $member->username)->where('due_id', $due->fee_id)->where('approval_status', 'approved')->first();

        if ($alreadyPaid) {
            return redirect()->back()->with(
                'error',
                'This association due has already been paid.'
            );
        }

        // check for pending transaction
        $pendingTransaction = PaymentTransaction::where('registered_payer', $member->username)
            ->where('due_id', $due->fee_id)
            ->where(function ($query) {
                $query->where('payment_status', 'pending')
                    ->orWhere('approval_status', 'pending');
            })
            ->first();

        // check for pending waiver request
        $pendingWaiverRequest = PaymentWaiver::where('username', $member->username)->where('due_id', $due->fee_id)->where('approval_status', 'pending')->first();

        if ($pendingWaiverRequest) {
            return redirect()->back()->with(
                'error',
                'A waiver request for this due is already pending.'
            );
        }

        do {
            $waiverId = 'pymt-waiver-' . now()->format('Ymds') . '-' . tenant()->id . random_int(100000000, 999999999);
        } while (PaymentWaiver::where('waiver_id', $waiverId)->exists());

        $waiverRequest = new PaymentWaiver();
        $waiverRequest->waiver_id = $waiverId;
        $waiverRequest->username = $member->username;
        $waiverRequest->due_id = $due->fee_id;
        $waiverRequest->reason = trim($request->reason);
        $waiverRequest->initiated_by = Auth::user()->username;
        $waiverRequest->initiated_at = Carbon::now();
        $waiverRequest->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function viewMemberInvoices($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('invoice.read'),
            403
        );


        Invoice::where('status', 'pending')
            ->where('due_date', '<=', now())
            ->update([
                'status' => 'expired',
            ]);

        $member = User::where('uuid', $uuid)->first();
        if (!$member) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $invoices = Invoice::where('registered_payer', $member->username)->orderBy('updated_at', 'desc')->paginate(20);

        $data = [
            'member' => $member,
            'invoices' => $invoices,
        ];

        return view('users.admin.userManagement.members.profile.invoices', compact('data'));
    }

    public function downloadDuePaymentWaiverNotice($uuid)
    {

        $waiver = PaymentWaiver::with([
            'memberAcademicInfo.theProgram',
            'memberAcademicInfo.programType',
            'memberAcademicInfo.theFaculty',
            'memberAcademicInfo.theDepartment',
            'memberAcademicInfo.approvedReAdmissions.previousProgram',
            'memberAcademicInfo.approvedReAdmissions.previousProgramType',
            'memberAcademicInfo.approvedReAdmissions.previousFaculty',
            'memberAcademicInfo.approvedReAdmissions.previousDepartment',
        ])->where('uuid', $uuid)->first();
        if (!$waiver) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $memberDue = $waiver->memberAcademicInfo
            ->associationDues()
            ->firstWhere('uuid', $waiver->due->uuid);

        if (!$memberDue) {
            $memberDue = $waiver->memberAcademicInfo
                ->pastProgramsAssociationDues()
                ->flatten(1)
                ->firstWhere('uuid', $waiver->due->uuid);
        }

        if (!$memberDue) {
            return redirect()->back()->with(
                'error',
                'This due does not apply to this member.'
            );
        }

        $due = $memberDue;

        $due->payable_amount;


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

        $mpdf->setFooter('Page {PAGENO} of {nb} >> Payment Waiver Notice :: ' . $waiver->uuid);
        $mpdf->showImageErrors = true;

        $basicSystemInfo = basicSystemInfo();

        $data = [
            'waiver' => $waiver,
            'basicSystemInfo' => $basicSystemInfo,
            'tenant' => tenant(),
            'due' => $due,
        ];


        $html = view('printFiles.dues.waiver.waiverNotice', compact('data'))->render();

        $mpdf->WriteHTML($html); // AddPage() not needed — WriteHTML handles it

        $fileName = 'Payment Waiver Notice :: ' . $waiver->uuid . '_' . date('Y-m-d_H-i-s') . '.pdf';

        while (ob_get_level()) ob_end_clean();

        $mpdf->Output($fileName, 'D'); // 'I' for inline, 'D' for download
        exit;
    }
    public function viewMemberRoles($uuid)
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

        $roles = Role::where('tenant_id', tenant()->id)->orderBy('name')->get();

        $data = [
            'member' => $member,
            'availableRoles' => $roles,
        ];
        return view('users.admin.userManagement.members.profile.roles', compact('data'));
    }
    public function assignUserNewRoleSubmit($uuid, Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,id'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('roles.assign'),
            403
        );
        $theRole = Role::where('id', $request->role)->first();
        if (!$theRole) {
            return redirect()->back()->with('error', 'Role records not found!');
        }

        $member = User::where('uuid', $uuid)->first();
        if (!$member) {
            return redirect()->back()->with('error', 'User records not found!');
        }

        // check if role is already assigned
        $alreadyAssigned = RoleUser::where('username', $member->username)->where('role_id', $theRole->id)->first();
        if ($alreadyAssigned) {
            return redirect()->back()->with('error', 'Already assigned!');
        }

        // check if assignment limit is reached
        if (!is_null($theRole->user_cap) && $theRole->users()->count() >= $theRole->user_cap) {
            return redirect()->back()->with('error', 'User cap reached, please remove at least one assignment to proceed!');
        }

        RoleUser::create([
            'tenant_id' => tenant()->id,
            'username' => $member->username,
            'role_id' => $theRole->id,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function unAssignRole($uuid, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('roles.assign'),
            403
        );

        $member = User::where('uuid', $uuid)->first();
        if (!$member) {
            return redirect()->back()->with('error', 'User records not found!');
        }


        RoleUser::where('username', $member->username)
            ->where('role_id', $id)
            ->where('tenant_id', tenant()->id)
            ->delete();

        return redirect()->back()->with('success', 'Done!');
    }
}
