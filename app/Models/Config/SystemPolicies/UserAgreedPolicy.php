<?php

namespace App\Models\Config\SystemPolicies;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAgreedPolicy extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'user_agreed_policies';
    protected $fillable = [
        'username',
        'tenant_id',
        'policy',
        'agreement_status',
        'agreement_date',
    ];

    public function policy()
    {
        return $this->belongsTo(SystemPolicy::class, 'policy', 'slug');
    }
}
