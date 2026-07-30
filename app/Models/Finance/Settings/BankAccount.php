<?php

namespace App\Models\Finance\Settings;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'bank_accounts';
    protected $fillable = [
        'account_number',
        'tenant_id',
        'account_name',
        'bank',
        'is_active',
    ];

    // cast attributes
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
