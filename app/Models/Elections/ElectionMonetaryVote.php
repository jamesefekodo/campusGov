<?php

namespace App\Models\Elections;

use App\Models\Elections\Election;
use App\Models\Elections\ElectionCandidateApplication;
use App\Models\Elections\ElectionPosition;
use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionMonetaryVote extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'election_monetary_votes';
    protected $fillable = [
        'tenant_id',
        'election_id',
        'position',
        'candidate',
        'registered_voter',
        'unregistered_voter',
        'voter_email',
        'voter_phone',
        'no_of_votes',
        'votes_counted',
        'payment_transaction_id',
        'amount_paid',
        'cost_per_vote',

    ];

    protected $casts = [
        'votes_counted' => 'boolean',
    ];




    public function election()
    {
        return $this->belongsTo(Election::class, 'election_id', 'election_id');
    }
    public function thePosition()
    {
        return $this->belongsTo(ElectionPosition::class, 'position_id', 'id');
    }
    public function theCandidate()
    {
        return $this->belongsTo(ElectionCandidateApplication::class, 'candidate', 'application_id');
    }
    public function voter()
    {
        return $this->belongsTo(User::class, 'registered_voter', 'username');
    }
    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id', 'payment_transaction_id');
    }
}
