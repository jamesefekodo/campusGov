<?php

namespace App\Models\Members;

use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberSkill extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'member_skills';
    protected $fillable = [
        'tenant_id',
        'username',
        'skill',
        'description',
    ];

    public function userProfile()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
