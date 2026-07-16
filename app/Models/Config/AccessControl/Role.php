<?php

namespace App\Models\Config\AccessControl;

use App\Models\Config\AccessControl\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_active'
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'role_users'
        );
    }
}
