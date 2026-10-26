<?php

namespace App\Models\Tenancy;

use App\Models\Admin\AdminProfile;
use App\Models\Config\BasicSystemSetting;
use App\Models\Theme\Frontend\TenantThemeSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasUuids;

    protected $fillable = [

        'uuid',
        'id',

        'name',
        'slug',
        'code',

        'logo',
        'favicon',

        'email',
        'phone',
        'address',

        'website',

        'domain',
        'subdomain',

        'is_active',
        'is_verified',

        'subscription_starts_at',
        'subscription_expires_at',

        'description',
    ];

    protected function casts(): array
    {
        return [

            'is_active' => 'boolean',
            'is_verified' => 'boolean',

            'subscription_starts_at' => 'date',
            'subscription_expires_at' => 'date',

        ];
    }

    protected static function booted()
    {
        static::creating(function ($tenant) {

            if (empty($tenant->uuid)) {
                $tenant->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }


    // relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function settings()
    {
        return $this->hasOne(BasicSystemSetting::class);
    }

    public function tenantAdministrators()
    {
        return $this->hasMany(AdminProfile::class, 'tenant_id', 'id');
    }
    public function themeSetting()
    {
        return $this->hasOne(TenantThemeSetting::class);
    }
}
