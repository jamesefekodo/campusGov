<?php

namespace App\Http\Controllers;

use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\Finance\Claims\FinancialClaim;
use App\Models\Finance\Claims\Invoice;
use App\Models\Finance\Donations\Donation;
use App\Models\Finance\Dues\AssociationDue;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberReAdmissionApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        // types: 0 - root, 1-admin , 2- student, 3 - association, 4 - association suppor
        // status 0 - new , 1- awaiting approval , 2- active, 3 - disabled

        if ($user) {
            // 2 student Account

            if ($user->account_type == 2 && getUserType() === 'member') {
                $data = [
                    'member' => $user,
                ];
                return view('users.member.dashboard', compact('data'));
            }
            // 1 - Admin, 0 - root - 90 odu admin
            if (
                in_array((int) $user->account_type, [0, 1, 90], true) || $user->admin_privilege === true
            ) {

                // admin data
                // static data
                // Members account stats
                $activeStudentMembersAccounts = MemberAcademicInfo::where('academic_status', 'student')
                    ->whereHas('userProfile', function ($query) {
                        $query->where('account_type', 2)->where('account_status', 2);
                    })->count();

                $memberAccountsAwaitingRegistration = User::where('account_type', 2)
                    ->where('account_status', 0)
                    ->count();

                $memberAccountsAwaitingVerification = MemberAcademicInfo::whereHas('userProfile', function ($query) {
                    $query->where('account_status', 1);
                })->count();

                $inactiveStudentAccounts = User::where('account_type', 2)
                    ->where('account_status', 3)
                    ->count();

                $readmissionsPendingApproval = MemberReAdmissionApplication::where('status', 'pending')
                    ->count();

                // Administrators
                $administrators = User::whereIn('account_type', [0, 1])
                    ->where('tenant_id', tenant()->id)
                    ->count();

                $activeAdministrators = User::whereIn('account_type', [0, 1])
                    ->where('tenant_id', tenant()->id)
                    ->where('account_status', 2)
                    ->count();
                $graduates = MemberAcademicInfo::query()
                    ->where('academic_status', 'alumni')
                    ->whereHas('userProfile', function ($query) {
                        $query->where('account_type', 2)
                            ->where('account_status', 2);
                    })
                    ->count();


                // finance stat
                $accountBalance = basicSystemInfo()->account_balance;
                $financialClaimsPendingApproval = FinancialClaim::where('approval_status', 'pending')->count();
                $duesForSession = AssociationDue::where('is_active', true)->where('academic_year', currentAcademicYear())->count();
                $ongoingDonations = Donation::where('approval_status', 'approved')->where('is_active', true)->count();
                $invoicePendingPayments = Invoice::where('status', 'pending')->count();



                // membershiip
                /*
|--------------------------------------------------------------------------
| Members analytics
|--------------------------------------------------------------------------
*/

                $analyticsYear = (int) $request->input('analytics_year', currentAcademicYear());
                $foundingYear = schoolFoundingYear();
                $currentYear = currentAcademicYear();

                // Never allow a year outside your institution's configured history.
                $analyticsYear = max($foundingYear, min($analyticsYear, $currentYear));

                $selectedAnalyticsFaculty = $request->input('analytics_faculty');
                $selectedAnalyticsDepartment = $request->input('analytics_department');
                $selectedAnalyticsProgram = $request->input('analytics_program');
                $selectedAnalyticsProgramType = $request->input('analytics_program_type');
                $selectedAnalyticsGender = $request->input('analytics_gender');
                $selectedAnalyticsStatus = $request->input('analytics_status');

                /*
 * First apply DB-safe filters. Do not add current academic_status = student here:
 * an alumnus today may have been an active student in the selected historical year.
 */
                $analyticsQuery = MemberAcademicInfo::query()
                    ->with([
                        'userProfile:username,account_type,account_status',
                        'personalProfile:username,gender',
                        'theProgram:slug,name,duration,numbering,increment,level_format',
                        'programType:slug,type_name',
                        'theFaculty:slug,name',
                        'theDepartment:slug,name',
                    ])
                    ->whereHas('userProfile', function ($query) {
                        $query->where('account_type', 2);
                        // Uncomment this only if charts should exclude currently disabled accounts.
                        // ->where('account_status', 2);
                    })
                    ->when(
                        $selectedAnalyticsFaculty,
                        fn($query) =>
                        $query->where('faculty', $selectedAnalyticsFaculty)
                    )
                    ->when(
                        $selectedAnalyticsDepartment,
                        fn($query) =>
                        $query->where('department', $selectedAnalyticsDepartment)
                    )
                    ->when(
                        $selectedAnalyticsProgram,
                        fn($query) =>
                        $query->where('program', $selectedAnalyticsProgram)
                    )
                    ->when(
                        $selectedAnalyticsProgramType,
                        fn($query) =>
                        $query->where('program_type', $selectedAnalyticsProgramType)
                    )
                    ->when($selectedAnalyticsGender, function ($query) use ($selectedAnalyticsGender) {
                        $query->whereHas('personalProfile', function ($profileQuery) use ($selectedAnalyticsGender) {
                            $profileQuery->where('gender', $selectedAnalyticsGender);
                        });
                    });

                $analyticsMembers = $analyticsQuery->get()
                    ->map(function ($academic) use ($analyticsYear) {
                        $admissionYear = (int) substr((string) $academic->admission_session, 0, 4);

                        if ($admissionYear < 1 || !$academic->theProgram) {
                            return null;
                        }

                        $duration = getProgramDuration(
                            $academic->theProgram,
                            $academic->programType,
                            $academic->theDepartment
                        );

                        $expectedGraduationSession = expectedGraduationSession(
                            $academic->admission_session,
                            $academic->theProgram,
                            $academic->programType,
                            $academic->theDepartment
                        );

                        $expectedGraduationYear = $expectedGraduationSession
                            ? (int) $expectedGraduationSession
                            : ($admissionYear + $duration - 1);

                        $wasStudentInSelectedYear = $analyticsYear >= $admissionYear
                            && $analyticsYear <= $expectedGraduationYear;

                        $level = $wasStudentInSelectedYear
                            ? ($analyticsYear - $admissionYear + 1)
                            : null;

                        /*
         * Status rule aligned with syncStudentStatuses():
         * - Alumni if analytics year is AFTER the expected graduation year.
         * - Student if within programme duration for that year.
         * - Otherwise use stored status.
         */
                        if ($analyticsYear > $expectedGraduationYear) {
                            $statusForYear = 'alumni';
                        } elseif ($wasStudentInSelectedYear) {
                            $statusForYear = 'student';
                        } else {
                            $statusForYear = $academic->academic_status ?: 'unknown';
                        }

                        return [
                            'username' => $academic->username,
                            'admission_year' => $admissionYear,
                            'expected_graduation_year' => $expectedGraduationYear,
                            'was_student' => $wasStudentInSelectedYear,
                            'level' => $level,
                            'status' => $statusForYear,
                            'faculty' => $academic->theFaculty?->name ?? 'Not specified',
                            'department' => $academic->theDepartment?->name ?? 'Not specified',
                            'program' => $academic->theProgram?->name ?? 'Not specified',
                            'program_type' => $academic->programType?->type_name ?? 'Not specified',
                            'gender' => ucfirst($academic->personalProfile?->gender ?? 'unknown'),
                        ];
                    })
                    ->filter()
                    ->values();

                $studentsForAnalyticsYear = $analyticsMembers
                    ->filter(fn($member) => $member['was_student'])
                    ->values();

                /*
 * If academic status was selected, filter the chart population by the
 * historical status computed above. Selecting “student” uses duration logic.
 */
                $analyticsPopulation = $selectedAnalyticsStatus
                    ? $analyticsMembers->filter(fn($member) => $member['status'] === $selectedAnalyticsStatus)->values()
                    : $studentsForAnalyticsYear;

                $analyticsGroup = function ($members, string $field) {
                    return $members
                        ->groupBy($field)
                        ->map(fn($items, $label) => [
                            'label' => (string) $label,
                            'count' => $items->count(),
                        ])
                        ->sortByDesc('count')
                        ->values();
                };

                $analyticsByLevel = $analyticsPopulation
                    ->filter(fn($member) => !is_null($member['level']))
                    ->groupBy('level')
                    ->map(fn($items, $level) => [
                        'label' => 'Year ' . $level,
                        'count' => $items->count(),
                    ])
                    ->sortBy(fn($item) => (int) str_replace('Year ', '', $item['label']))
                    ->values();

                $membersAnalytics = [
                    'selected_year' => $analyticsYear,
                    'session_label' => $analyticsYear . '/' . ($analyticsYear + 1),
                    'total_students' => $studentsForAnalyticsYear->count(),
                    'total_displayed' => $analyticsPopulation->count(),
                    'filters_applied' => (bool) (
                        $selectedAnalyticsFaculty ||
                        $selectedAnalyticsDepartment ||
                        $selectedAnalyticsProgram ||
                        $selectedAnalyticsProgramType ||
                        $selectedAnalyticsGender ||
                        $selectedAnalyticsStatus
                    ),
                    'by_level' => $analyticsByLevel,
                    'by_faculty' => $analyticsGroup($analyticsPopulation, 'faculty'),
                    'by_department' => $analyticsGroup($analyticsPopulation, 'department'),
                    'by_program' => $analyticsGroup($analyticsPopulation, 'program'),
                    'by_program_type' => $analyticsGroup($analyticsPopulation, 'program_type'),
                    'by_gender' => $analyticsGroup($analyticsPopulation, 'gender'),
                    'by_status' => $analyticsGroup($analyticsMembers, 'status'),
                ];

                $data = [
                    'user' => $user,
                    'activeStudentMembersAccounts' => $activeStudentMembersAccounts,
                    'memberAccountsAwaitingRegistration' => $memberAccountsAwaitingRegistration,
                    'memberAccountsAwaitingVerification' => $memberAccountsAwaitingVerification,
                    'inactiveStudentAccounts' => $inactiveStudentAccounts,
                    'readmissionsPendingApproval' => $readmissionsPendingApproval,
                    'administrators' => $administrators,
                    'activeAdministrators' => $activeAdministrators,
                    'graduates' => $graduates,


                    'accountBalance' => $accountBalance,
                    'financialClaimsPendingApproval' => $financialClaimsPendingApproval,
                    'duesForSession' => $duesForSession,
                    'ongoingDonations' => $ongoingDonations,
                    'invoicePendingPayments' => $invoicePendingPayments,



                    'membersAnalytics' => $membersAnalytics,
                    'analyticsYears' => range($currentYear, $foundingYear),
                    'analyticsFaculties' => Faculty::query()->orderBy('name')->get(['slug', 'name']),
                    'analyticsPrograms' => Program::query()->orderBy('name')->get(['slug', 'name']),

                    'selectedAnalyticsYear' => $analyticsYear,
                    'selectedAnalyticsFaculty' => $selectedAnalyticsFaculty,
                    'selectedAnalyticsDepartment' => $selectedAnalyticsDepartment,
                    'selectedAnalyticsProgram' => $selectedAnalyticsProgram,
                    'selectedAnalyticsProgramType' => $selectedAnalyticsProgramType,
                    'selectedAnalyticsGender' => $selectedAnalyticsGender,
                    'selectedAnalyticsStatus' => $selectedAnalyticsStatus,
                ];
                return view('users.admin.dashboard', compact('data'));
            }
        } else {
            return redirect()->route('accessDeniedLogout');
        }
    }
}
