<?php

namespace App\Http\Controllers\SystemSettings\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class SystemSetupController extends Controller
{
    protected function checkInstallationStatus()
    {
        if (File::exists(storage_path('installed'))) {
            $message = "This system is already installed.";
            $repeatedMessage = str_repeat($message, 300);

            return response($repeatedMessage, 403);
        }
        return null;
    }
    public function installSystem()
    {
        // Check if system is installed
        $response = $this->checkInstallationStatus();
        if ($response) {
            return $response;
        }

        $systemInfo = getSystemInfo();
        $data = [
            'systemInfo' => $systemInfo,
        ];
        return view('systemSettings.setup.installSystem', compact('data'));
    }
}
