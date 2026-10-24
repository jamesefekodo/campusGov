<?php

namespace App\Models\Leadership\StudentRepresentative;

use App\Models\Leadership\StudentRepresentative\StudentLeadershipTenure;
use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AssociationAdvisor extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'association_advisors';
    protected $fillable = [
        'tenant_id',
        'uuid',
        'name',
        'position',
        'photo',
        'tenure_slug',
        'remarks',
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
                $role->uuid = 'aadv-' . Str::uuid();
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

    public function tenure(): BelongsTo
    {
        return $this->belongsTo(
            StudentLeadershipTenure::class,
            'tenure_slug',
            'slug'
        );
    }
}
