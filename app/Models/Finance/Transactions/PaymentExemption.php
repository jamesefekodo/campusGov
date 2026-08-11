<?php

namespace App\Models\Finance\Transactions;

use App\Models\Finance\Dues\AssociationDue;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentExemption extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'payment_exemptions';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'exemption_id',
        'username',
        'due_id',
        'reason',
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
                $model->uuid = 'payexe-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

    public function due()
    {
        return $this->belongsTo(AssociationDue::class, 'due_id', 'fee_id');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
    public function personalProfile()
    {
        return $this->belongsTo(MemberPersonalProfile::class, 'username', 'username');
    }
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'username');
    }
    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by', 'username');
    }
}
