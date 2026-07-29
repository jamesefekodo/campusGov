<?php

namespace App\Models\Finance\Transactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;
    protected $table = 'payment_transactions';
    protected $fillable = [
        'payment_transaction_id',
        'service_provider_id',
        'paystack_reference',
        'payment_mode',
        'registered_payer',
        'non_registered_payer',
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

    // public function associationProfile()
    // {
    //     return $this->belongsTo(AssociationProfile::class, 'association', 'username');
    // }
    // public function theStudent()
    // {
    //     return $this->belongsTo(StudentPersonalProfile::class, 'registered_payer', 'username');
    // }
    // public function theStudentAcademicInfo()
    // {
    //     return $this->belongsTo(StudentAcademicInfo::class, 'registered_payer', 'username');
    // }
    // public function undergraduateObligatoryDue()
    // {
    //     return $this->belongsTo(UndergraduateObligatoryFee::class, 'undergraduate_due_id', 'fee_id');
    // }
    // public function backlogDue()
    // {
    //     return $this->belongsTo(Pre2025BacklogDue::class, 'backlog_due_id', 'fee_id');
    // }
    // public function alumniObligatoryDue()
    // {
    //     return $this->belongsTo(AlumniObligatoryFee::class, 'alumni_due_id', 'fee_id');
    // }
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
