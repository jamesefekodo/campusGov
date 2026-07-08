<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    public function checLogin()
    {
        if (Auth::check()) {
            $userType = getUserType();

            if (in_array($userType, ['root', 'admin'])) {
                return redirect()->route('adminDashboard');
            }
            if ($userType === 'student') {
                return redirect()->route('studentDashboard');
            }
        }

        return null; // No redirection, continue execution
    }

    public function homepage()
    {
        // Call the login check function
        $redirect = $this->checLogin();
        if ($redirect) {
            return $redirect;
        }


        // return view('frontend2.homepage', compact('data'));
        return 'Homepage is under construction. Please check back later.';
    }
}
