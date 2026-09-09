<?php

namespace App\Livewire;

use App\Models\Config\AccessControl\Permission;
use App\Models\Config\AccessControl\Role;
use Livewire\Component;

class RolePermissionAssignments extends Component
{
    public Role $role;
    public array $assignedPermissionIds = [];
    public string $search = '';

    public function mount(Role $role)
    {
        $this->role = $role;
        $this->assignedPermissionIds = $role->permissions()->pluck('permissions.id')->toArray();
    }

    public function togglePermission($permissionId)
    {
        if ($this->role->permissions()->where('permissions.id', $permissionId)->exists()) {
            $this->role->permissions()->detach($permissionId);
        } else {
            $this->role->permissions()->syncWithoutDetaching([$permissionId]);
        }

        $this->assignedPermissionIds = $this->role->permissions()->pluck('permissions.id')->toArray();
    }

    public function toggleModule(string $module, bool $enable)
    {
        $moduleIds = Permission::where('advanced_management', false)->where('module', $module)->pluck('id')->toArray();

        if ($enable) {
            $this->role->permissions()->syncWithoutDetaching($moduleIds);
        } else {
            $this->role->permissions()->detach($moduleIds);
        }

        $this->assignedPermissionIds = $this->role->permissions()->pluck('permissions.id')->toArray();
    }

    public function render()
    {
        $permissionsByModule = Permission::where('advanced_management', false)->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('module', 'like', "%{$this->search}%");
            })
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return view('livewire.role-permission-manager', [
            'permissionsByModule' => $permissionsByModule,
        ]);
    }
}