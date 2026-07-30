<?php

namespace App\Models\Members;

use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAcademicHistory extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'member_academic_histories';
    protected $fillable = [
        'username',
        'tenant_id',
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
