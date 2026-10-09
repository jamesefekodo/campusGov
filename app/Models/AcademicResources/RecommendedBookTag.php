<?php

namespace App\Models\AcademicResources;

use App\Models\AcademicResources\RecommendedBook;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendedBookTag extends Pivot
{
    protected $table = 'recommended_book_tags';
    protected $fillable = [
        'recommended_book_id',
        'tag_for_book_id',
    ];

    /**
     * Because your pivot migration includes an "id" column.
     */
    public $incrementing = true;

    /**
     * The publication connected to this tag.
     */
    public function recommendedBooks(): BelongsTo
    {
        return $this->belongsTo(
            RecommendedBook::class,
            'recommended_book_id'
        );
    }

    /**
     * The tag connected to this publication.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(
            TagsForRecommendedBooks::class,
            'tag_for_book_id'
        );
    }
}
