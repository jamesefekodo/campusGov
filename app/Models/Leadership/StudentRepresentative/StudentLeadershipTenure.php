<?php

namespace App\Models\Leadership\StudentRepresentative;

use App\Models\Leadership\StudentRepresentative\StudentLeadershipRepresentative;
use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StudentLeadershipTenure extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'student_leadership_tenures';
    protected $fillable = [
        'tenant_id',
        'slug',
        'name',
        'academic_session',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($tenure) {
            if (empty($tenure->slug)) {
                $tenure->slug = 'slt-' . Str::uuid();
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


    public function representatives(): HasMany
    {
        return $this->hasMany(
            StudentLeadershipRepresentative::class,
            'tenure_slug',
            'slug'
        );
    }
}
