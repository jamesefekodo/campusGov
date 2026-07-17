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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'account_type',
        'account_status',
        'phone',
        'photo',
        'email',
        'require_login_mfa',
        'email_verified_at',
        'email_verification',
        'email_verification_code',
        'email_verification_code_expires_at',
        'password',
        'admin_privilege',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'account_type' => 'integer',
            'account_status' => 'integer',
            'email_verification' => 'boolean',
            'require_login_mfa'=> 'boolean',
            'admin_privilege' => 'boolean',
        ];
    }


    // static functions
    static public function getEmailCheck($email)
    {
        return User::where('email', '=', $email)->first();
    }
    static public function getTokenSingle($remember_token)
    {
        return User::where('remember_token', '=', $remember_token)->first();
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


    public function hasRole(string $role): bool
    {
        if ($this->account_type === 0) {
            return true;
        }

        return $this->roles()
            ->where('slug', $role)
            ->exists();

        // usage ... auth()->user()->hasRole('admin');

    }

    public function hasAnyRole(array $roles): bool
    {
        if ($this->account_type === 0) {
            return true;
        }

        return $this->roles()
            ->whereIn('slug', $roles)
            ->exists();

        // usage ...
        //  $user->hasAnyRole([
        //  'admin',
        //  'speaker'
        // ]);
    }

    public function hasPermission(string $permission): bool
    {
        // // Root account bypasses all permission checks
        if ($this->account_type === 0) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })
            ->exists();

        // usage $user->hasPermission('create-user');

    }
}
