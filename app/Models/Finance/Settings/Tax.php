<?php

namespace App\Models\Finance\Settings;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tax extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'taxes';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'tax_name',
        'tax_method',
        'tax_rate',
        'is_active',
    ];

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($tax) {
            if (empty($tax->uuid)) {
                $tax->uuid = 'tax-Y' . now()->format('Y') .
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
}
