<?php

namespace App\Models\Leadership\StudentRepresentative;

use App\Models\Leadership\StudentRepresentative\StudentLeadershipPosition;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipRepresentative;
use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StudentLeadershipArm extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'student_leadership_arms';
    protected $fillable = [
        'tenant_id',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($role) {
            if (empty($role->uuid)) {
                $role->uuid = 'sla-' . Str::uuid();
            }
        });
    }


    /**
     * The tenant that owns this .
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    public function positions(): HasMany
    {
        return $this->hasMany(
            StudentLeadershipPosition::class,
            'arm_slug',
            'slug'
        );
    }

    public function representatives(): HasMany
    {
        return $this->hasMany(
            StudentLeadershipRepresentative::class,
            'arm_slug',
            'slug'
        );
    }
}
