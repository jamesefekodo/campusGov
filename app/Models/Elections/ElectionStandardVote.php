<?php

namespace App\Models\Elections;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Elections\Election;
use App\Models\Elections\ElectionPosition;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionStandardVote extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'election_standard_votes';
    protected $fillable = [
        'tenant_id',
        'election_id',
        'position',
        'candidate',
        'voter',
        'current_program',
        'current_program_type',
        'current_faculty',
        'current_department',
        'level_at_application',

    ];

    // protected $casts = [
    //     'approved_at' => 'datetime',
    // ];




    public function election()
    {
        return $this->belongsTo(Election::class, 'election_id', 'election_id');
    }
    public function position()
    {
        return $this->belongsTo(ElectionPosition::class, 'position_id', 'id');
    }
    public function theCandidate()
    {
        return $this->belongsTo(ElectionCandidateApplication::class, 'candidate', 'application_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'current_program', 'slug');
    }
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'current_faculty', 'slug');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'current_department', 'slug');
    }
    public function programType()
    {
        return $this->belongsTo(ProgramType::class, 'current_program_type', 'slug');
    }
}
