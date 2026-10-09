<?php

namespace App\Models\AcademicResources;

use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RecommendedBook extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'recommended_books';

    protected $fillable = [
        'slug',
        'created_by',
        'title',
        'description',
        'cover_image',
        'is_published',
        'file_url',
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
            TagsForRecommendedBooks::class,
            'recommended_book_tags',
            'recommended_book_id',
            'tag_for_book_id'
        )
            ->using(RecommendedBookTag::class)
            ->withTimestamps();
    }
}
