<?php

namespace App\Models\Finance\Donations;

use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Donation extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'donations';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'donation_id',
        'title',
        'description',
        'is_active',
        'target_amount',
        'deadline',
        'created_by',
        'processed_by',
        'approval_status',
        'approved_at',
    ];

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'don-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'username');
    }
    public function successfulPayments()
    {
        return $this->hasMany(PaymentTransaction::class, 'donation_id', 'donation_id')
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved');
    }

    public function getAmountRaisedAttribute(): float
    {
        return $this->hasMany(PaymentTransaction::class, 'donation_id', 'donation_id')
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved')
            ->sum('amount_settled');
    }

    public function failedPayments()
    {
        return $this->hasMany(PaymentTransaction::class, 'donation_id', 'donation_id')
            ->whereNotIn('payment_status', ['successful', 'pending'])
            ->whereNotIn('approval_status', ['approved', 'pending']);
    }

    public function paymentsPendingApproval()
    {
        return $this->hasMany(PaymentTransaction::class, 'donation_id', 'donation_id')
            ->where('payment_status', 'pending')
            ->where('approval_status', 'pending');
    }
}
