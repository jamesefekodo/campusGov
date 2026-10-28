<?php

namespace App\Models\Event;

use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'events';
    protected $fillable = [
        'tenant_id',
        'uuid',
        'title',
        'description',
        'venue',
        'cover_image',
        'event_start',
        'event_end',
        'is_published',
        'created_by',
        'approved_by',
        'approval_status',
        'last_update',
        'last_update_by',
        'last_approval_update',

    ];

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'evt-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

     protected $casts = [
         'approval_status' => 'boolean',
        'last_update' => 'datetime',
        'last_approval_update' => 'datetime',
    ];


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'username');
    }
     public function lastUpdateBy()
    {
        return $this->belongsTo(User::class, 'last_update_by', 'username');
    }
}
