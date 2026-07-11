<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        // types: 0 - root, 1-admin , 2- student, 3 - association, 4 - association suppor
        // status 0 - new , 1- awaiting approval , 2- active, 3 - disabled

        if ($user) {
            // 2 student Account

            if ($user->account_type == 2) {
                $data = [
                    'user' => $user,
                ];
                return view('users.students.dashboard', compact('data'));
            }
            // 1 - Admin, 0 - root
            if ($user->account_type == 0 || $user->account_type == 1) {
                $data = [
                    'user' => $user,
                ];
                return view('users.admin.dashboard', compact('data'));
            }
        } else {
            return redirect()->route('accessDeniedLogout');
        }
    }
}
