<?php

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\Config\BasicSystemSetting;
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

if (!function_exists('currentLevel')) {
    function currentLevel($program, int $currentLevel): string
    {
        $value = match ($program->numbering) {
            'roman'    => toRoman($currentLevel),
            'weighted' => $currentLevel * $program->increment,
            default    => $currentLevel,
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

// usage
// getLevel($program, 1); // ND I
// getLevel($program, 2); // ND II
// getLevel($program, 3); // ND III

// getLevel($bscProgram, 1); // 100L
// getLevel($bscProgram, 2); // 200L

// getLevel($mscProgram, 2); // MSc II
