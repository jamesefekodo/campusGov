<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAward extends Model
{
    use HasFactory;
    protected $table = 'member_awards';
    protected $fillable = [
        'username',
        'award',
        'organization',
        'date',
    ];
}
