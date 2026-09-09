<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\AccessControl\Permission;
use App\Models\Config\AccessControl\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PermissionAccessController extends Controller
{
    public function viewUserRoles(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('roles.read'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $roles = '';
        $totalRoles = 0;


        if (Route::currentRouteName() == 'rolesSearch') {
            $searchKey = $request->input('searchKey');
            $roles = Role::where('name', 'like', "%$searchKey%")
                ->orWhere('description', 'like', "%$searchKey%")
                ->orderBy('name')
                ->paginate(20)->withQueryString();


            $totalRoles = $roles->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $roles = Role::orderBy('name')->paginate(20);
            $totalRoles = Role::count();
            $requestType = 'all';
            $pageTitle = 'User Roles';
        }

        $data = [
            'roles' => $roles,
            'totalRoles' => $totalRoles,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];


        return view('users.admin.config.accessControl.roles.roles', compact('data'));
    }
    public function viewRole(Request $request, $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('roles.read'),
            403
        );

        $role = Role::where('uuid', $uuid)->first();

        $assignedPermissionIds = $role->permissions()->pluck('permissions.id')->toArray();

        $permissionsByModule = Permission::orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        if (!$role) {
            return redirect()->back()->with('error', 'Role not found.');
        }

        $data = [
            'role' => $role,
            'assignedPermissionIds' => $assignedPermissionIds,
            'permissionsByModule' => $permissionsByModule,
        ];

        return view('users.admin.config.accessControl.roles.viewRole', compact('data'));
    }
    public function addNewRoleSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('roles.create'),
            403
        );

        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        // do {
        //     $roleSlug = 'role-' . now()->format('Ym') . '-' . Str::lower(Str::random(10));
        // } while (Role::where('slug', $roleSlug)->exists());  i replaced this with uuid in model


        $role = Role::create([
            'name' => trim($request->name),
            'description' => trim($request->description),
        ]);

        return redirect()->back()->with('success', 'New role added successfully.');
    }
    public function updateRoleSubmit(Request $request, $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('roles.update'),
            403
        );

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'user_cap' => ['nullable', 'numeric', 'min:1'],
            'assignment_scope' => 'required',
        ]);

        $role = Role::where('uuid', $uuid)->first();
        if (!$role) {
            return redirect()->back()->with('error', 'Role not found.');
        }
        $role->update([
            'name' => trim($request->name),
            'description' => trim($request->description),
            'user_cap' => filled($request->user_cap) ? $request->user_cap : null,
            'assignment_scope' => $request->assignment_scope,
        ]);

        return redirect()->back()->with('success', 'Role updated successfully.');
    }
    public function deleteRole(Request $request, $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('roles.delete'),
            403
        );

        $role = Role::where('uuid', $uuid)->first();
        if (!$role) {
            return redirect()->back()->with('error', 'Role not found.');
        }

        // Check if the role is assigned to any users
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role. It is assigned to one or more users.');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }
}
