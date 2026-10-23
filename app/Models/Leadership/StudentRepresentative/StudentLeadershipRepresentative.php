<?php

namespace App\Models\Leadership\StudentRepresentative;

use App\Models\Leadership\StudentRepresentative\StudentLeadershipArm;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipPosition;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipTenure;
use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class StudentLeadershipRepresentative extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'student_leadership_representative';
    protected $fillable = [
        'tenant_id',
        'uuid',
        'username',
        'position_slug',
        'tenure_slug',
        'description',
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
                $role->uuid = 'slr-' . Str::uuid();
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
   
    public function position(): BelongsTo
    {
        return $this->belongsTo(
            StudentLeadershipPosition::class,
            'position_slug',
            'slug'
        );
    }

    public function tenure(): BelongsTo
    {
        return $this->belongsTo(
            StudentLeadershipTenure::class,
            'tenure_slug',
            'slug'
        );
    }
}
