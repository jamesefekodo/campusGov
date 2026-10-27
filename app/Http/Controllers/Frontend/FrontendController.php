<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Faculty;
use App\Models\Config\HomepageSlider;
use App\Models\Members\MemberAcademicInfo;
use App\Services\ThemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    public function checkLogin()
    {
        if (Auth::check()) {
            $userType = getUserType();

            if (in_array($userType, ['root', 'admin'])) {
                return redirect()->route('adminDashboard');
            }
            if ($userType === 'member') {
                return redirect()->route('memberDashboard');
            }
        }

        return null; // No redirection, continue execution
    }

    // public function homepage()
    // {
    //     // Call the login check function
    //     $redirect = $this->checkLogin();
    //     if ($redirect) {
    //         return $redirect;
    //     }

    //     // return view('users.guests.temporaryHomepage');

    //     return view('users.guests.homepage');
    // }



    public function homepage()
    {
        $redirect = $this->checkLogin();

        if ($redirect) {
            return $redirect;
        }

        $tenant = tenant();

        $themeService = app(ThemeService::class);

        $theme = $themeService->activeTheme($tenant);
        $themeColors = $themeService->forTenant($tenant);

        // ############################
        $basicInfo = basicSystemInfo();
        $operationalLevel = $basicInfo->operational_level;
        $undergraduatesCount = MemberAcademicInfo::where('academic_status', 'student')
            ->whereHas('userProfile', function ($query) {
                $query->where('account_type', 2)->where('account_status', 2);
            })->count();
        $alumniCount = MemberAcademicInfo::query()
            ->where('academic_status', 'alumni')
            ->whereHas('userProfile', function ($query) {
                $query->where('account_type', 2)
                    ->where('account_status', 2);
            })
            ->count();
        // Format function: rounds to nearest 5 and appends '+'
        $formatCount = function ($count) {
            $rounded = round($count / 5) * 5;
            return $rounded . '+';
        };

        $undergraduates = $formatCount($undergraduatesCount);
        $alumni = $formatCount($alumniCount);

        $faculties = Faculty::where('is_active', true)->count();

        $data = [
            'sliders' => HomepageSlider::orderBy('order')->get(),
            'operationalLevel' => $operationalLevel,
            'undergraduates' => $undergraduates,
            'alumni' => $alumni,
        ];

        return view($themeService->homepageView($tenant), [
            'theme' => $theme,
            'themeColors' => $themeColors,
            'data' => $data,
        ]);
    }

    // public function publications()
    // {
    //     $tenant = tenant();
    //     $themeService = app(ThemeService::class);

    //     $publications = Publication::latest('published_at')->paginate(12);

    //     return view($themeService->view($tenant, 'publications'), [
    //         'publications' => $publications,
    //         'theme' => $themeService->activeTheme($tenant),
    //         'themeColors' => $themeService->forTenant($tenant),
    //     ]);
    // }
}
