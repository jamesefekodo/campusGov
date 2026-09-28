<?php

namespace App\Models\Media;

use App\Models\Media\PublicationComment;
use App\Models\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationCommentLike extends Model
{
    protected $fillable = [
        'tenant_id',
        'publication_comment_id',
        'username',
    ];

    /*
     * The comment or reply that received the like.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(
            PublicationComment::class,
            'publication_comment_id'
        );
    }

    /*
     * User who liked the comment or reply.
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
     * Tenant that owns this like record.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }
}
