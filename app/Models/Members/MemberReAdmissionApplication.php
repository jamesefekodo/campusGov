<?php

namespace App\Models\Members;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MemberReAdmissionApplication extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'member_re_admission_applications';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'username',

        'previous_program',
        'previous_program_type',
        'previous_faculty',
        'previous_department',
        'previous_admission_session',
        'previous_graduation_session',
        'previous_matric_number',
        'previous_academic_status',
        'new_program',
        'new_program_type',
        'new_faculty',
        'new_department',
        'new_admission_session',
        'new_matric_number',
        'status',
        'processed_by',
        'processed_at',
    ];

    // cast attributes
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'processed_at' => 'datetime',

        ];
    }


    // uuid
    protected static function booted(): void
    {
        static::creating(function ($role) {
            if (empty($role->uuid)) {
                $role->uuid = 're-admission-' . now()->format('Ym') . '-' . Str::uuid();
            }
        });
    }

    public function userProfile()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    public function personalProfile()
    {
        return $this->belongsTo(MemberPersonalProfile::class, 'username', 'username');
    }

    // previous
    public function previousProgram()
    {
        return $this->belongsTo(Program::class, 'previous_program', 'slug');
    }
    public function previousFaculty()
    {
        return $this->belongsTo(Faculty::class, 'new_faculty', 'slug');
    }
    public function previousDepartment()
    {
        return $this->belongsTo(Department::class, 'previous_department', 'slug');
    }
    public function previousProgramType()
    {
        return $this->belongsTo(ProgramType::class, 'previous_program_type', 'slug');
    }



    // new
    public function newProgram()
    {
        return $this->belongsTo(Program::class, 'new_program', 'slug');
    }
    public function newFaculty()
    {
        return $this->belongsTo(Faculty::class, 'new_faculty', 'slug');
    }
    public function newDepartment()
    {
        return $this->belongsTo(Department::class, 'new_department', 'slug');
    }
    public function newProgramType()
    {
        return $this->belongsTo(ProgramType::class, 'new_program_type', 'slug');
    }


    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'username');
    }
}
