<?php

namespace App\Models\AcademicConfig;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\HeadOfFaculty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faculty extends Model
{
    use HasFactory;
    protected $table = 'faculties';
    protected $fillable = [
        'uuid',
        'slug',
        'name',
        'acronym',
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
                $role->uuid = 'fac-' . Str::uuid();
            }
        });
    }

    public function facultyHead()
    {
        return $this->belongsTo(
            HeadOfFaculty::class,
            'slug',      // Local key on this model
            'faculty'    // Owner key on head_of_faculties
        )->where('is_active', true);
    }
    public function pastFacultyHeads()
    {
        return $this->hasMany(HeadOfFaculty::class, 'faculty', 'slug')->where('is_active', false);
    }
    public function departments()
    {
        return $this->hasMany(Department::class, 'faculty', 'slug');
    }
}
