<?php

namespace App\Models\AcademicConfig;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramDurationOverride extends Model
{
    use HasFactory;
    protected $table = 'program_duration_overrides';
    protected $fillable = [
        'uuid',
        'program_slug',
        'program_type_slug',
        'department_slug',
        'duration',
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
                $role->uuid = 'durexc-' . Str::uuid();
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_slug', 'slug');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_slug', 'slug');
    }
    public function programType()
    {
        return $this->belongsTo(ProgramType::class, 'program_type_slug', 'slug');
    }
}
