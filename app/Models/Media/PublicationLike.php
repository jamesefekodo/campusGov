<?php

namespace App\Models\Media;

use App\Models\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationLike extends Model
{
    protected $fillable = [
        'tenant_id',
        'media_publication_id',
        'username',
    ];

    /*
     * The publication/post liked by the user.
     */
    public function publication(): BelongsTo
    {
        return $this->belongsTo(
            MediaPublication::class,
            'media_publication_id'
        );
    }

    /*
     * User who liked the publication.
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
