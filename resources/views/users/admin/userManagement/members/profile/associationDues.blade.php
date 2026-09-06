@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? '') . ' - Association Dues')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')
                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Association Dues
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    @forelse($data['member']->memberAcademicInfo->associationDues() as $due)
                                        <div class="col-md-12 mb-3">
                                            <div class="card shadow-sm border-0">

                                                {{-- Header --}}
                                                <div
                                                    class="card-header bg-blue text-white d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0 text-white">
                                                        {{ $due->title }}
                                                    </h5>

                                                    <span class="badge bg-light text-dark">
                                                        {{ $due->academic_year }}/{{ $due->academic_year + 1 }}
                                                    </span>
                                                </div>

                                                {{-- Body --}}
                                                <div class="card-body">

                                                    @if ($due->description)
                                                        <p class="text-muted mb-4">
                                                            {{ $due->description }}
                                                        </p>
                                                    @endif

                                                    <div class="row">

                                                        <div class="col-md-4 mb-3">
                                                            <small class="text-muted d-block">Amount Payable</small>
                                                            <h4 class="mb-0 text-success">
                                                                ₦{{ number_format($due->payable_amount, 2) }}
                                                            </h4>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <small class="text-muted d-block">Default Amount</small>
                                                            ₦{{ number_format($due->amount, 2) }}
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <small class="text-muted d-block">Variation Applied</small>

                                                            @if ($due->matchedVariation)
                                                                <span class="badge bg-success">
                                                                    Yes
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    No
                                                                </span>
                                                            @endif
                                                        </div>

                                                    </div>

                                                    @if ($due->matchedVariation)
                                                        <hr>

                                                        <h6 class="mb-3">
                                                            Applied Variation
                                                        </h6>

                                                        <div class="row">

                                                            <div class="col-md-4">
                                                                <strong>Admission Session</strong><br>

                                                                @if ($due->matchedVariation?->admission_session)
                                                                    {{ $due->matchedVariation->admission_session }}/{{ $due->matchedVariation->admission_session + 1 }}

                                                                    @php
                                                                        $year =
                                                                            $due->academic_year -
                                                                            $due->matchedVariation->admission_session +
                                                                            1;
                                                                    @endphp

                                                                    -
                                                                    @if ($year == 1)
                                                                        (Freshers)
                                                                    @else
                                                                        (Year {{ $year }})
                                                                    @endif
                                                                @else
                                                                    All
                                                                @endif
                                                            </div>

                                                            <div class="col-md-4">
                                                                <strong>Program</strong><br>

                                                                {{ $due->matchedVariation->program?->name ?? 'All' }}
                                                            </div>

                                                            <div class="col-md-4">
                                                                <strong>Program Type</strong><br>

                                                                {{ $due->matchedVariation->programType?->type_name ?? 'All' }}
                                                            </div>

                                                        </div>
                                                    @endif

                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            @if ($data['member']->successfulPaymentForDue($due->fee_id))
                                                                <div class="d-flex justify-content-end flex-wrap mt-3">
                                                                    @php
                                                                        $successfulPayment = $data[
                                                                            'member'
                                                                        ]->successfulPaymentForDue($due->fee_id);
                                                                    @endphp
                                                                    <a
                                                                    href="{{ route('downloadPaymentReceipt', ['payment_transaction_id' => $successfulPayment->payment_transaction_id]) }}"
                                                                        class="btn btn-warning m-1 d-flex align-items-center">
                                                                        <i class="fas fa-download me-2"></i>
                                                                        <span>Download Receipt</span>
                                                                    </a>
                                                                </div>
                                                            @elseif($data['member']->pendingPaymentForDue($due->fee_id))
                                                                <div class="d-flex justify-content-end flex-wrap mt-3">
                                                                    @php
                                                                        $pendingPaymentTransaction = $data[
                                                                            'member'
                                                                        ]->pendingPaymentForDue($due->fee_id);
                                                                    @endphp
                                                                    <a href="{{ route('viewPaymentTransaction', ['payment_transaction_id' => $pendingPaymentTransaction->payment_transaction_id]) }}"
                                                                        class="btn btn-warning m-1 d-flex align-items-center">
                                                                        <i class="fas fa-eye me-2"></i>
                                                                        <span>View Pending Payment Transaction</span>
                                                                    </a>
                                                                </div>
                                                            @elseif ($data['member']->approvedDueWaiver($due->fee_id))
                                                                <div class="d-flex justify-content-end flex-wrap mt-3">
                                                                    @php
                                                                        $approvedWaiver = $data[
                                                                            'member'
                                                                        ]->approvedDueWaiver($due->fee_id);
                                                                    @endphp
                                                                    <a href="{{ route('downloadDuePaymentWaiverNotice', ['uuid' => $approvedWaiver->uuid]) }}"
                                                                        class="btn btn-warning m-1 d-flex align-items-center">
                                                                        <i class="fas fa-download me-2"></i>
                                                                        <span>Waiver Notice</span>
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="d-flex flex-wrap align-items-center justify-content-end gap-2">

                                                                    @if (Auth::user()->hasPermission('transactions.create'))
                                                                        <button type="button" class="btn btn-warning me-3"
                                                                            data-toggle="modal"
                                                                            data-target="#grantPaymentWaiverModal_{{ $due->fee_id }}">
                                                                            <i class="fas fa-hand-holding-dollar me-1"></i>
                                                                            Grant Waiver
                                                                        </button>
                                                                        @include('users.admin.userManagement.members.profile.include.grantPaymentWaiverModal')



                                                                        <span class="fw-semibold text-muted">
                                                                            Already Paid?
                                                                        </span>

                                                                        <button type="button" class="btn btn-primary"
                                                                            data-toggle="modal"
                                                                            data-target="#paymentRegularizationModal_{{ $due->fee_id }}">
                                                                            <i class="fas fa-receipt me-1"></i>
                                                                            Regularize Payment
                                                                        </button>

                                                                        @include('users.admin.userManagement.members.profile.include.paymentRegularizationModal')
                                                                    @endif

                                                                </div>
                                                            @endif

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>

                                    @empty

                                        <div class="col-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body text-center py-5">
                                                    <p class="text-muted mb-0">
                                                        No dues available for the current academic year.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse

                                    @if ($data['member']->memberAcademicInfo->pastProgramsAssociationDues()->isNotEmpty())
                                        <hr class="my-5">

                                        <h4 class="mb-4">
                                            Association Dues from Previous Programmes
                                        </h4>
                                        @include('users.admin.userManagement.members.profile.include.associationDuesFromPastPrograms')
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('viewMember', ['uuid' => $data['member']->uuid]) }}">
                            <i class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>

                </div>

            </div>
        </div>

    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
