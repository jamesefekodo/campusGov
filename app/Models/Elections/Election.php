<?php

namespace App\Models\Elections;

use App\Models\Elections\ElectionGeneralQualifiedCandidateGroup;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Election extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'elections';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'election_id',
        'voting_method',
        'public_visibility',
        'title',
        'academic_session',
        'description',
        'status',
        'form_sales_starts',
        'form_sales_ends',
        'screening_starts',
        'screening_ends',
        'campaign_starts',
        'campaign_ends',
        'voting_starts',
        'voting_ends',
        'cost_per_vote',
        'initiated_by',
        'initiated_at',
        'processed_by',
        'approval_status',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'public_visibility' => 'boolean',
    ];

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'elec-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'username');
    }
    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by', 'username');
    }
     public function generalCandidateGroups()
    {
        return $this->hasMany(ElectionGeneralQualifiedCandidateGroup::class, 'initiated_by', 'username');
    }
}
