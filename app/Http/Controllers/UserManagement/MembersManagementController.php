<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Members\MemberAcademicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
                ->where('member_academic_infos.academic_status', 'student')
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
            $members = MemberAcademicInfo::where('academic_status', 'student')
                ->whereHas('userProfile', function ($query) {
                    $query->where('account_status', 1);
                })
                ->paginate(50);
            $totalMembers = MemberAcademicInfo::where('academic_status', 'student')->count();
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
}
