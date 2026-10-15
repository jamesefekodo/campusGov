<?php

namespace App\Models\AcademicResources;

use App\Models\AcademicResources\RecommendedCourse;
use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class TagsForRecommendedCourses extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'tags_for_recommended_courses';

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = 'rccs-tag-' . date('Ymd') . '-' . Str::lower(Str::random(10));
            }
        });
    }

    /**
     * The tenant that owns this tag.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Publications using this tag.
     */
    public function recommendedCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            RecommendedCourse::class,
            'recommended_course_tags',
            'tag_for_course_id',        // Foreign key on pivot for this model
            'recommended_course_id'     // Related key on pivot for RecommendedCourse
        )
            ->using(RecommendedCourseTag::class)
            ->withTimestamps();
    }
}
