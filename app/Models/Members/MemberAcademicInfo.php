<?php

namespace App\Models\Members;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Finance\Dues\AssociationDue;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Members\MemberReAdmissionApplication;
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
    // A: changes to unkown status
    // public static function syncStudentStatuses(): void
    // {
    //     static::with([
    //         'theProgram',
    //         'programType',
    //         'theDepartment',
    //     ])
    //         ->whereIn('academic_status', ['student', 'unknown'])
    //         ->chunk(200, function ($records) {

    //             foreach ($records as $record) {

    //                 if (!$record->theProgram) {
    //                     continue;
    //                 }

    //                 $isStudent = isStillWithinProgramDuration(
    //                     $record->admission_session,
    //                     $record->theProgram,
    //                     $record->programType,
    //                     $record->theDepartment
    //                 );

    //                 if ($isStudent && $record->academic_status === 'unknown') {

    //                     $record->update([
    //                         'academic_status' => 'student',
    //                     ]);
    //                 } elseif (!$isStudent && $record->academic_status === 'student') {

    //                     $record->update([
    //                         'academic_status' => 'unknown',
    //                     ]);
    //                 }
    //             }
    //         });
    // }

    // B: changes to alumni
    public static function syncStudentStatuses(): void
    {
        static::with([
            'theProgram.durationOverrides',
            'programType',
            'theDepartment',
        ])
            ->whereIn('academic_status', ['student', 'unknown', 'alumni'])
            ->chunk(200, function ($records) {

                foreach ($records as $record) {

                    if (!$record->theProgram || !$record->admission_session) {
                        continue;
                    }

                    $expectedGraduationSession = expectedGraduationSession(
                        $record->admission_session,
                        $record->theProgram,
                        $record->programType,
                        $record->theDepartment
                    );

                    if (!$expectedGraduationSession) {
                        continue;
                    }

                    /*
                 * The student remains a student throughout
                 * the expected graduation academic year.
                 *
                 * They become alumni only after that academic
                 * year has ended and currentAcademicYear() moves
                 * to the next session.
                 */
                    $shouldBeAlumni =
                        currentAcademicYear() > $expectedGraduationSession;

                    if ($shouldBeAlumni) {

                        if (
                            $record->academic_status === 'student' ||
                            $record->academic_status === 'unknown'
                        ) {
                            $record->update([
                                'academic_status' => 'alumni',
                                'graduation_session' => $expectedGraduationSession,
                            ]);
                        } elseif ($record->academic_status === 'alumni') {

                            // Keep the graduation session synchronized.
                            if (
                                (int) $record->graduation_session !==
                                (int) $expectedGraduationSession
                            ) {
                                $record->update([
                                    'graduation_session' => $expectedGraduationSession,
                                ]);
                            }
                        }
                    } else {

                        /*
                     * The student is still within their program.
                     *
                     * This allows an incorrectly classified alumni
                     * record to safely return to student.
                     */
                        if (
                            $record->academic_status === 'alumni' ||
                            $record->academic_status === 'unknown'
                        ) {
                            $record->update([
                                'academic_status' => 'student',
                            ]);
                        }
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

    // past programs
    public function approvedReAdmissions()
    {
        return $this->hasMany(
            MemberReAdmissionApplication::class,
            'username',
            'username'
        )->where('status', 'approved');
    }

    public function pastProgramsAssociationDues()
    {
        return $this->approvedReAdmissions()
            ->get()
            ->mapWithKeys(function ($reAdmission) {

                $dues = AssociationDue::with('variations')
                    ->where('is_active', true)
                    ->whereBetween('academic_year', [
                        (int) $reAdmission->previous_admission_session,
                        (int) $reAdmission->previous_graduation_session,
                    ])
                    ->get()
                    ->map(function ($due) use ($reAdmission) {

                        $variation = $due->variations
                            ->sortByDesc(function ($variation) {
                                return (!is_null($variation->admission_session) ? 4 : 0) +
                                    (!is_null($variation->program_slug) ? 2 : 0) +
                                    (!is_null($variation->program_type_slug) ? 1 : 0);
                            })
                            ->first(function ($variation) use ($reAdmission) {

                                return (
                                    is_null($variation->admission_session) ||
                                    $variation->admission_session == $reAdmission->previous_admission_session
                                )
                                    &&
                                    (
                                        is_null($variation->program_slug) ||
                                        $variation->program_slug == $reAdmission->previous_program
                                    )
                                    &&
                                    (
                                        is_null($variation->program_type_slug) ||
                                        $variation->program_type_slug == $reAdmission->previous_program_type
                                    );
                            });

                        $due->payable_amount = $variation?->amount ?? $due->amount;
                        $due->matchedVariation = $variation;
                        $due->reAdmissionApplication = $reAdmission;

                        return $due;
                    });

                return [
                    $reAdmission->uuid => $dues,
                ];
            });
    }
}
