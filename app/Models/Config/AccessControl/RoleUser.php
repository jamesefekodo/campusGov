<?php

namespace App\Models\Config\AccessControl;

use App\Models\Config\AccessControl\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_users'
        );
    }

    public function permissions()
    {
        return $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }
}
