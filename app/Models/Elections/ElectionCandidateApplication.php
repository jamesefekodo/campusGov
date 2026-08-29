<?php

namespace App\Models\Elections;

use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\Program;
use App\Models\AcademicConfig\ProgramType;
use App\Models\Elections\Election;
use App\Models\Elections\ElectionPosition;
use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ElectionCandidateApplication extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'election_candidate_applications';
    protected $fillable = [
        'uuid',
        'tenant_id',
        'application_id',
        'election_id',
        'position_id',
        'candidate',
        'application_type',
        'current_program',
        'current_program_type',
        'current_faculty',
        'current_department',
        'level_at_application',
        'bio',
        'manifesto',
        'stakeholders_remarks',
        'application_form',
        'initiated_by',
        'status',
        'approved_at',
        'processed_by',
    ];

    // protected $casts = [
    //     'approved_at' => 'datetime',
    // ];

    // UUID & Application ID
    protected static function booted(): void
    {
        static::creating(function ($model) {

            // UUID
            if (empty($model->uuid)) {
                $model->uuid = 'eca-' . now()->format('Ymd') . '-' . Str::uuid();
            }

            // Application ID
            if (empty($model->application_id)) {
                $model->application_id =
                    'elec-app-' .
                    now()->format('Ymd-His') .
                    '-' .
                    Str::random(15);
            }
        });
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
        return $this->belongsTo(User::class, 'candidate', 'username');
    }
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'username');
    }
    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by', 'username');
    }

    public function allPaymentAttempts()
    {
        return $this->hasMany(
            PaymentTransaction::class,
            'election_candidate_application',
            'id'
        );
    }
    public function successfulPaymentTransaction()
    {
        return $this->hasOne(
            PaymentTransaction::class,
            'election_candidate_application',
            'id'
        )
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved');
    }

    public function standardVotes()
    {
        return $this->hasMany(
            ElectionStandardVote::class,
            'election_id',
            'election_id'
        )->where(
            'position',
            $this->position_id
        )->where('candidate', $this->application_id);
    }
    public function monetaryVotes()
    {
        return $this->hasMany(
            ElectionMonetaryVote::class,
            'election_id',
            'election_id'
        )->where(
            'position',
            $this->position_id
        )->where('candidate', $this->application_id)->where('votes_counted', true);
    }

 public function totalMonetaryVote()
{
    // Sum the amount_paid for valid votes
    $totalAmount = $this->hasMany(
        ElectionMonetaryVote::class,
        'election_id',
        'election_id'
    )
        ->where('position', $this->position_id)
        ->where('candidate', $this->application_id)
        ->where('votes_counted', true)
        ->sum('amount_paid');

    // Get the cost per vote from the election
    $costPerVote = $this->election->cost_per_vote;

    if ($costPerVote <= 0) {
        return 0;
    }

    // Convert the monetary amount into number of votes
    return (int) floor($totalAmount / $costPerVote);
}


    // public function allPaymentAttempts()
    // {
    //     return $this->hasMany(
    //         PaymentTransaction::class,
    //         'election_id',
    //         'election_id'
    //     )->where(
    //         'election_position',
    //         $this->position_id
    //     );
    // }

    // public function successfulPaymentTransaction()
    // {
    //     return $this->hasMany(
    //         PaymentTransaction::class,
    //         'election_id',
    //         'election_id'
    //     )
    //         ->where('election_position', $this->position_id)
    //         ->where('payment_status', 'successful')
    //         ->where('approval_status', 'approved');
    // }

    // public function successfulPaymentTransaction()
    // {
    //     return $this->hasOne(
    //         PaymentTransaction::class,
    //         'election_id',
    //         'election_id'
    //     )
    //         ->where('election_position', $this->position_id)
    //         ->where('payment_status', 'successful')
    //         ->where('approval_status', 'approved');
    // }
}
