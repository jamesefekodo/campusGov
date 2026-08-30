<?php

namespace App\Models\Finance\Claims;

use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FinancialClaim extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'financial_claims';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'claim_id',
        'amount',
        'date',
        'claim_type',
        'description',
        'balance_update',
        'remarks',
        'initiated_by',
        'initiated_at',
        'processed_by',
        'approval_status',
        'approved_at',

    ];

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'fincl-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by', 'username');
    }
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'username');
    }
}
