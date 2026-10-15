<?php

namespace App\Models\AcademicResources;

use App\Models\AcademicResources\RecommendedCourse;
use App\Models\AcademicResources\TagsForRecommendedCourses;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendedCourseTag extends Pivot
{
    protected $table = 'recommended_course_tags';
    protected $fillable = [
        'recommended_course_id',
        'tag_for_course_id',
    ];

    /**
     * Because your pivot migration includes an "id" column.
     */
    public $incrementing = true;

    /**
     * The publication connected to this tag.
     */
    public function recommendedCourses(): BelongsTo
    {
        return $this->belongsTo(
            RecommendedCourse::class,
            'recommended_course_id'
        );
    }

    /**
     * The tag connected to this publication.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(
            TagsForRecommendedCourses::class,
            'tag_for_course_id'
        );
    }
}
