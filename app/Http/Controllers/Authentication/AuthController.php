<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Mail\AccountSetup\EmailVerificationMail;
use App\Mail\AccountSetup\LoginOtpMail;
use App\Mail\AccountSetup\NewAccountWelcomeMail;
use App\Mail\AccountSetup\PasswordResetMail;
use App\Models\Config\BasicSystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


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
                return redirect()->route('memberDashboard');
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
    public function forgotPassword()
    {
        if (!empty(Auth::check())) {
            return $this->redirectToUserDashboard(Auth::user()->account_type);
        }
        return view('utilities.pages.auth.fogortPassword');
    }
    public function requestPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::getEmailCheck($request->email);
        if (!empty($user)) {
            $user->remember_token = Str::random(30);
            $user->save();
            Mail::to($user->email)->send(new PasswordResetMail($user));

            return redirect()->route('loginPage')->with('success', "Sent!");
        } else {
            return redirect()->back()->with('error', 'No Records Found!');
        }
    }
    public function passwordResetPage($remember_token)
    {
        $user = User::getTokenSingle($remember_token);
        if (!empty($user)) {
            $data['user'] = $user;
            return view('utilities.pages.auth.resetPassword', $data);
        } else {
            abort(404);
        }
    }
    public function submitPasswordResetRequest($token, Request $request)
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
            'password' => $passwordRules,
            'confirm_password' => 'required|string|same:password',
        ]);

        // Check validation results
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // Pass all validation errors
                ->withInput(); // Retain user input
        }

        if ($request->password == $request->confirm_password) {
            $user = User::getTokenSingle($token);
            if (!$user) {
                return redirect()->back()->with('error', 'User record not found!');
            }

            $user->password = Hash::make($request->password);
            $user->remember_token = Str::random(30);
            $user->save();

            return redirect()->route('loginPage')->with('success', 'Done!');
        } else {
            return redirect()->back()->with('error', 'Passwords do not match!');
        }
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

            if ($user) {
                if ($user->require_login_mfa == 1) {
                    return redirect()->route('authenticateLogin');
                } else {
                    return $this->redirectToUserDashboard($user->account_type);
                }
            } else {
                return redirect()->route('AuthLogout');
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
            'email' => 'required|email|unique:users,email',
            // |unique:student_personal_profiles,email2
            'phone' => 'required|string|unique:users,phone',
            // |unique:student_personal_profiles,phone2
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

        Mail::to($user->email)->send(new NewAccountWelcomeMail($user));

        $loginRequest = new Request([
            'login_id' => $user->email,
            'password' => $request->password,
        ]);

        return $this->authLogin($loginRequest);
        // return redirect()->route('loginPage')->with('success', 'Done!');
    }
    public function maintenanceMode()
    {
        $user = Auth::user();
        if (!$user) {
            return $this->accessDeniedLogout();
        }
        $basicDefaultSettings = BasicSystemSetting::first();
        if ($maintenanceModeLock = $basicDefaultSettings->maintenance_mode === true && (int) $user->account_type !== 0) {
            return view('utilities.pages.auth.maintenanceMode');
        }
        if (!empty(Auth::check())) {
            return $this->redirectToUserDashboard(Auth::user()->account_type);
        }
    }
    public function generateEmailVerificationCode(User $user)
    {
        $otp = rand(10000000, 99999999);

        DB::table('users')
            ->where('username', $user->username)
            ->update([
                'email_verification_code' => $otp,
                'email_verification_code_expires_at' => now()->addMinutes(10),
            ]);

        return $otp;
    }

    public function resetEmailVerificationCode(User $user)
    {
        DB::table('users')
            ->where('username', $user->username)
            ->update([
                'email_verification_code' => null,
                'email_verification_code_expires_at' => null,
            ]);
    }
    private function sendEmailVerificationCodeEmailtoUser($email, $otp)
    {
        // Send OTP to user's email
        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Error!');
        } else {
            Mail::to($email)->send(new EmailVerificationMail($email, $user, $otp));
        }
    }
    public function verifyUserEmail()
    {
        $user = Auth::user();
        if (
            $user->email_verification !== true
        ) {
            if (
                $user->email_verification_code === null ||
                $user->email_verification_code === '' ||
                Carbon::parse($user->email_verification_code_expires_at)->lt(now())
            ) {
                $otp = $this->generateEmailVerificationCode($user);
                $this->sendEmailVerificationCodeEmailtoUser($user->email, $otp);
            }

            return view('utilities.pages.auth.userAccountValidation');
        } else {
            return $this->redirectToUserDashboard($user->account_type);
        }
    }
    public function resendEmailVerificationCodeEmailtoUser()
    {
        $user = Auth::user();

        if ($user) {
            $otp = $this->generateEmailVerificationCode($user);
            $this->sendEmailVerificationCodeEmailtoUser($user->email, $otp);

            return redirect()->back()->with('success', 'Check your mail for the code!');
        } else {
            return $this->accessDeniedLogout();
        }
    }
    public function verifyUserEmailSubmit(Request $request)
    {
        $request->validate([
            'email_verification_code' => 'required|numeric|digits:8',
        ]);
        $user = Auth::user();
        $userRec = User::where('username', $user->username);


        if ($user) {
            // Check if OTP has expired
            if ($user->email_verification_code_expires_at < now()) {
                $this->resetEmailVerificationCode($user);
                return $this->accessDeniedLogout();
            }

            // check if OTP is correct
            if ($request->input('email_verification_code') === $user->email_verification_code) {
                $this->resetEmailVerificationCode($user);


                $userRec->update([
                    'email_verification' => true,
                    'email_verified_at' => now(),
                ]);

                return $this->redirectToUserDashboard(Auth::user()->account_type);
            } else {
                return redirect()->back()->with('error', 'Invalid Code!');
            }
        } else {
            return $this->accessDeniedLogout();
        }
    }

    public function generateTwoFactorCode(User $user, $currentSession)
    {
        $otp = rand(100000, 999999);

        DB::table('sessions')
            ->where('id', $currentSession->id)
            ->update([
                'login_otp' => $otp,
                'login_otp_expires_at' => now()->addMinutes(10),
            ]);

        return $otp;
    }

    public function resetTwoFactorCode(User $user, $currentSession)
    {
        DB::table('sessions')
            ->where('id', $currentSession->id)
            ->update([
                'login_otp' => null,
                'login_otp_expires_at' => null,
            ]);
    }


    private function sendLoginOtpByEmailtoUser($email, $otp)
    {
        // Send OTP to user's email
        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Error!');
        } else {
            Mail::to($email)->send(new LoginOtpMail($email, $user, $otp));
        }
    }
    public function loginOtpExpiredLogout()
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

        return redirect(url('/'))->with('error', 'OTP has expired!');
    }
    public function authenticateLogin()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->accessDeniedLogout();
        }

        $currentSessionId = Session::getId();
        $currentSession = DB::table('sessions')->where('id', $currentSessionId)->first();

        if (!$currentSession) {
            return $this->accessDeniedLogout();
        }

        if ($user->require_login_mfa == 0) {
            return $this->redirectToUserDashboard($user->account_type);
        }

        // MFA verified
        if ((int) $currentSession->mfa_verified === 1) {
            return $this->redirectToUserDashboard($user->account_type);
        }


        // OTP to email senabled and not yet verified
        if (
            (int) $user->require_login_mfa === 1
            && (int) $currentSession->mfa_verified !== 1
        ) {

            if (
                $currentSession->login_otp === null ||
                $currentSession->login_otp === '' ||
                Carbon::parse($currentSession->login_otp_expires_at)->lt(now())
            ) {
                $otp = $this->generateTwoFactorCode($user, $currentSession);
                $this->sendLoginOtpByEmailtoUser($user->email, $otp);
            }

            return view('utilities.pages.auth.loginOtpAuthentication');
        }
    }
    public function verifyLoginOtpSubmit(Request $request)
    {
        $request->validate([
            'login_otp' => 'required|numeric|digits:6',
        ]);
        $user = Auth::user();
        $currentSessionId = Session::getId();
        $currentSession = DB::table('sessions')->where('id', $currentSessionId)->first();

        if ($user) {
            // Check if OTP has expired
            if ($currentSession->login_otp_expires_at < now()) {
                $this->resetTwoFactorCode($user, $currentSession);
                return $this->loginOtpExpiredLogout();
            }

            // check if OTP is correct
            if ((int) $request->input('login_otp') === (int)$currentSession->login_otp) {
                DB::table('sessions')
                    ->where('id', $currentSession->id)
                    ->update([
                        'mfa_verified' => 1,
                    ]);
                $this->resetTwoFactorCode($user, $currentSession);

                return $this->redirectToUserDashboard(Auth::user()->account_type);
            } else {
                return redirect()->back()->with('error', 'Incorrect OTP!');
            }
        } else {
            return $this->accessDeniedLogout();
        }
    }
    public function resendLoginOtp()
    {
        $user = Auth::user();
        $currentSessionId = Session::getId();
        $currentSession = DB::table('sessions')->where('id', $currentSessionId)->first();

        if ($user) {
            $otp = $this->generateTwoFactorCode($user, $currentSession);
            $this->sendLoginOtpByEmailtoUser($user->email, $otp);

            return redirect()->back()->with('success', 'OTP Sent!');
        } else {
            return $this->accessDeniedLogout();
        }
    }
    public function manageSession()
    {
        $user = Auth::user();

        if ($user) {
            $currentSessionId = Session::getId();
            $currentSession = DB::table('sessions')->where('id', $currentSessionId)->first();
            $userActiveSessions = DB::table('sessions')->where('user_id', $user->id)->count();
            if ($userActiveSessions > $user->session_limit) {
                return view('utilities.pages.auth.manageSessions');
            } else {
                return $this->redirectToUserDashboard(Auth::user()->account_type);
            }
        } else {
            return $this->accessDeniedLogout();
        }
    }
    public function endOtherSessions()
    {
        $user = Auth::user();
        $currentSessionId = Session::getId();

        // Get all sessions for the user except the current one
        $otherSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->get();

        // Loop through each session and delete it
        foreach ($otherSessions as $session) {
            DB::table('sessions')->where('id', $session->id)->delete();
        }

        return redirect('/');
    }
}
