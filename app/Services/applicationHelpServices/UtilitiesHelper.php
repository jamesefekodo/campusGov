<?php

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\ProgramDurationOverride;
use App\Models\Config\BasicSystemSetting;
use App\Models\Config\HomepageSlider;
use App\Models\Elections\Election;
use App\Models\Elections\ElectionCandidateApplication;
use App\Models\Finance\Claims\FinancialClaim;
use App\Models\Finance\Claims\Invoice;
use App\Models\Finance\Settings\BankAccount;
use App\Models\Finance\Settings\ElectronicPaymentConfig;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\ManualPaymentMethod;
use App\Models\Finance\Transactions\PaymentRegularization;
use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Finance\Transactions\PaymentWaiver;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberReAdmissionApplication;
use App\Models\Support\WhatsappSupportLine;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;



// if (!function_exists('getUserType')) {
//     function getUserType()
//     {
//         if (!Auth::check()) {
//             return 'guest';
//         }

//         return match ((int) Auth::user()->account_type) {
//             0 => 'root',
//             1 => 'admin',
//             2 => 'member',
//             default => 'guest',
//         };
//     }
// }

if (!function_exists('getUserType')) {
    function getUserType(): string
    {
        if (!Auth::check()) {
            return 'guest';
        }

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Root account
        |--------------------------------------------------------------------------
        */
        if (in_array((int) $user->account_type, [0, 90], true)) {
            return 'root';
        }

        /*
        |--------------------------------------------------------------------------
        | Permanent admin account
        |--------------------------------------------------------------------------
        |
        | account_type = 1 means the account itself is an administrator.
        | It does not need to switch context.
        |
        */
        if ((int) $user->account_type === 1) {
            return 'admin';
        }

        /*
        |--------------------------------------------------------------------------
        | Non-member/unknown account types
        |--------------------------------------------------------------------------
        */
        if ((int) $user->account_type !== 2) {
            return 'guest';
        }

        /*
        |--------------------------------------------------------------------------
        | Member account
        |--------------------------------------------------------------------------
        |
        | A member can only switch to admin context if:
        | 1. admin_privilege is true, and
        | 2. the user still has at least one role.
        |
        */
        $canUseAdminContext = (bool) $user->admin_privilege
            && $user->roles()->exists();

        /*
        |--------------------------------------------------------------------------
        | Normal member: force personal context
        |--------------------------------------------------------------------------
        */
        if (!$canUseAdminContext) {
            if (session('user_context') !== 'personal') {
                session([
                    'user_context' => 'personal',
                ]);
            }

            return 'member';
        }

        /*
        |--------------------------------------------------------------------------
        | Member with administrative role:
        | return selected context from session
        |--------------------------------------------------------------------------
        */
        $selectedContext = session(
            'user_context',
            'personal'
        );

        /*
        |--------------------------------------------------------------------------
        | Prevent invalid context values
        |--------------------------------------------------------------------------
        */
        if (!in_array($selectedContext, ['personal', 'admin'], true)) {
            $selectedContext = 'personal';

            session([
                'user_context' => 'personal',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin context selected
        |--------------------------------------------------------------------------
        */
        if ($selectedContext === 'admin') {
            return 'admin';
        }

        /*
        |--------------------------------------------------------------------------
        | Personal context selected
        |--------------------------------------------------------------------------
        */
        return 'member';
    }
}

if (!function_exists('tenant')) {
    function tenant()
    {
        return app()->bound('tenant')
            ? app('tenant')
            : null;
    }
}

if (!function_exists('tenantId')) {
    function tenantId()
    {
        return tenant()?->id;
    }
}

if (!function_exists('homepageSliders')) {
    function homepageSliders()
    {
        return HomepageSlider::where('tenant_id', tenant()->id)->get();
    }
}
if (!function_exists('whatsappSupportLines')) {
    function whatsappSupportLines()
    {
        return WhatsappSupportLine::where('tenant_id', tenant()->id)->where('is_active', true)->orderBy('name')->get();
    }
}


if (!function_exists('basicSystemInfo')) {
    function basicSystemInfo()
    {
        if (!app()->bound('tenant')) {
            return null;
        }

        return BasicSystemSetting::where(
            'tenant_id',
            app('tenant')->id
        )->first();
    }
}

if (!function_exists('associationLogo')) {
    function associationLogo()
    {
        $defaultSettings = basicSystemInfo();

        if (
            $defaultSettings &&
            $defaultSettings->association_logo &&
            file_exists(
                public_path(
                    'assets/tenant_' . tenant()->id . '/uploads/tenantProfile/' .
                        $defaultSettings->association_logo,
                ),
            )
        ) {
            $logo = asset('assets/tenant_' . tenant()->id . '/uploads/tenantProfile/' . $defaultSettings->association_logo);
        } else {
            $logo = asset('system/images/transcend_logo.webp');
        }
        return $logo;
    }
}


if (!function_exists('base64Image')) {
    function base64Image(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        $relativePath = parse_url($url, PHP_URL_PATH);
        $fullPath = public_path(ltrim($relativePath, '/'));

        if (!file_exists($fullPath)) {
            return '';
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
            $img = imagecreatefromwebp($fullPath);
            ob_start();
            imagepng($img);
            $data = ob_get_clean();
            imagedestroy($img);
            $type = 'png';
        } else {
            $data = file_get_contents($fullPath);
            $type = $ext ?: 'png';
        }

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}

if (!function_exists('schoolLogo')) {
    function schoolLogo()
    {
        $defaultSettings = basicSystemInfo();

        if (
            $defaultSettings &&
            $defaultSettings->school_logo &&
            file_exists(
                public_path(
                    'assets/tenant_' . tenant()->id . '/uploads/tenantProfile/' .
                        $defaultSettings->school_logo,
                ),
            )
        ) {
            $logo = asset('assets/tenant_' . tenant()->id . '/uploads/tenantProfile/' . $defaultSettings->school_logo);
        } else {
            $logo = asset('system/images/transcend_logo.webp');
        }
        return $logo;
    }
}


if (!function_exists('schoolFoundingYear')) {
    function schoolFoundingYear()
    {
        return (int) (basicSystemInfo()?->school_founding_year ?? 2000);
    }
}

if (!function_exists('faculties')) {
    function faculties()
    {
        return Faculty::query()->orderBy('name');
    }
}

if (!function_exists('departments')) {
    function departments()
    {
        return Department::query()->orderBy('name');
    }
}



if (!function_exists('currentLevel')) {
    function currentLevel($program, int $currentLevel): string
    {
        $value = match ($program->numbering) {
            'roman' => toRoman($currentLevel),
            'weighted' => $currentLevel * $program->increment,
            default => $currentLevel,
        };
        return str_replace('{value}', $value, $program->level_format);
    }
}

if (!function_exists('toRoman')) {
    function toRoman(int $number): string
    {
        $map = [
            1000 => 'M',
            900  => 'CM',
            500  => 'D',
            400  => 'CD',
            100  => 'C',
            90   => 'XC',
            50   => 'L',
            40   => 'XL',
            10   => 'X',
            9    => 'IX',
            5    => 'V',
            4    => 'IV',
            1    => 'I',
        ];

        $result = '';

        foreach ($map as $value => $roman) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }

        return $result;
    }
}
function getProgramDuration($program, $programType = null, $department = null): int
{
    if (!$program) {
        return 4;
    }

    // 1. Exact match: Program + Department + Program Type
    if ($department && $programType) {
        $override = ProgramDurationOverride::where('program_slug', $program->slug)
            ->where('department_slug', $department->slug)
            ->where('program_type_slug', $programType->slug)
            ->where('is_active', true)
            ->first();

        if ($override) {
            return $override->duration;
        }
    }

    // 2. Program + Department
    if ($department) {
        $override = ProgramDurationOverride::where('program_slug', $program->slug)
            ->where('department_slug', $department->slug)
            ->whereNull('program_type_slug')
            ->where('is_active', true)
            ->first();

        if ($override) {
            return $override->duration;
        }
    }

    // 3. Program + Program Type
    if ($programType) {
        $override = ProgramDurationOverride::where('program_slug', $program->slug)
            ->where('program_type_slug', $programType->slug)
            ->whereNull('department_slug')
            ->where('is_active', true)
            ->first();

        if ($override) {
            return $override->duration;
        }
    }

    // 4. Program only
    $override = ProgramDurationOverride::where('program_slug', $program->slug)
        ->whereNull('department_slug')
        ->whereNull('program_type_slug')
        ->where('is_active', true)
        ->first();

    if ($override) {
        return $override->duration;
    }

    // 5. Default duration
    return $program->duration;
}


if (!function_exists('currentAcademicYear')) {
    function currentAcademicYear(): int
    {
        return (int) basicSystemInfo()->current_academic_year;
    }
}
if (!function_exists('isStillWithinProgramDuration')) {

    function isStillWithinProgramDuration(
        $admissionSession,
        $program,
        $programType = null,
        $department = null
    ): bool {

        if (!$admissionSession || !$program) {
            return true;
        }

        $duration = getProgramDuration($program, $programType, $department);

        $admissionYear = (int) $admissionSession;

        $currentLevel = currentAcademicYear() - $admissionYear + 1;

        return $currentLevel <= $duration;
    }
}

if (!function_exists('expectedGraduationSession')) {
    function expectedGraduationSession(
        $admissionSession,
        $program,
        $programType = null,
        $department = null
    ): ?int {
        if (!$admissionSession || !$program) {
            return null;
        }

        $duration = $program->duration;

        $variation = $program->durationOverrides
            ->sortByDesc(function ($override) {
                return (!is_null($override->admission_session) ? 8 : 0) +
                    (!is_null($override->program_type_slug) ? 4 : 0) +
                    (!is_null($override->department_slug) ? 2 : 0) +
                    (!is_null($override->program_slug) ? 1 : 0);
            })
            ->first(function ($override) use (
                $admissionSession,
                $program,
                $programType,
                $department
            ) {

                return (
                    is_null($override->admission_session) ||
                    (int) $override->admission_session === (int) $admissionSession
                )
                    &&
                    (
                        is_null($override->program_slug) ||
                        $override->program_slug === $program->slug
                    )
                    &&
                    (
                        is_null($override->program_type_slug) ||
                        !$programType ||
                        $override->program_type_slug === $programType->slug
                    )
                    &&
                    (
                        is_null($override->department_slug) ||
                        !$department ||
                        $override->department_slug === $department->slug
                    );
            });

        if ($variation?->duration) {
            $duration = $variation->duration;
        }

        if (!$duration) {
            return null;
        }

        /*
         * Example:
         *
         * Admission: 2024
         * Duration: 4
         *
         * Expected graduation:
         * 2024 + 4 - 1 = 2027
         */
        return (int) $admissionSession + (int) $duration - 1;
    }
}
// academicStatusOptions

if (!function_exists('academicStatusOptions')) {

    function academicStatusOptions(
        $admissionSession,
        $program,
        $programType = null,
        $department = null
    ): array {

        $isStudent = isStillWithinProgramDuration(
            $admissionSession,
            $program,
            $programType,
            $department
        );


        return [

            'is_student' => $isStudent,

            'statuses' => $isStudent
                ? [
                    'student' => 'Student'
                ]
                : [
                    'extra_year' => 'Extra Year',
                    'alumni' => 'Alumni',
                    'withdrawn' => 'Withdrawn',
                    'suspended' => 'Suspended',
                    'expelled' => 'Expelled',
                    'unknown' => 'Unknown',
                ],

            'require_graduation_session' => !$isStudent

        ];
    }
}

function studentCurrentLevel($academic): int
{
    if (!$academic || !$academic->admission_session) {
        return 1;
    }

    $admissionYear = (int) explode('/', $academic->admission_session)[0];

    return max(1, currentAcademicYear() - $admissionYear + 1);
}

if (!function_exists('countMemberProfilesAwaitingVerification')) {
    function countMemberProfilesAwaitingVerification()
    {
        return User::where('account_type', 2)
            ->where('account_status', 1)
            ->count();
    }
}

if (!function_exists('countPendingReAdmissionRequests')) {
    function countPendingReAdmissionRequests()
    {
        return MemberReAdmissionApplication::where('status', 'pending')
            ->count();
    }
}


if (!function_exists('manualPaymentConfig')) {
    function manualPaymentConfig()
    {
        return ManualPaymentMethod::first();
    }
}

if (!function_exists('activeBankAccounts')) {
    function activeBankAccounts()
    {
        return BankAccount::where('is_active', true)->orderBy('account_name')->get();
    }
}
if (!function_exists('electronicsPaymentMethods')) {
    function electronicsPaymentMethods()
    {
        return ElectronicPaymentMethod::query()
            ->where('is_active', true)
            ->whereHas('apiConfig', function ($q) {
                $q->where('tenant_id', tenant()->id)
                    ->where('is_active', true);
            })
            ->get();
    }
}

// check member level at the time when due is created
// if (!function_exists('studentLevelAtDue')) {
//     function studentLevelAtDue($academic, $due): int
//     {
//         if (!$academic || !$due || !$academic->admission_session) {
//             return 1;
//         }

//         return ($due->academic_year - (int) $academic->admission_session) + 1;
//     }
// }

if (!function_exists('studentLevelAtDue')) {
    function studentLevelAtDue($academic, $due): array
    {
        $default = [
            'program' => $academic?->theProgram,
            'level' => 1,
            'is_previous_program' => false,
            'readmission' => null,
        ];

        if (!$academic || !$due) {
            return $default;
        }

        $dueYear = (int) substr((string) $due->academic_year, 0, 4);

        if ($dueYear < 1) {
            return $default;
        }

        $readmissions = $academic->approvedReAdmissions
            ?? collect();

        $previousReadmission = $readmissions
            ->sortByDesc(function ($readmission) {
                return (int) substr(
                    (string) $readmission->previous_graduation_session,
                    0,
                    4
                );
            })
            ->first(function ($readmission) use ($dueYear) {
                $previousAdmissionYear = (int) substr(
                    (string) $readmission->previous_admission_session,
                    0,
                    4
                );

                $previousGraduationYear = (int) substr(
                    (string) $readmission->previous_graduation_session,
                    0,
                    4
                );

                return $previousAdmissionYear > 0
                    && $previousGraduationYear > 0
                    && $dueYear >= $previousAdmissionYear
                    && $dueYear <= $previousGraduationYear;
            });

        /*
         * Previous-programme due:
         * Use the programme stored on the readmission record.
         */
        if ($previousReadmission) {
            $previousProgram = $previousReadmission->previousProgram;

            /*
             * Do not silently use the current programme here.
             * If this is null, your old programme ID/relationship
             * is missing or has not been eager-loaded.
             */
            if (!$previousProgram) {
                return [
                    'program' => null,
                    'level' => 1,
                    'is_previous_program' => true,
                    'readmission' => $previousReadmission,
                ];
            }

            $previousAdmissionYear = (int) substr(
                (string) $previousReadmission->previous_admission_session,
                0,
                4
            );

            return [
                'program' => $previousProgram,
                'level' => max(1, $dueYear - $previousAdmissionYear + 1),
                'is_previous_program' => true,
                'readmission' => $previousReadmission,
            ];
        }

        /*
         * Current-programme due.
         */
        $currentAdmissionYear = (int) substr(
            (string) $academic->admission_session,
            0,
            4
        );

        return [
            'program' => $academic->theProgram,
            'level' => $currentAdmissionYear > 0
                ? max(1, $dueYear - $currentAdmissionYear + 1)
                : 1,
            'is_previous_program' => false,
            'readmission' => null,
        ];
    }
}



if (!function_exists('totalPendingPaymentTransactions')) {
    function totalPendingPaymentTransactions()
    {
        return PaymentTransaction::where('payment_status', 'pending')
            ->orWhere('approval_status', 'pending')
            ->count();
    }
}


if (!function_exists('totalPendingPaymentRegularizations')) {
    function totalPendingPaymentRegularizations()
    {
        return PaymentRegularization::where('approval_status', 'pending')->count();
    }
}

if (!function_exists('totalPendingPaymentWaiverRequest')) {
    function totalPendingPaymentWaiverRequest()
    {
        return PaymentWaiver::where('approval_status', 'pending')->count();
    }
}

if (!function_exists('totalElectionsAwaitingApproval')) {
    function totalElectionsAwaitingApproval()
    {
        return Election::where('status', 'awaiting_approval')->count();
    }
}


if (!function_exists('totalElectionCandidatesAwaitingApproval')) {
    function totalElectionCandidatesAwaitingApproval()
    {
        return ElectionCandidateApplication::where('status', 'pending_approval')->count();
    }
}

if (!function_exists('totalOngoingElection')) {
    function totalOngoingElection()
    {
        return Election::where('status', 'voting')->count();
    }
}


if (!function_exists('highestProgramDuration')) {
    function highestProgramDuration(): int
    {
        return DB::query()
            ->fromSub(function ($query) {
                $query->select('duration')
                    ->from('programs')
                    ->where('is_active', true)
                    ->unionAll(
                        DB::table('program_duration_overrides')
                            ->select('duration')
                            ->where('is_active', true)
                    );
            }, 'durations')
            ->max('duration') ?? 0;
    }
}


// check if student is qualified to apply for a position in an election
// Check if a student is qualified to apply for a position in an election
if (!function_exists('canApplyForPosition')) {
    function canApplyForPosition($student, $position): array
    {
        // Academic record required
        $academic = $student->memberAcademicInfo;
        if (!$academic) {
            return ['qualified' => false, 'message' => 'Academic record not found',];
        }
        // General requirement
        if ($academic->academic_status !== 'student') {
            return ['qualified' => false, 'message' => 'Only active students can apply',];
        }
        $requirements = $position->candidateGroups;
        // No requirements = any student qualifies
        if ($requirements->isEmpty()) {
            return ['qualified' => true, 'message' => 'Qualified',];
        }
        foreach ($requirements as $requirement) {
            // Program
            if ($requirement->program && $requirement->program !== $academic->program) {
                continue;
            }
            // Program Type
            if ($requirement->program_type && $requirement->program_type !== $academic->program_type) {
                continue;
            }
            // Faculty
            if ($requirement->faculty && $requirement->faculty !== $academic->faculty) {
                continue;
            }
            // Department
            if ($requirement->department && $requirement->department !== $academic->department) {
                continue;
            }
            // Current level
            $level = studentCurrentLevel($academic);
            // Single level requirement
            if ($requirement->start_level && !$requirement->end_level) {
                if ($level != $requirement->start_level) {
                    continue;
                }
            }
            // Level range requirement
            if ($requirement->start_level && $requirement->end_level) {
                if ($level < $requirement->start_level || $level > $requirement->end_level) {
                    continue;
                }
            }
            // Student passed this requirement
            return ['qualified' => true, 'message' => 'Qualified',];
        }
        return ['qualified' => false, 'message' => 'You do not meet the eligibility requirements for this position',];
    }

    // total qualified voters
    function totalQualifiedVoters(Election $election): int
    {
        $groups = $election->voterGroups;

        // No restrictions = all students
        if ($groups->isEmpty()) {
            return MemberAcademicInfo::query()
                ->where('academic_status', 'student')
                ->count();
        }

        return MemberAcademicInfo::query()
            ->where('academic_status', 'student')
            ->where(function ($query) use ($groups) {

                foreach ($groups as $group) {

                    $query->orWhere(function ($q) use ($group) {

                        if ($group->faculty) {
                            $q->where('faculty', $group->faculty);
                        }

                        if ($group->department) {
                            $q->where('department', $group->department);
                        }

                        if ($group->program) {
                            $q->where('program', $group->program);
                        }

                        if ($group->program_type) {
                            $q->where('program_type', $group->program_type);
                        }

                        if ($group->admission_session) {
                            $q->where(
                                'admission_session',
                                $group->admission_session
                            );
                        }
                    });
                }
            })
            ->distinct('username')
            ->count('username');
    }
}

// can this user vote
function canUserVoteInElection(Election $election, User $user): bool
{
    $academicInfo = $user->memberAcademicInfo;

    // No academic information = cannot vote
    if (!$academicInfo) {
        return false;
    }

    // Only current students can vote
    if ($academicInfo->academic_status !== 'student') {
        return false;
    }

    // No qualification conditions = all students can vote
    $groups = $election->voterGroups;

    if ($groups->isEmpty()) {
        return true;
    }

    // User must match at least ONE qualification group
    foreach ($groups as $group) {

        // Faculty condition
        if (
            !empty($group->faculty) &&
            $academicInfo->faculty !== $group->faculty
        ) {
            continue;
        }

        // Department condition
        if (
            !empty($group->department) &&
            $academicInfo->department !== $group->department
        ) {
            continue;
        }

        // Program condition
        if (
            !empty($group->program) &&
            $academicInfo->program !== $group->program
        ) {
            continue;
        }

        // Program type condition
        if (
            !empty($group->program_type) &&
            $academicInfo->program_type !== $group->program_type
        ) {
            continue;
        }

        // Admission session condition
        if (
            !empty($group->admission_session) &&
            $academicInfo->admission_session != $group->admission_session
        ) {
            continue;
        }

        // All conditions in this group matched
        return true;
    }

    return false;
}


if (!function_exists('financialClaimPendingApproval')) {
    function financialClaimPendingApproval()
    {
        return FinancialClaim::where('approval_status', 'pending')
            ->count();
    }
}

if (!function_exists('countPendingInvoicesForMember')) {
    function countPendingInvoicesForMember()
    {
        return Invoice::where('registered_payer', Auth::user()->username)->where('status', 'pending')
            ->count();
    }
}


##################################################
if (!function_exists('resolveAcademicContextForDue')) {
    function resolveAcademicContextForDue(
        \App\Models\Members\MemberAcademicInfo $academicInfo,
        \App\Models\Finance\Dues\AssociationDue $due
    ): array {

        $dueYear = (int) $due->academic_year;

        // ── Check current program ──────────────────────────────────────
        // A due belongs to the current program if its academic_year falls
        // within the member's current admission → graduation range
        $currentStart = (int) $academicInfo->admission_session;
        $currentEnd   = (int) ($academicInfo->graduation_session
            ?? ($currentStart + ($academicInfo->theProgram?->duration ?? 0) - 1));

        if ($dueYear >= $currentStart && $dueYear <= $currentEnd) {
            return [
                'source'             => 'current',
                'matric_number'      => $academicInfo->matric_number,
                'program'            => $academicInfo->theProgram?->name,
                'program_type'       => $academicInfo->programType?->type_name,
                'faculty'            => $academicInfo->theFaculty?->name,
                'department'         => $academicInfo->theDepartment?->name,
                'admission_session'  => $academicInfo->admission_session,
                'graduation_session' => $academicInfo->graduation_session,
                'reAdmission'        => null,
            ];
        }

        // ── Check past programs ────────────────────────────────────────
        // approvedReAdmissions is already eager loaded in the controller
        // so no extra query here — just iterate the loaded collection
        $reAdmissions = $academicInfo->approvedReAdmissions
            ->sortByDesc('previous_admission_session'); // most recent first

        foreach ($reAdmissions as $reAdmission) {

            if (
                empty($reAdmission->previous_admission_session) ||
                empty($reAdmission->previous_graduation_session)
            ) {
                continue;
            }

            $start = (int) $reAdmission->previous_admission_session;
            $end   = (int) $reAdmission->previous_graduation_session;

            if ($dueYear >= $start && $dueYear <= $end) {
                return [
                    'source'             => 'readmission',
                    'matric_number'      => $reAdmission->previous_matric_number
                        ?? $academicInfo->matric_number,
                    'program'            => $reAdmission->previousProgram?->name,
                    'program_type'       => $reAdmission->previousProgramType?->type_name,
                    'faculty'            => $reAdmission->previousFaculty?->name,
                    'department'         => $reAdmission->previousDepartment?->name,
                    'admission_session'  => $reAdmission->previous_admission_session,
                    'graduation_session' => $reAdmission->previous_graduation_session,
                    'reAdmission'        => $reAdmission,
                ];
            }
        }

        // ── Fallback — should never reach here ─────────────────────────
        return [
            'source'             => 'current',
            'matric_number'      => $academicInfo->matric_number,
            'program'            => $academicInfo->theProgram?->name,
            'program_type'       => $academicInfo->programType?->type_name,
            'faculty'            => $academicInfo->theFaculty?->name,
            'department'         => $academicInfo->theDepartment?->name,
            'admission_session'  => $academicInfo->admission_session,
            'graduation_session' => $academicInfo->graduation_session,
            'reAdmission'        => null,
        ];
    }
}

if (!function_exists('emailSafeLogo')) {
    function emailSafeLogo(string $backgroundColor = '#f0f0f0'): string
    {
        $defaultSettings = basicSystemInfo();
        $logoPath = null;
        $tenantId = tenantId();

        if (
            $defaultSettings &&
            $defaultSettings->association_logo &&
            $tenantId &&
            file_exists(public_path('assets/tenant_' . $tenantId . '/uploads/tenantProfile/' . $defaultSettings->association_logo))
        ) {
            $logoPath = public_path('assets/tenant_' . $tenantId . '/uploads/tenantProfile/' . $defaultSettings->association_logo);
        } else {
            $logoPath = public_path('system/images/transcend_logo.webp');
        }

        $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
        $source = $ext === 'webp' ? imagecreatefromwebp($logoPath) : imagecreatefrompng($logoPath);

        $width = imagesx($source);
        $height = imagesy($source);

        $flattened = imagecreatetruecolor($width, $height);

        // Parse hex colour into RGB
        $hex = ltrim($backgroundColor, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $bgColor = imagecolorallocate($flattened, $r, $g, $b);

        imagefill($flattened, 0, 0, $bgColor);
        imagecopy($flattened, $source, 0, 0, 0, 0, $width, $height);

        $outputDir = public_path('system/images/email-safe');
        if (!is_dir($outputDir)) {
            @mkdir($outputDir, 0775, true);
        }

        $outputPath = $outputDir . '/logo-' . ($tenantId ?? 'default') . '-' . $hex . '.png';
        imagepng($flattened, $outputPath);

        imagedestroy($source);
        imagedestroy($flattened);

        return asset('system/images/email-safe/logo-' . ($tenantId ?? 'default') . '-' . $hex . '.png');
    }
}

if (!function_exists('emailFromName')) {
    function emailFromName(): string
    {
        $settings = basicSystemInfo();

        if (!$settings) {
            return config('app.name');
        }

        $name = $settings->association_acronym ?? config('app.name');

        if (!empty($settings->school_acronym)) {
            $name .= ' | ' . $settings->school_acronym;
        }

        return $name;
    }
}
