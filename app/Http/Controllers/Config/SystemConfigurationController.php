<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\BasicSystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class SystemConfigurationController extends Controller
{
    public function viewSystemSettings(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('basic_system_config.read'),
            403
        );

        Artisan::call('config:clear');

        $appName = env('APP_NAME', '');
        $appUrl = env('APP_URL', 'http://localhost');
        $timezone = env('APP_TIMEZONE', 'Etc/GMT');



        $MAIL_MAILER = env('MAIL_MAILER', '');
        $MAIL_HOST = env('MAIL_HOST', '');
        $MAIL_PORT = env('MAIL_PORT', '');
        $MAIL_USERNAME = env('MAIL_USERNAME', '');
        $MAIL_PASSWORD = env('MAIL_PASSWORD', '');
        $MAIL_ENCRYPTION = env('MAIL_ENCRYPTION', '');
        $MAIL_FROM_ADDRESS = env('MAIL_FROM_ADDRESS', '');

        $basicSystemSetting = BasicSystemSetting::first()?->maintenance_mode ?? false;

        $data = [
            'appName' => $appName,
            'appUrl' => $appUrl,
            'timezone' => $timezone,
            'MAIL_MAILER' => $MAIL_MAILER,
            'MAIL_HOST' => $MAIL_HOST,
            'MAIL_PORT' => $MAIL_PORT,
            'MAIL_USERNAME' => $MAIL_USERNAME,
            'MAIL_PASSWORD' => $MAIL_PASSWORD,
            'MAIL_ENCRYPTION' => $MAIL_ENCRYPTION,
            'MAIL_FROM_ADDRESS' => $MAIL_FROM_ADDRESS,
            'basicSystemSetting' => $basicSystemSetting
        ];

        return view('users.admin.config.basic.basicConfig', compact('data'));
    }
}
