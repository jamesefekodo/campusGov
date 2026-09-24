<?php

namespace App\Models\Media;

use App\Models\Media\MediaPublication;
use App\Models\Media\TagsForPublication;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot; // <-- Change this

class PublicationTag extends Pivot
{
    protected $table = 'publication_tags';
    protected $fillable = [
        'media_publication_id',
        'tag_for_publication_id',
    ];

    /**
     * Because your pivot migration includes an "id" column.
     */
    public $incrementing = true;

    /**
     * The publication connected to this tag.
     */
    public function publication(): BelongsTo
    {
        return $this->belongsTo(
            MediaPublication::class,
            'media_publication_id'
        );
    }

    /**
     * The tag connected to this publication.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(
            TagsForPublication::class,
            'tag_for_publication_id'
        );
    }
}
