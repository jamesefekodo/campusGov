<?php

namespace App\Models\Elections;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ElectionPositionQualifiedCandidateGroup extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'position_qualified_candidate_groups';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'position_id',
        'election_id',
        'program',
        'program_type',
        'faculty',
        'department',
        'start_level',
        'end_level',
    ];

    // protected $casts = [
    //     'approved_at' => 'datetime',
    // ];

    // uuid
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = 'eqc-' . date('Ymd') . '-' . Str::uuid();
            }
        });
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
    public function election()
    {
        return $this->belongsTo(Election::class, 'election_id', 'election_id');
    }
    public function position()
    {
        return $this->belongsTo(ElectionPosition::class, 'election_id', 'election_id');
    }
}
