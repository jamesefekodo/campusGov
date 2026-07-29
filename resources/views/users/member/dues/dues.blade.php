@extends('layouts.userLayout')
@section('title', 'Dues')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <div class="mb-4">
                <div class="individual-review-card-container">
                    <div class="col-md-12 individual-review-card">

                        <div class="my_form_container row col-md-12 pb-5">
                            <h4 class="block text-center py-2 pt-3 text-uppercase type-b-page-header">
                                Dues
                            </h4>
                            <div class="row">

                                @forelse($data['member']->memberAcademicInfo->associationDues() as $due)
                                    <div class="col-md-12 mb-4">
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

                                                    {{-- <div class="col-md-4 mb-3">
                                                                <small class="text-muted d-block">Default Amount</small>
                                                                ₦{{ number_format($due->amount, 2) }}
                                                            </div> --}}

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



                                                @if ($transaction = $data['member']->successfulPaymentForDue($due->fee_id))
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <a href="{{ route('printPaymentReceipt', $transaction->uuid) }}"
                                                            class="btn btn-success">
                                                            <i class="fas fa-print me-1"></i>
                                                            Print Receipt
                                                        </a>
                                                    </div>
                                                @elseif ($data['member']->pendingPaymentForDue($due->fee_id))
                                                    <div class="d-flex justify-content-end align-items-center">

                                                        <button type="button" class="btn btn-warning" disabled>
                                                            <i class="fas fa-clock me-1"></i>
                                                            Pending Approval
                                                        </button>
                                                    </div>
                                                @else
                                                    {{-- Pay Now --}}
                                                    @php
                                                        $collapseId = 'payNowSection-' . $due->id;
                                                    @endphp

                                                    @include('users.member.dues.include.payDueNowModal')
                                                @endif

                                            </div>

                                        </div>
                                    </div>

                                @empty

                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center py-5">
                                                <p class="text-muted mb-0">
                                                    There are no dues applicable to your membership period. </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse

                            </div>
                        </div>
                        {{-- Back Home --}}
                        <div class="w-100 justify-content-center text-center my-4">
                            <a class="btn btn-primary" href="{{ route('memberViewSelfProfile') }}"> <i
                                    class="fas fa-chevron-left"></i>
                                Back
                            </a>

                        </div>


                    </div>
                </div>


            </div>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
