<?php

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\ProgramDurationOverride;
use App\Models\Config\BasicSystemSetting;
use App\Models\Finance\Settings\BankAccount;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\ManualPaymentMethod;
use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;



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
                    'system/images/' .
                        $defaultSettings->association_logo,
                ),
            )
        ) {
            $logo = asset('system/images/' . $defaultSettings->association_logo);
        } else {
            $logo = asset('system/images/transcend_logo.webp');
        }
        return $logo;
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
                    'system/images/' .
                        $defaultSettings->school_logo,
                ),
            )
        ) {
            $logo = asset('system/images/' . $defaultSettings->school_logo);
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
if (!function_exists('studentLevelAtDue')) {
    function studentLevelAtDue($academic, $due): int
    {
        if (!$academic || !$due || !$academic->admission_session) {
            return 1;
        }

        return ($due->academic_year - (int) $academic->admission_session) + 1;
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

