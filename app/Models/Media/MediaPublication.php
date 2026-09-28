<?php

namespace App\Models\Media;

use App\Models\Media\PublicationComment;
use App\Models\Media\TagsForPublication;
use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MediaPublication extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'media_publications';

    protected $fillable = [
        'tenant_id',
        'uuid',
        'author',
        'title',
        'content',
        'cover_image',
        'meta_description',
        'meta_keywords',
        'views',
        'shares',
        'approval_status',
        'approved_by',
        'published_at',
        'is_active',
        'last_update',
        'last_update_by',
        'last_approval_update',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'last_update' => 'datetime',
        'last_approval_update' => 'datetime',
        'is_active' => 'boolean',
        'views' => 'integer',
        'shares' => 'integer',
    ];


    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'medpub-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

    /**
     * The tenant that owns this publication.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * User who created/wrote the publication.
     *
     * Uses username because your media_publications.author column
     * references users.username rather than users.id.
     */
    public function authorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author', 'username');
    }

    /**
     * User who approved the publication.
     *
     * Uses username because approved_by references users.username.
     */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'username');
    }

    /**
     * Tags attached to this publication.
     */
    // public function tags(): BelongsToMany
    // {
    //     return $this->belongsToMany(
    //         TagsForPublication::class,
    //         'publication_tags',
    //         'media_publication_id',
    //         'tag_for_publication_id'
    //     )->withTimestamps();
    // }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            TagsForPublication::class,
            'publication_tags',
            'media_publication_id',
            'tag_for_publication_id'
        )
            ->using(PublicationTag::class)
            ->withTimestamps();
    }

    public function lastUpdateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_update_by', 'username');
    }

    /*
 * All post likes.
 */
    public function likes(): HasMany
    {
        return $this->hasMany(
            PublicationLike::class,
            'media_publication_id'
        );
    }

    /*
 * All comments and replies.
 */
    public function comments(): HasMany
    {
        return $this->hasMany(
            PublicationComment::class,
            'media_publication_id'
        );
    }

    /*
 * Main comments only.
 * Replies are accessed with $comment->replies.
 */
    public function mainComments(): HasMany
    {
        return $this->hasMany(
            PublicationComment::class,
            'media_publication_id'
        )
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->where('is_active', true)
            ->latest();
    }
}
