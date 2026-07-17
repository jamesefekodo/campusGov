<?php

namespace App\Models\Config\SystemPolicies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAgreedPolicy extends Model
{
    use HasFactory;
    protected $table = 'user_agreed_policies';
    protected $fillable = [
        'username',
        'policy',
        'agreement_status',
        'agreement_date',
    ];

    public function policy()
    {
        return $this->belongsTo(SystemPolicy::class, 'policy', 'slug');
    }
}
