<?php

namespace App\Models\Elections;

use App\Models\Elections\Election;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ElectionPosition extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'election_positions';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'election_id',
        'position',
        'description',
        'application_fee',
    ];


    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'elpo-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

    public function election()
    {
        return $this->belongsTo(Election::class, 'election_id', 'election_id');
    }
    public function candidateGroups()
    {
        return $this->hasMany(ElectionPositionQualifiedCandidateGroup::class, 'position_id', 'id');
    }
}
