<?php

namespace App\Models\Members;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAcademicHistory extends Model
{
    use HasFactory;
    protected $table = 'member_academic_histories';
    protected $fillable = [
        'username',
        'institution',
        'address',
        'certificate',
        'date',
    ];

    public function userProfile()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
