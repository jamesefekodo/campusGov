<?php

namespace App\Models\Finance\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualPaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'manual_payment_methods';
    protected $fillable = [
        'cash',
        'bank_transfer',
        'cheque',
        'allow_manual_dues',
    ];

    // cast attributes
    protected function casts(): array
    {
        return [
            'cash' => 'boolean',
            'bank_transfer' => 'boolean',
            'cheque' => 'boolean',
            'allow_manual_dues' => 'boolean',
        ];
    }
}
