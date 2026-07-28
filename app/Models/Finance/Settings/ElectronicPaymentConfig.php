<?php

namespace App\Models\Finance\Settings;

use App\Models\Finance\Settings\ElectronicPaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectronicPaymentConfig extends Model
{
    use HasFactory;
    protected $table = 'electronic_payment_config';
    protected $fillable = [
        'payment_method',
        'client_id',
        'public_key',
        'secret_key',
        'encryption_key',
        'version',
        'is_active',
    ];

    // cast attributes
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    public function paymentMethod()
    {
        return $this->belongsTo(ElectronicPaymentMethod::class, 'payment_method', 'slug');
    }
}
