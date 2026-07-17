<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\BasicSystemSetting;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
        $timezones = DateTimeZone::listIdentifiers();




        $MAIL_MAILER = env('MAIL_MAILER', '');
        $MAIL_HOST = env('MAIL_HOST', '');
        $MAIL_PORT = env('MAIL_PORT', '');
        $MAIL_USERNAME = env('MAIL_USERNAME', '');
        $MAIL_PASSWORD = env('MAIL_PASSWORD', '');
        $MAIL_ENCRYPTION = env('MAIL_ENCRYPTION', '');
        $MAIL_FROM_ADDRESS = env('MAIL_FROM_ADDRESS', '');

        $basicSystemSetting = BasicSystemSetting::first();

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
            'basicSystemSetting' => $basicSystemSetting,
            'timezones' => $timezones,
        ];

        return view('users.admin.config.basic.basicConfig', compact('data'));
    }
    public function updateMaintenanceMode(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('basic_system_config.update'),
            403
        );

        $request->validate([
            'maintenance_mode' => 'required',
        ]);

        $basicSystemSetting = BasicSystemSetting::first();
        if (!$basicSystemSetting) {
            // $basicSystemSetting = new BasicSystemSetting();
            return redirect()->back()->with('error', 'Basic system settings not found.');
        }

        $basicSystemSetting->update([
            'maintenance_mode' => $request->boolean('maintenance_mode'),
        ]);

        return redirect()->back()->with('success', 'Maintenance mode updated successfully.');
    }
     protected function updateEnv(array $data)
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return false;
        }

        $env = file_get_contents($path);

        foreach ($data as $key => $value) {

            $escapedKey = preg_quote($key, '/');

            $pattern = "/^{$escapedKey}=.*/m";

            $replacement = $key . '="' . addslashes($value) . '"';

            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, $replacement, $env);
            } else {
                $env .= "\n" . $replacement;
            }
        }

        file_put_contents($path, $env);

        return true;
    }


    public function updateBasicSystemInfoUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('basic_system_config.update'),
            403
        );

        $basicSystemSetting = BasicSystemSetting::first();
        if (!$basicSystemSetting) {
            return redirect()->back()->with('error', 'Basic system settings not found.');
        }


        $request->validate([
            'APP_NAME' => 'required|string',
            'APP_URL' => 'required|string',
            'timezone' => 'required|string',
            'association_name' => 'required|string',
            'association_acronym' => 'required|string',
            'association_slogan' => 'required|string',
            'description' => 'required',
            'operational_level' => 'required|string',
            'school_name' => 'required|string',
            'school_acronym' => 'required|string',
            'association_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3145728',
            'school_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3145728',
            'primary_colour' => 'nullable',
            'secondary_colour' => 'nullable',
            'tertiary_colour' => 'nullable',

        ]);

        $basicSystemSetting->association_name = trim($request->association_name);
        $basicSystemSetting->association_acronym = trim($request->association_acronym);
        $basicSystemSetting->association_slogan = trim($request->association_slogan);
        $basicSystemSetting->description = $request->description;
        $basicSystemSetting->operational_level = trim($request->operational_level);
        $basicSystemSetting->school_name = trim($request->school_name);
        $basicSystemSetting->school_acronym = trim($request->school_acronym);
        $basicSystemSetting->primary_colour = $request->primary_colour;
        $basicSystemSetting->secondary_colour = $request->secondary_colour;
        $basicSystemSetting->tertiary_colour = $request->tertiary_colour;


        if ($request->hasFile('association_logo')) {

            $file = $request->file('association_logo');
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $uploadPath = public_path('system/images');
            File::ensureDirectoryExists($uploadPath);

            // Base filename
            $baseName = strtolower($basicSystemSetting->association_name ?: config('app.name'));

            $baseName = Str::of($baseName)
                ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                ->trim('_')
                ->toString();
            $fallbackName = $baseName . '.' . $ext;
            $counter = 1;

            // Delete existing file(s) with same base name regardless of extension
            foreach (glob($uploadPath . '/' . $baseName . '.*') as $existingFile) {
                if (file_exists($existingFile)) {
                    unlink($existingFile);
                }
            }

            // Avoid overwriting existing files
            while (file_exists($uploadPath . '/' . $fallbackName)) {
                $fallbackName = $baseName . '-' . $counter . '.' . $ext;
                $counter++;
            }

            $fallbackPath = $uploadPath . '/' . $fallbackName;

            /*
            |--------------------------------------------------------------------------
            | GD + WebP processing
            |--------------------------------------------------------------------------
            */
            $processed = false;

            if (function_exists('imagewebp') && function_exists('imagecreatetruecolor')) {

                $tmp = $file->getPathname();

                $src = match ($mime) {
                    'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($tmp) : null,
                    'image/png'  => function_exists('imagecreatefrompng')  ? @imagecreatefrompng($tmp)  : null,
                    'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : null,
                    default => null,
                };

                if ($src) {
                    $width  = imagesx($src);
                    $height = imagesy($src);

                    // Resize if needed
                    $maxSize = 800;
                    $scale = min(1, $maxSize / max($width, $height));

                    if ($scale < 1) {
                        $newW = (int)($width * $scale);
                        $newH = (int)($height * $scale);
                        $dst = imagecreatetruecolor($newW, $newH);

                        // Preserve transparency
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                        imagefill($dst, 0, 0, $transparent);

                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
                        imagedestroy($src);
                        $src = $dst;
                    }

                    // WebP conversion with quality loop
                    $webpName = $baseName . '.webp';
                    $webpCounter = 1;
                    $webpPath = $uploadPath . '/' . $webpName;

                    while (file_exists($webpPath)) {
                        $webpName = $baseName . '-' . $webpCounter . '.webp';
                        $webpPath = $uploadPath . '/' . $webpName;
                        $webpCounter++;
                    }

                    $quality = 90;
                    do {
                        imagewebp($src, $webpPath, $quality);
                        clearstatcache();

                        if (filesize($webpPath) <= 1048576) {
                            $processed = true;
                            $basicSystemSetting->association_logo = $webpName;
                            imagedestroy($src);
                            break;
                        }

                        $quality -= 5;
                    } while ($quality >= 40);

                    if (!$processed) {
                        @unlink($webpPath);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback: move original file
            |--------------------------------------------------------------------------
            */
            if (!$processed) {
                $file->move($uploadPath, $fallbackName);
                $basicSystemSetting->association_logo = $fallbackName;
            }
        }

        if ($request->hasFile('school_logo')) {

            $file2 = $request->file('school_logo');
            $mime2 = $file2->getMimeType();
            $ext2  = strtolower($file2->getClientOriginalExtension());

            $uploadPath2 = public_path('system/images');
            File::ensureDirectoryExists($uploadPath2);

            // Base filename
            $baseName2 = strtolower($basicSystemSetting->school_name ?: config('app.name'));

            $baseName2 = Str::of($baseName2)
                ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                ->trim('_')
                ->toString();
            $fallbackName2 = $baseName2 . '.' . $ext2;

            // Delete existing file(s) with same base name regardless of extension
            foreach (glob($uploadPath2 . '/' . $baseName2 . '.*') as $existingFile2) {
                if (file_exists($existingFile2)) {
                    unlink($existingFile2);
                }
            }

            $counter2 = 1;

            // Avoid overwriting existing files
            while (file_exists($uploadPath2 . '/' . $fallbackName2)) {
                $fallbackName2 = $baseName2 . '-' . $counter2 . '.' . $ext2;
                $counter2++;
            }

            $fallbackPath2 = $uploadPath2 . '/' . $fallbackName2;

            /*
            |--------------------------------------------------------------------------
            | GD + WebP processing
            |--------------------------------------------------------------------------
            */
            $processed2 = false;

            if (function_exists('imagewebp') && function_exists('imagecreatetruecolor')) {

                $tmp2 = $file2->getPathname();

                $src = match ($mime2) {
                    'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($tmp2) : null,
                    'image/png'  => function_exists('imagecreatefrompng')  ? @imagecreatefrompng($tmp2)  : null,
                    'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp2) : null,
                    default => null,
                };

                if ($src) {
                    $width  = imagesx($src);
                    $height = imagesy($src);

                    // Resize if needed
                    $maxSize = 800;
                    $scale = min(1, $maxSize / max($width, $height));

                    if ($scale < 1) {
                        $newW = (int)($width * $scale);
                        $newH = (int)($height * $scale);
                        $dst = imagecreatetruecolor($newW, $newH);

                        // Preserve transparency
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                        imagefill($dst, 0, 0, $transparent);

                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
                        imagedestroy($src);
                        $src = $dst;
                    }

                    // WebP conversion with quality loop
                    $webpName2 = $baseName2 . '.webp';
                    $webpCounter = 1;
                    $webpPath2 = $uploadPath2 . '/' . $webpName2;

                    while (file_exists($webpPath2)) {
                        $webpName2 = $baseName2 . '-' . $webpCounter . '.webp';
                        $webpPath2 = $uploadPath2 . '/' . $webpName2;
                        $webpCounter++;
                    }

                    $quality = 90;
                    do {
                        imagewebp($src, $webpPath2, $quality);
                        clearstatcache();

                        if (filesize($webpPath2) <= 1048576) {
                            $processed = true;
                            $basicSystemSetting->school_logo = $webpName2;
                            imagedestroy($src);
                            break;
                        }

                        $quality -= 5;
                    } while ($quality >= 40);

                    if (!$processed2) {
                        @unlink($webpPath2);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback: move original file
            |--------------------------------------------------------------------------
            */
            if (!$processed2) {
                $file2->move($uploadPath2, $fallbackName2);
                $basicSystemSetting->school_logo = $fallbackName2;
            }
        }
        $basicSystemSetting->save();


        $data = [
            'APP_NAME' => $request->APP_NAME,
            'APP_TIMEZONE' => $request->timezone,
            'APP_URL' => $request->APP_URL,
        ];


        if ($this->updateEnv($data)) {
            // Clear the cached configuration to apply the changes immediately
            Artisan::call('config:clear');
            Artisan::call('config:cache');

            return redirect()->back()->with('success', 'Updated Successfully!');
        } else {
            return redirect()->back()->with('error', 'Update Failed!');
        }
    }
    public function updateEmailConfigSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('basic_system_config.update'),
            403
        );

        $basicSystemSetting = BasicSystemSetting::first();
        if (!$basicSystemSetting) {
            // $basicSystemSetting = new BasicSystemSetting();
            return redirect()->back()->with('error', 'Basic system settings not found.');
        }

        $validated = $request->validate([
            'MAIL_MAILER' => 'required|string',
            'MAIL_HOST' => 'required|string',
            'MAIL_PORT' => 'required|integer',
            'MAIL_USERNAME' => 'required|string',
            'MAIL_PASSWORD' => 'required|string',
            'MAIL_ENCRYPTION' => 'nullable|in:tls,ssl',
            'MAIL_FROM_ADDRESS' => 'required|email',
        ]);

        $envUpdate = $this->updateEnv([
            'MAIL_MAILER' => $request->MAIL_MAILER,
            'MAIL_HOST' => $request->MAIL_HOST,
            'MAIL_PORT' => $request->MAIL_PORT,
            'MAIL_USERNAME' => $request->MAIL_USERNAME,
            'MAIL_PASSWORD' => $request->MAIL_PASSWORD,
            'MAIL_ENCRYPTION' => $request->MAIL_ENCRYPTION ?: null,
            'MAIL_FROM_ADDRESS' => $request->MAIL_FROM_ADDRESS
        ]);


        if ($envUpdate) {
            // Clear the cached configuration to apply the changes immediately
            Artisan::call('config:clear');
            Artisan::call('config:cache');

            return redirect()->back()->with('success', 'Updated Successfully!');
        } else {
            return back()->with('error', 'Failed to update mail configuration.');
        }
    }
}
