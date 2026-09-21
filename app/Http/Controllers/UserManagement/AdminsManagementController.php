<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminProfile;
use App\Models\Config\AccessControl\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminsManagementController extends Controller
{
    public function administrators(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('admin.read'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $admins = '';
        $totalAdmins = 0;
        $membersWithAdminPermission = null;

        if (Route::currentRouteName() == 'administratorsSearch') {
            $searchKey = $request->input('searchKey');

            $admins = User::whereIn('account_type', [0, 1])
                ->where('users.tenant_id', tenant()->id) // qualify
                ->with(['adminProfile'])
                ->when($searchKey, function ($query) use ($searchKey) {
                    $terms = preg_split('/\s+/', trim($searchKey));

                    $query->where(function ($q) use ($terms) {
                        foreach ($terms as $term) {
                            if (empty($term)) {
                                continue;
                            }

                            $keyword = "%{$term}%";

                            $q->where(function ($subQuery) use ($keyword) {
                                // Search User fields
                                $subQuery->where('users.phone', 'like', $keyword)
                                    ->orWhere('users.username', 'like', $keyword)
                                    ->orWhere('users.email', 'like', $keyword)

                                    // Search Admin Profile via relationship
                                    ->orWhereHas('adminProfile', function ($pq) use ($keyword) {
                                        $pq->where('name', 'like', $keyword)
                                            ->orWhere('position', 'like', $keyword)
                                            ->orWhere('last_name', 'like', $keyword)
                                            ->orWhere('description', 'like', $keyword);
                                    });
                            });
                        }
                    });
                })
                ->leftJoin('admin_profiles', 'users.username', '=', 'admin_profiles.username')
                ->orderBy('admin_profiles.name', 'asc')
                ->select('users.*')
                ->get()
                ->withQueryString();

            // $totalAdmins = $admins->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;

            $membersWithAdminPermission = null;
        } else {
            $admins = User::whereIn('account_type', [0, 1])
                ->where('users.tenant_id', tenant()->id) // qualify table
                ->leftJoin('admin_profiles', 'users.username', '=', 'admin_profiles.username')
                ->orderByRaw('CASE WHEN users.account_type = 0 THEN 0 ELSE 1 END')
                ->orderBy('admin_profiles.name', 'asc')
                ->select('users.*')
                ->get();
            // $totalAdmins = $admins->total();
            $requestType = 'all';
            $pageTitle = 'Administrators';
            $membersWithAdminPermission = User::whereHas('roles')->get();
        }
        $data = [
            'admins' => $admins,
            // 'totalAdmins' => $totalAdmins,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'membersWithAdminPermission' => $membersWithAdminPermission,
        ];
        return view('users.admin.userManagement.administrators.administrators', compact('data'));
    }
    public function createNewAdmin(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_admin'),
            403
        );

        // check if it has reached limit
        if (tenant()->tenantAdministrators()->count() >= 10) {
            return redirect()->back()->with('error', 'You cannot have more than ten extra administrator accounts, besides one root account.');
        }
        return view('users.admin.userManagement.administrators.createNewAdministrator');
    }
    public function createAdminProfileSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_admin'),
            403
        );

        $request->validate([
            'photo_cropped' => 'nullable',
            'username' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('users')
                        ->where('username', $value)
                        ->exists(); // no tenant_id filter

                    if ($exists) {
                        $fail('The username has already been taken.');
                    }
                },
                'regex:/^\S*$/u',
                'max:20',
                'not_regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
                'not_regex:/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{2300}-\x{23FF}\x{2B50}]/u',
            ],
            'name' => 'required',

            // 'phone' => [
            //     'required',
            //     'string',
            //     function ($attribute, $value, $fail) {
            //         $existsInUsers = DB::table('users')
            //             ->where('phone', $value)
            //             ->exists();

            //         $existsInProfiles = DB::table('member_personal_profiles')
            //             ->where('phone2', $value)
            //             ->exists();

            //         if ($existsInUsers || $existsInProfiles) {
            //             $fail('The phone number has already been taken.');
            //         }
            //     },
            // ],

            // 'email' => [
            //     'required',
            //     'email',
            //     function ($attribute, $value, $fail) {
            //         $existsInUsers = DB::table('users')
            //             ->where('email', $value)
            //             ->exists();

            //         $existsInProfiles = DB::table('member_personal_profiles')
            //             ->where('email2', $value)
            //             ->exists();

            //         if ($existsInUsers || $existsInProfiles) {
            //             $fail('The email address has already been taken.');
            //         }
            //     },
            // ],
            'phone' => 'required|string|unique:users,phone|unique:member_personal_profiles,phone2',
            'email' => 'required|email|unique:users,email|unique:member_personal_profiles,email2',
            'position' => 'required',
            'description' => 'required',
        ]);




        $user = new User();
        $user->username = strtolower(trim($request->username));
        $user->email = trim($request->email);
        $user->phone = trim($request->phone);
        $user->account_type = 1;
        $user->account_status = 2;
        $user->password = Hash::make(Str::random(10));
        $user->save();

        $adminProfile = AdminProfile::updateOrCreate(
            ['username' => $request->username],
            [
                'name' => trim($request->name),
                'position' => trim($request->position),
                'description' => trim($request->description),
                'created_by' => Auth::user()->username,
                'last_update_by' => Auth::user()->username,
            ]
        );


        if ($request->filled('photo_cropped')) {
            // Delete existing photo if it exists
            if (!empty($adminProfile->userProfile->photo)) {
                $oldPath = public_path('assets/tenant_' . tenant()->id . '/uploads/users/admin/photo/' . $adminProfile->userProfile->photo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo_cropped);

            $decoded = base64_decode($base64);

            $filename = $adminProfile->userProfile->username . uniqid() . '.jpg';

            $path = public_path('assets/tenant_' . tenant()->id . '/uploads/users/admin/photo/' . $filename);

            file_put_contents($path, $decoded);

            $adminProfile->userProfile->photo = $filename;
            $adminProfile->userProfile->save();
        }


        return redirect()->route('administrators')->with('success', 'Done!');
    }
    public function viewAdmin($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_admin'),
            403
        );

        $admin = User::whereIn('account_type', [0, 1])->where('uuid', $uuid)->first();
        if (!$admin) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $roles = Role::where('tenant_id', tenant()->id)->orderBy('name')->get();

        $data = [
            'admin' => $admin,
            'availableRoles' => $roles,
        ];

        return view('users.admin.userManagement.administrators.viewAdministrator', compact('data'));
    }
    public function updateAdminProfileSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_admin'),
            403
        );

        $admin = User::whereIn('account_type', [0, 1])->where('uuid', $uuid)->first();
        if (!$admin) {
            return redirect()->back()->with('error', 'Records not found!');
        }


        $request->validate([
            'photo_cropped' => 'nullable',
            // 'username' => [
            //     'required',
            //     function ($attribute, $value, $fail) use ($admin) {
            //         $exists = DB::table('users')
            //             ->where('username', $value)
            //             ->where('username', '!=', $admin->username) // ignore current admin
            //             ->exists();

            //         if ($exists) {
            //             $fail('The username has already been taken.');
            //         }
            //     },
            //     'regex:/^\S*$/u',
            //     'max:20',
            //     'not_regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
            //     'not_regex:/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{2300}-\x{23FF}\x{2B50}]/u',
            // ],
            'name' => 'required',

            // 'phone' => [
            //     'required',
            //     'string',
            //     function ($attribute, $value, $fail) {
            //         $existsInUsers = DB::table('users')
            //             ->where('phone', $value)
            //             ->exists();

            //         $existsInProfiles = DB::table('member_personal_profiles')
            //             ->where('phone2', $value)
            //             ->exists();

            //         if ($existsInUsers || $existsInProfiles) {
            //             $fail('The phone number has already been taken.');
            //         }
            //     },
            // ],

            // 'email' => [
            //     'required',
            //     'email',
            //     function ($attribute, $value, $fail) {
            //         $existsInUsers = DB::table('users')
            //             ->where('email', $value)
            //             ->exists();

            //         $existsInProfiles = DB::table('member_personal_profiles')
            //             ->where('email2', $value)
            //             ->exists();

            //         if ($existsInUsers || $existsInProfiles) {
            //             $fail('The email address has already been taken.');
            //         }
            //     },
            // ],
            'phone' => [
                'required',
                'string',
                // Unique within tenant in users.phone, ignoring current admin
                Rule::unique('users', 'phone')
                    ->ignore($admin->username, 'username'),

                // Unique within tenant in member_personal_profiles.phone2, ignoring current admin
                Rule::unique('member_personal_profiles', 'phone2', 'username')
                    ->ignore($admin->username, 'username'),
            ],

            'email' => [
                'required',
                'email',
                // Unique within tenant in users.email, ignoring current admin
                Rule::unique('users', 'email')
                    ->ignore($admin->username, 'username'),

                // Unique within tenant in member_personal_profiles.email2, ignoring current admin
                Rule::unique('member_personal_profiles', 'email2', 'username')
                    ->ignore($admin->username, 'username'),
            ],
            'position' => 'required',
            'description' => 'required',
        ]);

        if ($admin->isDirty('email') && $admin->email !== $admin->getOriginal('email')) {
            $admin->email_verified_at = null;
            $admin->email_verification = false;

            // Delete all agreed policies for this admin
            $admin->agreedPolicies()->delete();
        }

        $admin->phone = $request->phone;
        $admin->email = $request->email;

        if ($request->filled('photo_cropped')) {
            // Delete existing photo if it exists
            if (!empty($admin->photo)) {
                $oldPath = public_path('assets/tenant_' . tenant()->id . '/uploads/users/admin/photo/' . $admin->photo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo_cropped);

            $decoded = base64_decode($base64);

            $filename = $admin->username . uniqid() . '.jpg';

            $path = public_path('assets/tenant_' . tenant()->id . '/uploads/users/admin/photo/' . $filename);

            file_put_contents($path, $decoded);

            $admin->photo = $filename;
        }


        $admin->save();


        $admin->adminProfile([
            'name' => trim($request->name),
            'position' => trim($request->position),
            'description' => trim($request->description),
            'last_update_by' => Auth::user()->username,
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
    public function changeAdminPasswordSubmit($uuid, Request $request)
    {
        $passwordRules = [
            'required',
            'string',
            'min:8',
            'regex:/[A-Z]/',
            'regex:/[a-z]/',
            'regex:/[0-9]/',
            'regex:/[!@#$%^&*(),.?":{}|<>]/',
        ];

        // Validate input
        $validator = Validator::make($request->all(), [
            'password' => $passwordRules,
            'confirm_password' => 'required|string|same:password',
        ]);

        // Check validation results
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_admin'),
            403
        );

        $admin = User::whereIn('account_type', [0, 1])->where('uuid', $uuid)->first();
        if (!$admin) {
            return redirect()->back()->with('error', 'Records not found!');
        }



        // Change password
        User::where('username', $admin->username)->first()->update([
            'password' => Hash::make($request->password),
        ]);


        return redirect()->back()
            ->with('success', 'Password changed successfully!');
    }
    public function toggleAdminAccountStatus($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_admin'),
            403
        );

        $admin = User::where('account_type', 1)->where('uuid', $uuid)->first();
        if (!$admin) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        if ($admin->account_status == 2) {
            $admin->update([
                'account_status' => 3,
            ]);
        } else {
            $admin->update([
                'account_status' => 2,
            ]);
        }
        return redirect()->back()->with('success', 'Done!');
    }
    public function adminSelfProfile()
    {
        $admin = Auth::user();
        $data = [
            'admin' => $admin,
        ];
        return view('users.admin.userManagement.administrators.viewSelfProfile', compact('data'));
    }
}
