<?php

namespace App\Models\Media;

use App\Models\Media\PublicationCommentLike;
use App\Models\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PublicationComment extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'media_publication_id',
        'parent_id',
        'username',
        'comment',
        'status',
        'is_active',
        'edited_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'edited_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PublicationComment $comment) {
            if (empty($comment->uuid)) {
                $comment->uuid = (string) Str::uuid();
            }
        });
    }

    /*
     * Publication/post that owns this comment.
     */
    public function publication(): BelongsTo
    {
        return $this->belongsTo(
            MediaPublication::class,
            'media_publication_id'
        );
    }

    /*
     * User that created this comment.
     *
     * First key: this table's column.
     * Second key: users table's column.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'username',
            'username'
        );
    }

    /*
     * Tenant that owns this comment.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }

    /*
     * Main comment being replied to.
     * Null means this is a normal main comment.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    /*
     * Direct replies under this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        )
            ->where('status', 'approved')
            ->where('is_active', true)
            ->oldest();
    }

    /*
     * Likes on this comment or reply.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(
            PublicationCommentLike::class,
            'publication_comment_id'
        );
    }

    /*
     * Only approved and active comments.
     */
    public function scopeVisible($query)
    {
        return $query
            ->where('status', 'approved')
            ->where('is_active', true);
    }

    /*
     * Only top-level comments.
     */
    public function scopeMainComments($query)
    {
        return $query->whereNull('parent_id');
    }
}
