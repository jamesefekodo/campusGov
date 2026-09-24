<?php

namespace App\Models\Media;

use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class TagsForPublication extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'tags_for_publications';

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = 'publ-tag-' . date('Ymd') . '-' . Str::lower(Str::random(10));
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
    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(
            MediaPublication::class,
            'publication_tags',
            'tag_for_publication_id',
            'media_publication_id'
        )->withTimestamps();
    }
}
