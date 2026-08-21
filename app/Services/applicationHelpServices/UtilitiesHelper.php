<?php

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\ProgramDurationOverride;
use App\Models\Config\BasicSystemSetting;
use App\Models\Elections\Election;
use App\Models\Elections\ElectionCandidateApplication;
use App\Models\Finance\Settings\BankAccount;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\ManualPaymentMethod;
use App\Models\Finance\Transactions\PaymentRegularization;
use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Finance\Transactions\PaymentWaiver;
use App\Models\Members\MemberReAdmissionApplication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;



if (!function_exists('getUserType')) {
    function getUserType()
    {
        if (!Auth::check()) {
            return 'guest';
        }

        return match ((int) Auth::user()->account_type) {
            0 => 'root',
            1 => 'admin',
            2 => 'member',
            default => 'guest',
        };
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
        return ElectronicPaymentMethod::where('is_active', true)->get();
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
    function studentLevelAtDue($academic, $due): int
    {
        if (!$academic || !$due) {
            return 1;
        }

        /*
         * Check whether this due belongs to a previous programme.
         */
        $pastProgram = $academic->approvedReAdmissions()
            ->get()
            ->first(function ($reAdmission) use ($due) {

                return $due->academic_year >=
                    (int) $reAdmission->previous_admission_session

                    &&

                    $due->academic_year <=
                    (int) $reAdmission->previous_graduation_session;
            });

        /*
         * If the due belongs to a previous programme,
         * calculate the level using that programme's
         * admission session.
         */
        if ($pastProgram) {
            return max(
                1,
                (int) $due->academic_year -
                    (int) $pastProgram->previous_admission_session +
                    1
            );
        }

        /*
         * Otherwise, it is a current-programme due.
         */
        if (!$academic->admission_session) {
            return 1;
        }

        return max(
            1,
            (int) $due->academic_year -
                (int) $academic->admission_session +
                1
        );
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
}
