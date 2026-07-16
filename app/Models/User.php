<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Config\AccessControl\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    // relationships
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


    public function hasRole($role)
    {
        return $this->roles()
            ->where('slug', $role)
            ->exists();

        // usage ... auth()->user()->hasRole('admin');
    }
    public function hasAnyRole(array $roles)
    {
        return $this->roles()
            ->whereIn('slug', $roles)
            ->exists();

        // usage ...
        //  $user->hasAnyRole([
        //  'admin',
        //  'speaker'
        // ]);
    }
    public function hasPermission($permission)
    {
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permission) {
                $q->where('slug', $permission);
            })
            ->exists();

        // usage $user->hasPermission('create-user');
    }
}
