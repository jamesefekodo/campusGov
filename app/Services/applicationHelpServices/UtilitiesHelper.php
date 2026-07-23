<?php

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\ProgramDurationOverride;
use App\Models\Config\BasicSystemSetting;
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



if (!function_exists('associationLogo')) {
    function associationLogo()
    {
        $defaultSettings = BasicSystemSetting::first();

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
        $defaultSettings = BasicSystemSetting::first();

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

if (!function_exists('basicSystemInfo')) {
    function basicSystemInfo()
    {
        return BasicSystemSetting::first();
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

// if (!function_exists('currentLevel')) {
//     function currentLevel($program, int $currentLevel): string
//     {
//         $value = match ($program->numbering) {
//             'roman'    => toRoman($currentLevel),
//             'weighted' => $currentLevel * $program->increment,
//             default    => $currentLevel,
//         };

//         return str_replace('{value}', $value, $program->level_format);
//     }
// }

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

// usage
// getLevel($program, 1); // ND I
// getLevel($program, 2); // ND II
// getLevel($program, 3); // ND III

// getLevel($bscProgram, 1); // 100L
// getLevel($bscProgram, 2); // 200L

// getLevel($mscProgram, 2); // MSc II


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

if (!function_exists('getProgramDuration')) {

    function getProgramDuration($program, $programType = null, $department = null): int
    {
        if (!$program) {
            return 4;
        }

        $override = ProgramDurationOverride::query()
            ->where('program_slug', $program->slug)
            ->where('is_active', true)
            ->where(function ($query) use ($programType, $department) {

                if ($department) {
                    $query->where('department_slug', $department->slug);
                }

                if ($programType) {
                    $query->orWhere('program_type_slug', $programType->slug);
                }

                $query->orWhere(function ($q) {
                    $q->whereNull('department_slug')
                        ->whereNull('program_type_slug');
                });
            })
            ->orderByRaw("
                CASE
                    WHEN department_slug IS NOT NULL THEN 1
                    WHEN program_type_slug IS NOT NULL THEN 2
                    ELSE 3
                END
            ")
            ->first();


        return $override?->duration ?? $program->duration;
    }
}

if (!function_exists('isStillWithinProgramDuration')) {
    //   Check if student is still within normal program years
    function isStillWithinProgramDuration($admissionSession, $program, $programType = null, $department = null): bool
    {
        if (!$admissionSession || !$program) return true;

        $duration = getProgramDuration($program, $programType, $department);

        // Extract year from admission session (e.g., "2022/2023" → 2022)
        $startYear = (int) explode('/', $admissionSession)[0];
        $currentYear = now()->year;

        $yearsElapsed = $currentYear - $startYear;

        return $yearsElapsed < $duration;
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


if (!function_exists('countMemberProfilesAwaitingVerification')) {
    function countMemberProfilesAwaitingVerification()
    {
        return User::where('account_type', 2)
            ->where('account_status', 1)
            ->count();
    }
}
