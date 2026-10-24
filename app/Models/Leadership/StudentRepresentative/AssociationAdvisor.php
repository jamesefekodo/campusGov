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
        'remarks',
        'start_date',
        'end_date',
        // 'is_active',
    ];

    protected $casts = [
        // 'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
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
}
