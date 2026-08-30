<?php

namespace App\Models\Finance\Claims;

use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'invoices';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'invoice_number',
        'registered_payer',
        'non_registered_payer',
        'amount',
        'description',
        'issue_date',
        'due_date',
        'status',
        'created_by',
    ];

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'inv-' . date('Ymd') . '-' . Str::uuid();
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }
     public function invoiceFor()
    {
        return $this->belongsTo(User::class, 'registered_payer', 'username');
    }
}
