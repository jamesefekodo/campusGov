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

            if ($user->account_type == 2 && getUserType() === 'member') {
                $data = [
                    'member' => $user,
                ];
                return view('users.member.dashboard', compact('data'));
            }
            // 1 - Admin, 0 - root - 90 odu admin
            if (
                in_array((int) $user->account_type, [0, 1, 90], true) || $user->admin_privilege === true
            ) {
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
