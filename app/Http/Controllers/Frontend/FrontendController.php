<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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

        return view($themeService->homepageView($tenant), [
            'theme' => $theme,
            'themeColors' => $themeColors,
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
