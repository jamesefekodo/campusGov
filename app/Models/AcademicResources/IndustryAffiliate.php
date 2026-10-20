<?php

namespace App\Models\AcademicResources;

use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IndustryAffiliate extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'industry_affiliates';

    protected $fillable = [
        'tenant_id',
        'slug',
        'created_by',
        'title',
        'description',
        'cover_image',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];



    /**
     * The tenant that owns this publication.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by',
            'username'
        );
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            TagsForIndustryAffiliate::class,
            'recommended_course_tags',
            'recommended_course_id',
            'tag_for_course_id'
        )
            ->using(IndustryAffiliateTag::class)
            ->withTimestamps();
    }
}
