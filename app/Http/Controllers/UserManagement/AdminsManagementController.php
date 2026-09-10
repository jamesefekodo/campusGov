<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
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


        if (Route::currentRouteName() == 'administratorsSearch') {
            $searchKey = $request->input('searchKey');

            $admins = User::whereIn('account_type', [0, 1])
                ->with([
                    'adminProfile',
                ])
                ->when($searchKey, function ($query) use ($searchKey) {

                    // Split search into individual words
                    $terms = preg_split('/\s+/', trim($searchKey));

                    $query->where(function ($q) use ($terms) {

                        foreach ($terms as $term) {

                            if (empty($term)) {
                                continue;
                            }

                            $keyword = "%{$term}%";

                            $q->where(function ($subQuery) use ($keyword) {

                                // Search User fields
                                $subQuery->where('phone', 'like', $keyword)
                                    ->orWhere('username', 'like', $keyword)
                                    ->orWhere('email', 'like', $keyword)

                                    // Search Admin Profile
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

                ->leftJoin(
                    'admin_profiles',
                    'users.username',
                    '=',
                    'admin_profiles.username'
                )

                ->orderBy('admin_profiles.name', 'asc')

                // Keep User as the returned model
                ->select('users.*')

                ->paginate(20)
                ->withQueryString();

            $totalAdmins = $admins->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $admins = User::whereIn('account_type', [0, 1])
                ->leftJoin('admin_profiles', 'users.username', '=', 'admin_profiles.username')
                ->orderByRaw('CASE WHEN users.account_type = 0 THEN 0 ELSE 1 END')
                ->orderBy('admin_profiles.name', 'asc')
                ->select('users.*')
                ->paginate(20);
            $totalAdmins = $admins->total();
            $requestType = 'all';
            $pageTitle = 'Administrators';
        }
        $data = [
            'admins' => $admins,
            // 'totalAdmins' => $totalAdmins,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
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

        $data = [
            'admin' => $admin,
        ];

        return view('users.admin.userManagement.administrators.viewAdministrator', compact('data'));
    }
}
