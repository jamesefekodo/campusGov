<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function redirectToUserDashboard($accountType)
    {
        switch ($accountType) {
            // 0 - root, 1-admin , 2- student
            case 0:
            case 1:
                return redirect()->route('adminDashboard');
            case 2:
                return redirect()->route('studentDashboard');
            default:
                return $this->accessDeniedLogout();
        }
    }

    public function AuthLogout()
    {
        // Get current session ID
        $currentSessionId = Session::getId();
        $now = Carbon::now();

        Auth::logout();

        // Invalidate and regenerate the session token
        Session::invalidate();
        Session::regenerateToken();


        // Check if session driver is 'database'
        if (config('session.driver') === 'database') {
            $currentSession = DB::table('sessions')->where('id', $currentSessionId)->first();

            // If the session exists, delete it from the database
            if ($currentSession) {
                DB::table('sessions')->where('id', $currentSession->id)->delete();
            }
        }

        return redirect(url('/'))->with('success', 'Logged Out!');
    }
    public function accessDeniedLogout()
    {
        // Get current session ID
        $currentSessionId = Session::getId();

        Auth::logout();

        // Invalidate and regenerate the session token
        Session::invalidate();
        Session::regenerateToken();


        // Check if session driver is 'database'
        if (config('session.driver') === 'database') {
            $currentSession = DB::table('sessions')->where('id', $currentSessionId)->first();

            // If the session exists, delete it from the database
            if ($currentSession) {
                DB::table('sessions')->where('id', $currentSession->id)->delete();
            }
        }

        return redirect(url('/'))->with('error', 'Access Denied');
    }
    public function login()
    {
        if (!empty(Auth::check() || getUserType() !== 'guest')) {
            return $this->redirectToUserDashboard(Auth::user()->account_type);
        }
        return view('utilities.pages.auth.login');
    }
    public function authLogin(Request $request)
    {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required',
        ]);


        if (Auth::attempt(['username' => $request->login_id, 'password' => $request->password]) || Auth::attempt(['email' => $request->login_id, 'password' => $request->password],)) {
            $user = Auth::user();
            $currentSessionId = Session::getId();
            $now = Carbon::now();
            $ipAddress = $request->ip();
            $userAgent = $request->header('User-Agent');

            // Determine device type
            if (preg_match('/Mobile|Android|iP(hone|od|ad)|Opera Mini/i', $userAgent)) {
                $deviceType = 'Mobile';
            } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
                $deviceType = 'Tablet';
            } else {
                $deviceType = 'Desktop';
            }
        } else {
            return redirect()->back()->with('error', "Invalid Credentials");
        }
    }
    public function register()
    {
        if (!empty(Auth::check() || getUserType() !== 'guest')) {
            return $this->redirectToUserDashboard(Auth::user()->account_type);
        }

        return view('utilities.pages.auth.register');
    }
    public function submitUserRegistrationRequest(Request $request)
    {
        $passwordRules = [
            'required',
            'string',
            'min:8', // Minimum of 8 characters
            'regex:/[A-Z]/', // At least 1 uppercase letter
            'regex:/[a-z]/', // At least 1 lowercase letter
            'regex:/[0-9]/', // At least 1 number
            'regex:/[!@#$%^&*(),.?":{}|<>]/', // At least 1 special character
        ];

        // Validate input
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'unique:users,username',
                'regex:/^\S*$/u',           // No spaces allowed
                'max:8',                    // Maximum 8 characters
                'not_regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',  // No email format allowed
                'not_regex:/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{2300}-\x{23FF}\x{2B50}]/u',  // No emoji or symbols
            ],
            'email' => 'required|email|unique:users,email|unique:student_personal_profiles,email2',
            'phone' => 'required|string|unique:users,phone|unique:student_personal_profiles,phone2',
            'password' => $passwordRules,
            'confirm_password' => 'required|string|same:password',
        ], [
            // Custom error messages
            'username.required' => 'The username is required.',
            'username.unique' => 'This username is already taken.',
            'username.regex' => 'The username cannot contain spaces.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.regex' => 'The password must include an uppercase letter, lowercase letter, number, and special character.',
            'confirm_password.same' => 'The confirmation password does not match.',
        ]);

        // Check validation results
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // Pass all validation errors
                ->withInput(); // Retain user input
        }


        $user = new User();
        $user->username = strtolower(trim($request->username));
        $user->email = trim($request->email);
        $user->phone = trim($request->phone);
        $user->account_type = 2;
        $user->password = Hash::make($request->password);
        $user->save();

        // Mail::to($user->email)->send(new NewAccountWelcomeMail($user));

        return redirect()->route('loginPage')->with('success', 'Done!');
    }
}
