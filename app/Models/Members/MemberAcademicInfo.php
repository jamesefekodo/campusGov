<?php

namespace App\Models\Members;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAcademicInfo extends Model
{
    use HasFactory;
    protected $table = 'member_academic_infos';
    protected $fillable = [

        'username',
        'academic_status',
        'program',
        'program_type',
        'department',
        'faculty',
        'admission_session',
        'graduation_session',
        'matric_number',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    // cast attributes
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean'
        ];
    }
    public function userProfile()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
