<?php

namespace App\Models\Finance\Dues;

use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociationDueVariation extends Model
{
    use HasFactory;
    protected $table = 'association_due_variations';
    protected $fillable = [
        'due_id',
        'admission_session',
        'program_slug',
        'program_type_slug',
        'amount',
    ];

    // cast attributes
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }

    public function due()
    {
        return $this->belongsTo(AssociationDue::class, 'due_id', 'fee_id');
    }
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_slug', 'slug');
    }
    public function programType()
    {
        return $this->belongsTo(ProgramType::class, 'program_type_slug', 'slug');
    }
}
