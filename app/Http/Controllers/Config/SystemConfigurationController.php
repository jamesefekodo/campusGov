<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\AccessControl\Role;
use App\Models\Config\AccessControl\RoleUser;
use App\Models\Config\BasicSystemSetting;
use App\Models\Config\HomepageSlider;
use App\Models\Config\SeoDetail;
use App\Models\Members\MemberAcademicInfo;
use App\Models\User;
use DateTime;
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
        $basicSystemSetting = basicSystemInfo();

        Artisan::call('config:clear');

        $appName = env('APP_NAME', '');
        $appUrl = env('APP_URL', 'http://localhost');

        $timezone = env('APP_TIMEZONE', 'Etc/GMT');
        $timezones = DateTimeZone::listIdentifiers();


        usort($timezones, function ($a, $b) {
            $offsetA = (new DateTime('now', new DateTimeZone($a)))->getOffset();
            $offsetB = (new DateTime('now', new DateTimeZone($b)))->getOffset();

            if ($offsetA === $offsetB) {
                return strcmp($a, $b); // sort alphabeticaly within the same offset
            }

            return $offsetA <=> $offsetB;
        });





        $MAIL_MAILER = env('MAIL_MAILER', '');
        $MAIL_HOST = env('MAIL_HOST', '');
        $MAIL_PORT = env('MAIL_PORT', '');
        $MAIL_USERNAME = env('MAIL_USERNAME', '');
        $MAIL_PASSWORD = env('MAIL_PASSWORD', '');
        $MAIL_ENCRYPTION = env('MAIL_ENCRYPTION', '');
        $MAIL_FROM_ADDRESS = env('MAIL_FROM_ADDRESS', '');


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
    public function updateAcademicYear(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('basic_system_config.update'),
            403
        );

        $basicSystemSetting = BasicSystemSetting::firstOrFail();

        switch ($request->direction) {

            case 'forward':
                $basicSystemSetting->current_academic_year++;
                break;

            case 'backward':
                $basicSystemSetting->current_academic_year--;
                break;

            default:
                return back()->with('error', 'Invalid direction.');
        }

        $basicSystemSetting->save();

        MemberAcademicInfo::syncStudentStatuses();


        // Revoke per-session roles/permissions
        $perSessionRoleIds = Role::where('assignment_scope', 'per_session')->pluck('id');

        RoleUser::whereIn('role_id', $perSessionRoleIds)->delete();

        // Disable admin accounts
        User::where('account_type', 1)
            ->where('account_status', 2)
            ->update(['account_status' => 3]);

        return back()->with(
            'success',
            'Academic year updated to ' . $basicSystemSetting->current_academic_year
        );
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
            // 'APP_NAME' => 'required|string',
            // 'APP_URL' => 'required|string',
            // 'timezone' => 'required|string',
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
            'is_polytechnic' => 'required',
            'school_founding_year' => 'required',
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
        $basicSystemSetting->is_polytechnic = $request->is_polytechnic;
        $basicSystemSetting->school_founding_year = trim($request->school_founding_year);


        if ($request->hasFile('association_logo')) {

            $file = $request->file('association_logo');
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $uploadPath = public_path('assets/tenant_' . tenant()->id . '/uploads/tenantProfile');
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

            $uploadPath2 = public_path('assets/tenant_' . tenant()->id . '/uploads/tenantProfile');
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


        // $data = [
        //     'APP_NAME' => $request->APP_NAME,
        //     'APP_TIMEZONE' => $request->timezone,
        //     'APP_URL' => $request->APP_URL,
        // ];


        // if ($this->updateEnv($data)) {
        //     // Clear the cached configuration to apply the changes immediately
        //     Artisan::call('config:clear');
        //     Artisan::call('config:cache');

        //     return redirect()->back()->with('success', 'Updated Successfully!');
        // } else {
        //     return redirect()->back()->with('error', 'Update Failed!');
        // }

        return redirect()->back()->with('success', 'Updated Successfully!');
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
    public function homepageSliders()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('basic_system_config.read'),
            403
        );

        $sliders = HomepageSlider::where('tenant_id', tenant()->id)->orderBy('order')->get();
        $data = [
            'sliders' => $sliders,
        ];

        return view('users.admin.config.sliders.sliders', compact('data'));
    }
    public function addNewSliderSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('basic_system_config.read'),
            403
        );

        $request->validate([
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2097152',
            'header' => 'required|string',
            'order' => 'required',
            'description' => 'nullable|string',

        ]);

        $orderExists = HomepageSlider::where('tenant_id', tenant()->id)->where('order', $request->order)->first();
        if ($orderExists) {
            return back()->with('error', 'Order already exists!');
        }
        $totalExistingSliders = HomepageSlider::count();
        if ($totalExistingSliders >= 7) {
            return back()->with('error', 'You can only add up to 7 slider images!');
        }
        $slider = new HomepageSlider();
        $slider->header = trim($request->header);
        $slider->order = trim($request->order);
        $slider->description = trim($request->description);


        if ($request->hasFile('background_image')) {

            $file = $request->file('background_image');
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $uploadPath = public_path('assets/tenant_' . tenant()->id . '/uploads/sliders');
            File::ensureDirectoryExists($uploadPath);

            // Base filename
            $baseName = Str::slug($request->header, '_');

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
                            $slider->background_image = $webpName;
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
                $slider->background_image = $fallbackName;
            }
            $slider->save();
        }
        return redirect()->back()->with('success', 'Done!');
    }
    // public function editSliderSubmit($id, Request $request)
    // {
    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();

    //     abort_unless(
    //         $user->hasPermission('basic_system_config.update'),
    //         403
    //     );

    //     $request->validate([
    //         'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2097152',
    //         'header' => 'required|string',
    //         'order' => 'required',
    //         'description' => 'nullable|string',
    //     ]);

    //     $slider = HomepageSlider::where('id', $id)->first();
    //     if (!$slider) {
    //         return redirect()->back()->with('error', 'Records not found!');
    //     }

    //     $slider->update([
    //         'header' => trim($request->header),
    //         'order' => trim($request->order),
    //         'description' => trim($request->description),
    //     ]);


    //     if ($request->hasFile('background_image')) {

    //         $file = $request->file('background_image');

    //         $mime = $file->getMimeType();
    //         $ext  = strtolower($file->getClientOriginalExtension());

    //         $uploadPath = public_path(
    //             'assets/tenant_' . tenant()->id . '/uploads/sliders'
    //         );

    //         File::ensureDirectoryExists($uploadPath);

    //         /*
    // |--------------------------------------------------------------------------
    // | Generate base filename
    // |--------------------------------------------------------------------------
    // */
    //         $baseName = Str::slug($slider->header, '_');

    //         $baseName = Str::of($baseName)
    //             ->replaceMatches('/[^A-Za-z0-9]+/', '_')
    //             ->trim('_')
    //             ->toString();

    //         /*
    // |--------------------------------------------------------------------------
    // | Delete previous slider image(s)
    // |--------------------------------------------------------------------------
    // */
    //         foreach (glob($uploadPath . '/' . $baseName . '.*') as $existingFile) {
    //             if (is_file($existingFile)) {
    //                 @unlink($existingFile);
    //             }
    //         }

    //         /*
    // |--------------------------------------------------------------------------
    // | Original uploaded temporary file
    // |--------------------------------------------------------------------------
    // */
    //         $tmp = $file->getPathname();

    //         /*
    // |--------------------------------------------------------------------------
    // | GD + WebP processing
    // |--------------------------------------------------------------------------
    // */
    //         $processed = false;

    //         if (
    //             function_exists('imagewebp') &&
    //             function_exists('imagecreatetruecolor')
    //         ) {

    //             $src = match ($mime) {

    //                 'image/jpeg' =>
    //                 function_exists('imagecreatefromjpeg')
    //                     ? @imagecreatefromjpeg($tmp)
    //                     : null,

    //                 'image/png' =>
    //                 function_exists('imagecreatefrompng')
    //                     ? @imagecreatefrompng($tmp)
    //                     : null,

    //                 'image/webp' =>
    //                 function_exists('imagecreatefromwebp')
    //                     ? @imagecreatefromwebp($tmp)
    //                     : null,

    //                 default => null,
    //             };

    //             if ($src) {

    //                 $width  = imagesx($src);
    //                 $height = imagesy($src);

    //                 /*
    //         |--------------------------------------------------------------------------
    //         | Resize if larger than 800px
    //         |--------------------------------------------------------------------------
    //         */
    //                 $maxSize = 800;

    //                 $scale = min(
    //                     1,
    //                     $maxSize / max($width, $height)
    //                 );

    //                 if ($scale < 1) {

    //                     $newW = (int) round($width * $scale);
    //                     $newH = (int) round($height * $scale);

    //                     $dst = imagecreatetruecolor($newW, $newH);

    //                     /*
    //             |--------------------------------------------------------------------------
    //             | Preserve transparency
    //             |--------------------------------------------------------------------------
    //             */
    //                     imagealphablending($dst, false);
    //                     imagesavealpha($dst, true);

    //                     $transparent = imagecolorallocatealpha(
    //                         $dst,
    //                         0,
    //                         0,
    //                         0,
    //                         127
    //                     );

    //                     imagefill(
    //                         $dst,
    //                         0,
    //                         0,
    //                         $transparent
    //                     );

    //                     imagecopyresampled(
    //                         $dst,
    //                         $src,
    //                         0,
    //                         0,
    //                         0,
    //                         0,
    //                         $newW,
    //                         $newH,
    //                         $width,
    //                         $height
    //                     );

    //                     imagedestroy($src);

    //                     $src = $dst;
    //                 }

    //                 /*
    //         |--------------------------------------------------------------------------
    //         | WebP output
    //         |--------------------------------------------------------------------------
    //         */
    //                 $webpName = $baseName . '.webp';
    //                 $webpPath = $uploadPath . '/' . $webpName;

    //                 $quality = 90;

    //                 do {

    //                     // Remove previous attempt
    //                     if (file_exists($webpPath)) {
    //                         @unlink($webpPath);
    //                     }

    //                     $success = imagewebp(
    //                         $src,
    //                         $webpPath,
    //                         $quality
    //                     );

    //                     clearstatcache(true, $webpPath);

    //                     if (
    //                         $success &&
    //                         file_exists($webpPath) &&
    //                         filesize($webpPath) <= 1048576
    //                     ) {

    //                         $processed = true;

    //                         $slider->background_image = $webpName;

    //                         break;
    //                     }

    //                     $quality -= 5;
    //                 } while ($quality >= 40);

    //                 /*
    //         |--------------------------------------------------------------------------
    //         | Cleanup GD resource
    //         |--------------------------------------------------------------------------
    //         */
    //                 imagedestroy($src);

    //                 /*
    //         |--------------------------------------------------------------------------
    //         | Remove failed WebP
    //         |--------------------------------------------------------------------------
    //         */
    //                 if (!$processed && file_exists($webpPath)) {
    //                     @unlink($webpPath);
    //                 }
    //             }
    //         }

    //         /*
    // |--------------------------------------------------------------------------
    // | Fallback: save original uploaded file
    // |--------------------------------------------------------------------------
    // */
    //         if (!$processed) {

    //             $fileName = $baseName . '.' . $ext;

    //             $file->move(
    //                 $uploadPath,
    //                 $fileName
    //             );

    //             $slider->background_image = $fileName;
    //         }

    //         /*
    // |--------------------------------------------------------------------------
    // | Save database record
    // |--------------------------------------------------------------------------
    // */
    //         $slider->save();
    //     }




    //     return redirect()->back()->with('success', 'Done!');
    // }

    public function editSliderSubmit($id, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('basic_system_config.update'),
            403
        );

        $request->validate([
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2097152',
            'header'           => 'required|string',
            'order'            => 'required',
            'description'      => 'nullable|string',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Find Slider
    |--------------------------------------------------------------------------
    */

        $slider = HomepageSlider::where('id', $id)->first();

        if (!$slider) {
            return redirect()->back()
                ->with('error', 'Records not found!');
        }

        /*
    |--------------------------------------------------------------------------
    | Store old image BEFORE updating the record
    |--------------------------------------------------------------------------
    */

        $oldImage = $slider->background_image;

        /*
    |--------------------------------------------------------------------------
    | Update slider information
    |--------------------------------------------------------------------------
    */

        $slider->header = trim($request->header);
        $slider->order = trim($request->order);
        $slider->description = trim($request->description);

        /*
    |--------------------------------------------------------------------------
    | Handle Background Image
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('background_image')) {

            $file = $request->file('background_image');

            /*
        |--------------------------------------------------------------------------
        | Check uploaded file
        |--------------------------------------------------------------------------
        */

            if (!$file->isValid()) {

                return redirect()->back()
                    ->with(
                        'error',
                        'Image upload failed: ' . $file->getErrorMessage()
                    );
            }

            /*
        |--------------------------------------------------------------------------
        | Upload directory
        |--------------------------------------------------------------------------
        */

            $uploadPath = public_path(
                'assets/tenant_' . tenant()->id . '/uploads/sliders'
            );

            File::ensureDirectoryExists($uploadPath);

            /*
        |--------------------------------------------------------------------------
        | Generate filename
        |--------------------------------------------------------------------------
        */

            $baseName = Str::slug(
                trim($request->header),
                '_'
            );

            if (empty($baseName)) {
                $baseName = 'slider_' . $slider->id;
            }

            /*
        |--------------------------------------------------------------------------
        | Temporary uploaded file
        |--------------------------------------------------------------------------
        */

            $tmp = $file->getRealPath();

            $mime = $file->getMimeType();

            $ext = strtolower(
                $file->getClientOriginalExtension()
            );

            /*
        |--------------------------------------------------------------------------
        | New image information
        |--------------------------------------------------------------------------
        */

            $newImageName = null;
            $processed = false;

            /*
        |--------------------------------------------------------------------------
        | Try GD WebP processing
        |--------------------------------------------------------------------------
        */

            if (
                function_exists('imagewebp') &&
                function_exists('imagecreatetruecolor')
            ) {

                $src = match ($mime) {

                    'image/jpeg' =>
                    function_exists('imagecreatefromjpeg')
                        ? @imagecreatefromjpeg($tmp)
                        : null,

                    'image/png' =>
                    function_exists('imagecreatefrompng')
                        ? @imagecreatefrompng($tmp)
                        : null,

                    'image/webp' =>
                    function_exists('imagecreatefromwebp')
                        ? @imagecreatefromwebp($tmp)
                        : null,

                    default => null,
                };

                /*
            |--------------------------------------------------------------------------
            | Image successfully loaded into GD
            |--------------------------------------------------------------------------
            */

                if ($src) {

                    $width = imagesx($src);
                    $height = imagesy($src);

                    /*
                |--------------------------------------------------------------------------
                | Resize maximum dimension to 800px
                |--------------------------------------------------------------------------
                */

                    $maxSize = 800;

                    if (max($width, $height) > $maxSize) {

                        $scale = $maxSize / max($width, $height);

                        $newWidth = (int) round($width * $scale);
                        $newHeight = (int) round($height * $scale);

                        $dst = imagecreatetruecolor(
                            $newWidth,
                            $newHeight
                        );

                        /*
                    |--------------------------------------------------------------------------
                    | Preserve transparency
                    |--------------------------------------------------------------------------
                    */

                        imagealphablending(
                            $dst,
                            false
                        );

                        imagesavealpha(
                            $dst,
                            true
                        );

                        $transparent = imagecolorallocatealpha(
                            $dst,
                            0,
                            0,
                            0,
                            127
                        );

                        imagefill(
                            $dst,
                            0,
                            0,
                            $transparent
                        );

                        /*
                    |--------------------------------------------------------------------------
                    | Resize
                    |--------------------------------------------------------------------------
                    */

                        imagecopyresampled(
                            $dst,
                            $src,
                            0,
                            0,
                            0,
                            0,
                            $newWidth,
                            $newHeight,
                            $width,
                            $height
                        );

                        imagedestroy($src);

                        $src = $dst;
                    }

                    /*
                |--------------------------------------------------------------------------
                | WebP filename
                |--------------------------------------------------------------------------
                */

                    $webpName = $baseName . '.webp';

                    $webpPath = $uploadPath . DIRECTORY_SEPARATOR . $webpName;

                    /*
                |--------------------------------------------------------------------------
                | Compress until <= 1MB
                |--------------------------------------------------------------------------
                */

                    $quality = 90;

                    do {

                        /*
                    | Remove previous attempt
                    */

                        if (file_exists($webpPath)) {
                            @unlink($webpPath);
                        }

                        /*
                    | Generate WebP
                    */

                        $success = @imagewebp(
                            $src,
                            $webpPath,
                            $quality
                        );

                        clearstatcache(
                            true,
                            $webpPath
                        );

                        /*
                    | Check generated file
                    */

                        if (
                            $success &&
                            file_exists($webpPath) &&
                            filesize($webpPath) <= 1048576
                        ) {

                            $processed = true;

                            $newImageName = $webpName;

                            break;
                        }

                        /*
                    | Reduce quality
                    */

                        $quality -= 5;
                    } while ($quality >= 40);

                    /*
                |--------------------------------------------------------------------------
                | Cleanup GD
                |--------------------------------------------------------------------------
                */

                    imagedestroy($src);

                    /*
                |--------------------------------------------------------------------------
                | Remove failed WebP
                |--------------------------------------------------------------------------
                */

                    if (
                        !$processed &&
                        file_exists($webpPath)
                    ) {

                        @unlink($webpPath);
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Fallback - Store Original File
        |--------------------------------------------------------------------------
        */

            if (!$processed) {

                /*
            | Prevent filename collisions
            */

                $fileName = $baseName . '.' . $ext;

                $destination = $uploadPath . DIRECTORY_SEPARATOR . $fileName;

                /*
            | If the destination already exists,
            | remove it before moving the new file.
            */

                if (file_exists($destination)) {
                    @unlink($destination);
                }

                /*
            | Move uploaded file
            */

                $file->move(
                    $uploadPath,
                    $fileName
                );

                $newImageName = $fileName;
            }

            /*
        |--------------------------------------------------------------------------
        | Make sure new image actually exists
        |--------------------------------------------------------------------------
        */

            if (
                !$newImageName ||
                !file_exists(
                    $uploadPath . DIRECTORY_SEPARATOR . $newImageName
                )
            ) {

                return redirect()->back()
                    ->with(
                        'error',
                        'The image could not be saved.'
                    );
            }

            /*
        |--------------------------------------------------------------------------
        | Set database filename
        |--------------------------------------------------------------------------
        */

            $slider->background_image = $newImageName;

            /*
        |--------------------------------------------------------------------------
        | Delete OLD image
        |--------------------------------------------------------------------------
        |
        | Only delete the old image AFTER the new image has been
        | successfully created.
        |
        */

            if (
                !empty($oldImage) &&
                $oldImage !== $newImageName
            ) {

                $oldImagePath = $uploadPath . DIRECTORY_SEPARATOR . $oldImage;

                if (is_file($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Also remove old files using the previous header filename
        |--------------------------------------------------------------------------
        |
        | This handles cases where the slider header was changed.
        |
        */

            if (!empty($oldImage)) {

                $oldBaseName = pathinfo(
                    $oldImage,
                    PATHINFO_FILENAME
                );

                foreach (
                    glob(
                        $uploadPath .
                            DIRECTORY_SEPARATOR .
                            $oldBaseName .
                            '.*'
                    ) ?: [] as $existingFile
                ) {

                    if (
                        is_file($existingFile) &&
                        basename($existingFile) !== $newImageName
                    ) {
                        @unlink($existingFile);
                    }
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Save Slider
    |--------------------------------------------------------------------------
    */

        $slider->save();

        return redirect()->back()
            ->with('success', 'Done!');
    }

    public function seoConfig()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_seo'),
            403
        );

        $seoDetails = SeoDetail::firstOrCreate([
            'tenant_id' => tenant()->id,
        ]);
        $data = [
            'seoDetails' => $seoDetails,
        ];

        return view('users.admin.config.seo.seoConfig', compact('data'));
    }
    public function updateSeoConfigSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_seo'),
            403
        );

        $request->validate([
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string',
            'meta_keywords' => 'required|string',
            'header_scripts' => 'nullable|string',
            'body_scripts' => 'nullable|string',
        ]);


        $seoDetails = SeoDetail::where('tenant_id', tenant()->id)->first();
        if (!$seoDetails) {
            return redirect()->back()->with('error', 'Seo config not found!');
        }
        $seoDetails->meta_title = $request->meta_title;
        $seoDetails->meta_description = $request->meta_description;
        $seoDetails->meta_keywords = $request->meta_keywords;
        $seoDetails->header_scripts = $request->header_scripts;
        $seoDetails->body_scripts = $request->body_scripts;
        $seoDetails->save();

        return redirect()->back()->with('success', 'Done!');
    }
}
