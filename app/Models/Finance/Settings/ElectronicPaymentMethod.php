<?php

namespace App\Models\Finance\Settings;

use App\Models\Finance\Settings\ElectronicPaymentConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ElectronicPaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'electronic_payment_methods';
    protected $fillable = [
        'uuid',
        'slug',
        'name',
        'logo',
        'default_api',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = 'paymetd-Y' . now()->format('Y') .
                    '-M' . now()->format('m') .
                    '-' . Str::uuid();
            }
        });
    }

    // cast attributes
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function apiConfig()
    {
        return $this->belongsTo(ElectronicPaymentConfig::class, 'slug', 'payment_method');
    }

    // public function defaultApi()
    // {
    //     return $this->belongsTo(ElectronicPaymentConfig::class, 'default_api', 'id');
    // }
}
