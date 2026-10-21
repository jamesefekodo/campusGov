<?php

namespace App\Models\Archive;

use App\Models\Tenancy\Tenant;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ArchiveFile extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'archive_files';

    protected $fillable = [
        'tenant_id',
        'uuid',
        'created_by',
        'title',
        'description',
        'members_can_see',
        'last_update_by',
    ];

    protected $casts = [
        'members_can_see' => 'boolean',
    ];

    // cast
     protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = 'file-' . date('Ymd') . '-' . Str::lower(Str::random(10));
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
}
