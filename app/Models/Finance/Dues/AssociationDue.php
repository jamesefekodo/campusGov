<?php

namespace App\Models\Finance\Dues;

use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Finance\Transactions\PaymentWaiver;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberReAdmissionApplication;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AssociationDue extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'association_dues';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'fee_id',
        'title',
        'description',
        'amount',
        'is_active',
        'academic_year',
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
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = 'due-Y' . now()->format('Y') .
                    '-M' . now()->format('m') .
                    '-' . Str::uuid();
            }
        });
    }

    public function variations()
    {
        return $this->hasMany(AssociationDueVariation::class, 'due_id', 'fee_id');
    }
    // expected payers
    public function expectedPayerCount(): int
    {
        $count = 0;

        // Current programs
        MemberAcademicInfo::with(['theProgram', 'programType', 'theDepartment'])
            ->whereNotNull('admission_session')
            ->chunk(200, function ($records) use (&$count) {
                foreach ($records as $record) {

                    if (!$record->theProgram) {
                        continue;
                    }

                    $endAcademicYear = isStillWithinProgramDuration(
                        $record->admission_session,
                        $record->theProgram,
                        $record->programType,
                        $record->theDepartment
                    )
                        ? currentAcademicYear()
                        : ((int) $record->admission_session + (int) $record->theProgram->duration - 1);

                    if (
                        $this->academic_year >= (int) $record->admission_session &&
                        $this->academic_year <= $endAcademicYear
                    ) {
                        $count++;
                    }
                }
            });

        // Past programs (approved re-admissions)
        $count += MemberReAdmissionApplication::where('status', 'approved')
            ->whereNotNull('previous_admission_session')
            ->whereNotNull('previous_graduation_session')
            ->where('previous_admission_session', '<=', $this->academic_year)
            ->where('previous_graduation_session', '>=', $this->academic_year)
            ->count();

        return $count;
    }
    // successful payments
    public function successfulPaymentCount(): int
    {
        return PaymentTransaction::where('due_id', $this->fee_id)
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved')
            ->count();
    }

    // approved waivers
    public function approvedWaiverCount(): int
    {
        return PaymentWaiver::where('due_id', $this->fee_id)
            ->where('approval_status', 'approved')
            ->count();
    }
    // paid or waived
    public function membersPaidOrWaived(): Collection
    {
        $paidUsernames = PaymentTransaction::where('due_id', $this->fee_id)
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved')
            ->pluck('registered_payer')
            ->unique();

        $waivedUsernames = PaymentWaiver::where('due_id', $this->fee_id)
            ->where('approval_status', 'approved')
            ->pluck('username')
            ->unique();

        $usernames = $paidUsernames->merge($waivedUsernames)->unique();

        return MemberAcademicInfo::whereIn('username', $usernames)->get();
    }
    // unpaid and unwaived
    public function membersUnpaidAndUnwaived(): Collection
    {
        $excludedUsernames = $this->membersPaidOrWaived()->pluck('username');

        $unpaidRecords = collect();

        // Current programs
        MemberAcademicInfo::with(['theProgram', 'programType', 'theDepartment'])
            ->whereNotNull('admission_session')
            ->whereNotIn('username', $excludedUsernames)
            ->chunk(200, function ($records) use (&$unpaidRecords) {
                foreach ($records as $record) {

                    if (!$record->theProgram) {
                        continue;
                    }

                    $endAcademicYear = isStillWithinProgramDuration(
                        $record->admission_session,
                        $record->theProgram,
                        $record->programType,
                        $record->theDepartment
                    )
                        ? currentAcademicYear()
                        : ((int) $record->admission_session + (int) $record->theProgram->duration - 1);

                    if (
                        $this->academic_year >= (int) $record->admission_session &&
                        $this->academic_year <= $endAcademicYear
                    ) {
                        $unpaidRecords->push($record);
                    }
                }
            });

        // Past programs (approved re-admissions)
        $eligibleReAdmissionUsernames = MemberReAdmissionApplication::where('status', 'approved')
            ->whereNotNull('previous_admission_session')
            ->whereNotNull('previous_graduation_session')
            ->where('previous_admission_session', '<=', $this->academic_year)
            ->where('previous_graduation_session', '>=', $this->academic_year)
            ->whereNotIn('username', $excludedUsernames)
            ->pluck('username')
            ->unique();

        if ($eligibleReAdmissionUsernames->isNotEmpty()) {
            $pastProgramRecords = MemberAcademicInfo::whereIn('username', $eligibleReAdmissionUsernames)->get();
            $unpaidRecords = $unpaidRecords->merge($pastProgramRecords);
        }

        return $unpaidRecords->unique('username')->values();
    }
}
