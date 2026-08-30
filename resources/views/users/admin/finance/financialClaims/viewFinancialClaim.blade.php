@extends('layouts.userLayout')
@section('title', 'Financial Claim - ' . $data['claim']->claim_id)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Financial Claim - <span
                                        class="text-uppercase">{{ strtolower($data['claim']->claim_id) }}</span>
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Claim ID:</span>
                                        <span class="text-black text-uppercase">
                                            {{ $data['claim']->claim_id }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Amount:</span>
                                        <span class="text-black">
                                            ₦{{ number_format($data['claim']->amount, 2) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Type:</span>
                                        <span class="text-black">
                                            @switch($data['claim']->claim_type)
                                                @case('credit')
                                                    <span class="badge bg-success-subtle text-success">Credit</span>
                                                @break

                                                @case('debit')
                                                    <span class="badge bg-danger-subtle text-danger">Debit</span>
                                                @break

                                                @default
                                                    <span
                                                        class="badge bg-warning-subtle text-warning">{{ $data['claim']->claim_type }}</span>
                                            @endswitch </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Date :</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['claim']->date)->format('l, d F, Y') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Submitted by:</span>
                                        <span class="text-black text-capitalize">
                                            @if ($data['claim']->submittedBy?->memberPersonalProfile)
                                                {{ $data['claim']->submittedBy->memberPersonalProfile->first_name }}
                                                {{ $data['claim']->submittedBy->memberPersonalProfile->last_name }}
                                            @else
                                                {{ $data['claim']->initiated_by }}
                                            @endif
                                            <a href="{{ route('viewMember', ['uuid' => $data['claim']->submittedBy->uuid]) }}"
                                                target="_blank" class="badge bg-secondary text-white" title="View Approver">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Date Submitted:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['claim']->created_at)->format('l, d F, Y h:i A') }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Approval Status:</span>
                                        <span class="text-black">
                                            @switch($data['claim']->approval_status)
                                                @case('approved')
                                                    <span class="badge bg-success-subtle text-success">Approved</span>
                                                @break

                                                @case('declined')
                                                    <span class="badge bg-danger-subtle text-danger">Declined</span>
                                                @break

                                                @case('pending')
                                                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                                                @break

                                                @default
                                                    <span
                                                        class="badge bg-warning-subtle text-warning">{{ $data['claim']->approval_status }}</span>
                                            @endswitch </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Processed By:</span>
                                        <span class="text-black">
                                            @if ($data['claim']->approved_by)

                                                @if ($data['claim']->approvedBy?->memberPersonalProfile)
                                                    {{ trim(
                                                        $data['claim']->approvedBy->memberPersonalProfile->first_name .
                                                            ' ' .
                                                            ($data['claim']->approvedBy->memberPersonalProfile->middle_name
                                                                ? $data['claim']->approvedBy->memberPersonalProfile->middle_name . ' '
                                                                : '') .
                                                            $data['claim']->approvedBy->memberPersonalProfile->last_name,
                                                    ) }}
                                                @else
                                                    {{ $data['claim']->approved_by }}
                                                @endif

                                                @if ($data['claim']->approvedBy)
                                                    <a href="{{ route('viewMember', ['uuid' => $data['claim']->approvedBy->uuid]) }}"
                                                        target="_blank" class="badge bg-secondary text-white"
                                                        title="View Approver">
                                                        <i class="fas fa-link"></i>
                                                    </a>
                                                @endif
                                                -
                                                {{ \Carbon\Carbon::parse($data['claim']->approved_at)->format('l, d F, Y h:i A') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Remarks:</span>
                                        <span class="text-black">
                                            {{ $data['claim']->remarks }}
                                        </span>
                                    </div>

                                    @if (Auth::user()->hasPermission('financial_claim.update') && $data['claim']->approval_status == 'pending')
                                        <div class="alert alert-warning text-center shadow-sm py-4 px-4" role="alert">
                                            <div class="mb-2">
                                                <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                            </div>

                                            <h5 class="fw-bold mb-2">Financial Claim Awaiting Review</h5>

                                            <p class="mb-3">
                                                This financial claim is waiting for your review and approval.
                                                Please review the claim details and take the appropriate action.
                                            </p>

                                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#processFinancialClaimsModal">
                                                <i class="fas fa-tasks me-1"></i>
                                                Review &amp; Approve/Decline Claim
                                            </button>


                                            @include('users.admin.finance.financialClaims.include.processFinancialClaimsModal')


                                        </div>
                                    @endif


                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        @php
                            $backRoute = route('financialClaims');
                            if ($data['claim']->approval_status == 'pending') {
                                $backRoute = route('financialClaimsPendingApproval');
                            }

                        @endphp
                        <a class="btn btn-primary" href="{{ $backRoute }}">
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
