<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Config\AccessControl\Role;
use App\Models\Media\MemberSocialMediaHandle;
use App\Models\Members\MemberAcademicHistory;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberAward;
use App\Models\Members\MemberEmploymentAndBusiness;
use App\Models\Members\MemberLeadershipPosition;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Members\MemberSkill;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

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
        'uuid',
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
            'require_login_mfa' => 'boolean',
            'admin_privilege' => 'boolean',
        ];
    }


    // uuid
    protected static function booted(): void
    {
        static::creating(function ($program) {
            if (empty($program->uuid)) {
                $program->uuid = 'user-Y' . now()->format('Y') .
                    '-M' . now()->format('m') .
                    '-' . Str::uuid();
            }
        });
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


    // relational model
    public function memberPersonalProfile()
    {
        return $this->belongsTo(MemberPersonalProfile::class, 'username', 'username');
    }
    public function memberAcademicInfo()
    {
        return $this->belongsTo(MemberAcademicInfo::class, 'username', 'username');
    }
    public function memberAcademicHistories()
    {
        return $this->hasMany(MemberAcademicHistory::class, 'username', 'username');
    }
    public function memberSkills()
    {
        return $this->hasMany(MemberSkill::class, 'username', 'username');
    }
    public function memberEmploymentAndBusiness()
    {
        return $this->hasMany(MemberEmploymentAndBusiness::class, 'username', 'username');
    }
    public function memberLeadershipPosition()
    {
        return $this->hasMany(MemberLeadershipPosition::class, 'username', 'username');
    }
    public function memberAwards()
    {
        return $this->hasMany(MemberAward::class, 'username', 'username');
    }
      public function memberSocialMediaHandle()
    {
        return $this->hasMany(MemberSocialMediaHandle::class, 'username', 'username');
    }
}
