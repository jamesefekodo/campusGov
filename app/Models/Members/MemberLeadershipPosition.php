<?php

namespace App\Models\Members;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberLeadershipPosition extends Model
{
    use HasFactory;
    protected $table = 'member_leaderhip_positions';
    protected $fillable = [
        'username',
        'organization',
        'is_current',
        'address',
        'position',
        'start_date',
        'end_date',
    ];

    public function userProfile()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
