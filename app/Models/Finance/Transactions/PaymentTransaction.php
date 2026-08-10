<?php

namespace App\Models\Finance\Transactions;

use App\Models\Finance\Dues\AssociationDue;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $table = 'payment_transactions';
    protected $fillable = [
        'payment_transaction_id',
        'tenant_id',
        'service_provider_id',
        'paystack_reference',
        'payment_mode',
        'payment_type',
        'registered_payer',
        'non_registered_payer',
        'phone',
        'email',
        'payment_purpose',
        'due_id',
        'invoice_id',
        'donation_id',
        'election_id',
        'election_position',
        'amount_payable',
        'amount_paid',
        'amount_settled',
        'service_charge',
        'total_tax',
        'taxes_paid',
        'electronic_payment_method',
        'manual_payment_method',
        'payment_status',
        'paid_at',
        'approval_status',
        'photo',
        'balance_update_status',
        'approved_by',
        'approved_at',

    ];

    public function due()
    {
        return $this->belongsTo(AssociationDue::class, 'due_id', 'fee_id');
    }

    public function registeredPayer()
    {
        return $this->belongsTo(User::class, 'registered_payer', 'username');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'username');
    }

    
    // public function onlinePaymentMethod()
    // {
    //     return $this->belongsTo(OnlinePaymentMethod::class, 'online_payment_method', 'slug');
    // }
    // public function invoice()
    // {
    //     return $this->belongsTo(AssociationInvoice::class, 'invoice_id', 'invoice_number');
    // }
    // public function donation()
    // {
    //     return $this->belongsTo(AssociationDonation::class, 'donation_id', 'donation_id');
    // }
    // public function election()
    // {
    //     return $this->belongsTo(Election::class, 'election_id', 'election_id');
    // }
    // public function electionPosition()
    // {
    //     return $this->belongsTo(ElectionPosition::class, 'election_position', 'id');
    // }
    // public function monetaryVote()
    // {
    //     return $this->belongsTo(MonetaryElectionVote::class, 'payment_transaction_id', 'payment_transaction_id');
    // }
}
