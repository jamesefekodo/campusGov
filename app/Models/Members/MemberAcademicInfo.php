<?php

namespace App\Models\Members;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Members\MemberPersonalProfile;
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

     public function personalProfile()
    {
        return $this->belongsTo(MemberPersonalProfile::class, 'username', 'username');
    }
    public function theProgram()
    {
        return $this->belongsTo(Program::class, 'program', 'slug');
    }
     public function theFaculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty', 'slug');
    }
    public function theDepartment()
    {
        return $this->belongsTo(Department::class, 'department', 'slug');
    }
    public function programType()
    {
        return $this->belongsTo(ProgramType::class, 'program_type', 'slug');
    }
}
