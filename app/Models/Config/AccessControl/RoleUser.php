<?php

namespace App\Models\Config\AccessControl;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_users';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role_id',
    ];


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

