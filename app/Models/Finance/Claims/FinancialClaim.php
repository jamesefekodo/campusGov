<?php

namespace App\Models\Finance\Claims;

use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'description',
        'update_balance',
        'remarks',
        'initiated_by',
        'initiated_at',
        'processed_by',
        'approval_status',
        'approved_at',

    ];



    public function registeredPayer()
    {
        return $this->belongsTo(User::class, 'initiated_by', 'username');
    }
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'username');
    }

}
