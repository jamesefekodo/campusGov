<?php

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
