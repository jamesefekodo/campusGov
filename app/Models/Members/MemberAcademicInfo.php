<?php

namespace App\Models\Members;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Finance\Dues\AssociationDue;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAcademicInfo extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'member_academic_infos';
    protected $fillable = [
        'tenant_id',
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

    // synchronize academic levels per change in academic year

    public static function syncStudentStatuses(): void
    {
        static::with([
            'theProgram',
            'programType',
            'theDepartment',
        ])
            ->whereIn('academic_status', ['student', 'unknown'])
            ->chunk(200, function ($records) {

                foreach ($records as $record) {

                    if (!$record->theProgram) {
                        continue;
                    }

                    $isStudent = isStillWithinProgramDuration(
                        $record->admission_session,
                        $record->theProgram,
                        $record->programType,
                        $record->theDepartment
                    );

                    if ($isStudent && $record->academic_status === 'unknown') {

                        $record->update([
                            'academic_status' => 'student',
                        ]);
                    } elseif (!$isStudent && $record->academic_status === 'student') {

                        $record->update([
                            'academic_status' => 'unknown',
                        ]);
                    }
                }
            });
    }


    public function associationDues()
    {
        if (!$this->theProgram) {
            return collect();
        }
        $endAcademicYear = isStillWithinProgramDuration(
            $this->admission_session,
            $this->theProgram,
            $this->programType,
            $this->theDepartment
        )
            ? currentAcademicYear()
            : (
                (int) $this->admission_session +
                (int) $this->theProgram->duration - 1
            );

        return AssociationDue::with('variations')
            ->where('is_active', true)
            ->whereBetween('academic_year', [
                (int) $this->admission_session,
                $endAcademicYear,
            ])
            ->get()
            ->map(function ($due) {

                $variation = $due->variations
                    ->sortByDesc(function ($variation) {
                        return (!is_null($variation->admission_session)) * 4 +
                            (!is_null($variation->program_slug)) * 2 +
                            (!is_null($variation->program_type_slug));
                    })
                    ->first(function ($variation) {
                        return (
                            is_null($variation->admission_session) ||
                            $variation->admission_session == $this->admission_session
                        )
                            &&
                            (
                                is_null($variation->program_slug) ||
                                $variation->program_slug == $this->program
                            )
                            &&
                            (
                                is_null($variation->program_type_slug) ||
                                $variation->program_type_slug == $this->program_type
                            );
                    });

                $due->payable_amount = $variation?->amount ?? $due->amount;
                $due->matchedVariation = $variation;

                return $due;
            });
    }
}
