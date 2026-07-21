<?php

namespace App\Models\AcademicConfig;

use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\HeadOfDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory;
    protected $table = 'departments';
    protected $fillable = [
        'uuid',
        'slug',
        'faculty',
        'name',
        'administrative_head_title',
        'description',
        'is_active',
    ];


    // cast attributes
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($role) {
            if (empty($role->uuid)) {
                $role->uuid = 'dept-' . Str::uuid();
            }
        });
    }

    public function theFaculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty', 'slug');
    }
    public function hod()
    {
        return $this->belongsTo(HeadOfDepartment::class, 'slug', 'department')->where('is_active', true);
    }
    public function pastHods()
    {
        return $this->hasMany(HeadOfDepartment::class, 'department', 'slug')->where('is_active', false);
    }
    // public function studentsInDepartment()
    // {
    //     return $this->hasMany(StudentAcademicInfo::class, 'current_department', 'slug');
    // }
    // public function departmentAlumni()
    // {
    //     return StudentAcademicInfo::where('past_program_department', $this->slug)
    //         ->orWhere('past_program_2_department', $this->slug);
    // }
    // public function pastQuestions()
    // {
    //     return $this->hasMany(PastQuestion::class, 'department', 'slug');
    // }
}
