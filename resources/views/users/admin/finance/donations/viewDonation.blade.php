@extends('layouts.userLayout')
@section('title', $data['donation']->title)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        {{ $data['donation']->title }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="col-md-12 row">
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Title:</span>
                                                <span class="text-black">
                                                    {{ $data['donation']->title }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Description:</span>
                                                <span class="text-black">
                                                    {{ $data['donation']->description }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Target Amount:</span>
                                                <span class="text-black">
                                                    @if (empty($data['donation']->target_amount) || $data['donation']->target_amount == 0)
                                                        N/A
                                                    @else
                                                        ₦{{ number_format($data['donation']->target_amount, 2) }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Deadline:</span>
                                                <span class="text-black">
                                                    @if ($data['donation']->deadline)
                                                        {{ \Carbon\Carbon::parse($data['donation']->deadline)->format('l, d F, Y h:i A') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Amount Raised:</span>
                                                <span class="text-black">
                                                    ₦{{ number_format($data['donation']->getAmountRaisedAttribute(), 2) }}
                                                    (Gross:
                                                    ₦{{ number_format($data['donation']->getGrossAmountRaisedAttribute(), 2) }})
                                                </span>
                                            </div>

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Created By:</span>
                                                <span class="text-black">
                                                    @if ($data['donation']->createdBy?->memberPersonalProfile)
                                                        {{ $data['donation']->createdBy->memberPersonalProfile->first_name }}
                                                        {{ $data['donation']->createdBy->memberPersonalProfile->last_name }}
                                                    @else
                                                        {{ $data['donation']->created_by }}
                                                    @endif
                                                    <a href="{{ route('viewMember', ['uuid' => $data['donation']->createdBy->uuid]) }}"
                                                        target="_blank" class="badge bg-secondary text-white"
                                                        title="View Approver">
                                                        <i class="fas fa-link"></i>
                                                    </a> |
                                                    @if ($data['donation']->created_at)
                                                        {{ \Carbon\Carbon::parse($data['donation']->created_at)->format('l, d F, Y h:i A') }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Status:</span>
                                                <span class="text-black">
                                                    @switch($data['donation']->is_active)
                                                        @case(true)
                                                            <span class="badge bg-success-subtle text-success">Active</span> |
                                                            Accepting Funds
                                                        @break

                                                        @default
                                                            <span class="badge bg-danger-subtle text-danger">Inactive</span> | Not
                                                            Accepting Funds
                                                    @endswitch
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Approval:</span>
                                                <span class="text-black">
                                                    @switch($data['donation']->approval_status)
                                                        @case('pending')
                                                            <span class="badge bg-warning-subtle text-warning">Pending</span>
                                                        @break

                                                        @case('approved')
                                                            <span class="badge bg-success-subtle text-success">Approved</span>
                                                        @break

                                                        @case('rejected')
                                                            <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                                        @break

                                                        @default
                                                            <span
                                                                class="badge bg-danger-subtle text-danger">{{ $data['donation']->approval_status }}</span>
                                                    @endswitch
                                                </span>
                                            </div>

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Processed By:</span>
                                                <span class="text-black">
                                                    @if ($data['donation']->processedBy?->memberPersonalProfile)
                                                        @php
                                                            $profile =
                                                                $data['donation']->processedBy?->memberPersonalProfile;
                                                        @endphp

                                                        {{ $profile ? trim($profile->first_name . ' ' . $profile->last_name) : $data['donation']->processed_by }}

                                                        <a href="{{ route('viewMember', ['uuid' => $data['donation']->processedBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Processor">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @else
                                                        -
                                                    @endif |
                                                    @if ($data['donation']->approved_at)
                                                        {{ \Carbon\Carbon::parse($data['donation']->approved_at)->format('l, d F, Y h:i A') }}
                                                    @endif
                                                </span>
                                            </div>
                                            {{-- Target amount + amount left --}}
                                            @php
                                                $hasTarget =
                                                    filled($data['donation']->target_amount) &&
                                                    $data['donation']->target_amount > 0;
                                                $amountRaised = $data['donation']->getAmountRaisedAttribute();
                                                $amountLeft = $hasTarget
                                                    ? max(0, $data['donation']->target_amount - $amountRaised)
                                                    : null;
                                                $targetReached =
                                                    $hasTarget && $amountRaised >= $data['donation']->target_amount;
                                            @endphp

                                            @if ($hasTarget)
                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Target Amount:</span>
                                                    <span class="text-black">
                                                        ₦{{ number_format($data['donation']->target_amount, 2) }}
                                                    </span>
                                                </div>

                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Amount Raised:</span>
                                                    <span class="text-black">
                                                        ₦{{ number_format($amountRaised, 2) }}
                                                    </span>
                                                </div>

                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Amount Left:</span>
                                                    <span class="text-black">
                                                        @if ($targetReached)
                                                            <span class="badge bg-success text-white">Target reached!</span>
                                                        @else
                                                            ₦{{ number_format($amountLeft, 2) }}
                                                        @endif
                                                    </span>
                                                </div>

                                                {{-- Progress bar --}}
                                                <div class="col-md-12 p-1">
                                                    @php
                                                        $percent = min(
                                                            100,
                                                            round(
                                                                ($amountRaised / $data['donation']->target_amount) *
                                                                    100,
                                                                1,
                                                            ),
                                                        );
                                                    @endphp
                                                    <div class="progress mt-1" style="height:10px;">
                                                        <div class="progress-bar {{ $targetReached ? 'bg-success' : 'bg-primary' }}"
                                                            role="progressbar" style="width: {{ $percent }}%;"
                                                            aria-valuenow="{{ $percent }}" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ $percent }}% of target raised</small>
                                                </div>
                                            @endif
                                        </div>

                                        <hr class="mt-4">
                                        <div class="d-flex justify-content-end flex-wrap m-3 mb-0">
                                            @if (Auth::user()->hasPermission('donation.update'))
                                                @if ($data['donation']->approval_status !== 'declined')
                                                    <button class="btn btn-warning m-1 d-flex align-items-center mb-5"
                                                        data-toggle="modal" data-target="#editDonationModal">
                                                        <i class="fas fa-edit me-2"></i>
                                                        <span>Update</span>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                        @include('users.admin.finance.donations.include.editDonationModal')

                                        @if ($data['donation']->approval_status == 'pending')
                                            <div class="alert alert-info m-3" role="alert">
                                                <h4 class="alert-heading">Pending Approval</h4>
                                                <p class="my-1">This donation is pending approval and cannot receive funds
                                                    unless approved. Click the button below to approve or decline it.</p>


                                                <button class="btn btn-block btn-primary mt-5 w-100 text-uppercase"
                                                    data-toggle="modal" data-target="#donationApprovalModal"> <i
                                                        class="fas fa-gavel me-2"></i>
                                                    Click here to approve/decline donation</button>

                                                @include('users.admin.finance.donations.include.donationApprovalModal')
                                            </div>
                                        @endif


                                        <div class="d-flex align-items-center">
                                            <hr class="flex-grow-1">
                                            <span class="mx-2 text-black">Payments
                                            </span>
                                            <hr class="flex-grow-1">
                                        </div>
                                        <div class="row g-3 mt-2 mb-4">
                                            <div class="col-md-6">
                                                <div
                                                    class="card shadow-sm border-0 bg-primary-subtle bg-opacity-10 h-100 m-2">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i class="fas fa-users fa-2x text-blue"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small">Amount Raised</div>
                                                                <div class="fw-bold text-blue"
                                                                    style="font-size: clamp(14px, 4vw, 2rem); word-break: break-word; line-height: 1.2;">
                                                                    ₦{{ number_format($data['donation']->getAmountRaisedAttribute(), 2) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 mt-auto w-100">
                                                            <!-- View Form Button -->
                                                            <a href="{{ route('viewDonationReport', ['uuid' => $data['donation']->uuid]) }}"
                                                                class="btn btn-sm btn-primary flex-fill text-nowrap d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-eye me-1"></i> View
                                                            </a>

                                                            <!-- Download Anchor Button -->
                                                            <a href="{{ route('downloadDonationReport', ['uuid' => $data['donation']->uuid]) }}"
                                                                class="btn btn-sm btn-outline-primary flex-fill text-nowrap d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div
                                                    class="card shadow-sm border-0 bg-success-subtle bg-opacity-10 h-100 m-2">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i class="fas fa-check-circle fa-2x text-success"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small">Successful Payments</div>
                                                                <div class="fs-4 fw-bold text-success">
                                                                    {{ $data['donation']->successfulPayments()->count() }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 mt-auto w-100">
                                                            <!-- View Form Button -->
                                                            <form method="GET"
                                                                action="{{ route('paymentTransactionsSearch') }}"
                                                                target="_blank" class="flex-fill">
                                                                @csrf
                                                                <div class="input-group w-100">
                                                                    <input type="hidden" name="searchKey"
                                                                        value="{{ $data['donation']->donation_id }}">
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-success w-100 text-nowrap">
                                                                        <i class="fas fa-eye me-1"></i> View
                                                                    </button>
                                                                </div>
                                                            </form>

                                                            <!-- Download Anchor Button -->
                                                            {{-- <a href="#"
                                                                class="btn btn-sm btn-outline-success flex-fill text-nowrap d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div
                                                    class="card shadow-sm border-0 bg-warning-subtle bg-opacity-10 h-100 m-2">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i
                                                                    class="fas fa-hand-holding-heart fa-2x text-warning"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small">Pending</div>
                                                                <div class="fs-4 fw-bold text-warning">
                                                                    {{ $data['donation']->paymentsPendingApproval()->count() }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 mt-auto">
                                                            <form type="GET"
                                                                action="{{ route('paymentTransactionsSearch') }}"
                                                                target="_blank" class="w-100">
                                                                @csrf
                                                                <div class="input-group-append">
                                                                    <input type="hidden" name="searchKey"
                                                                        value="{{ $data['donation']->donation_id }}">

                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-warning flex-fill w-100">
                                                                        <i class="fas fa-eye me-1"></i> View
                                                                    </button>
                                                                </div>
                                                            </form>
                                                            {{-- <a href="{{ route('due.paidOrWaived.download', ['uuid' => $data['due']->uuid]) }}"
                                                                class="btn btn-sm btn-outline-warning flex-fill">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card shadow-sm border-0 bg-danger bg-opacity-10 h-100 m-2">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i
                                                                    class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small">Failed Payments</div>
                                                                <div class="fs-4 fw-bold text-danger">
                                                                    {{ $data['donation']->failedPayments()->count() }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 mt-auto">
                                                            <form type="GET"
                                                                action="{{ route('paymentTransactionsSearch') }}"
                                                                target="_blank" class="w-100">
                                                                @csrf
                                                                <div class="input-group-append">
                                                                    <input type="hidden" name="searchKey"
                                                                        value="{{ $data['donation']->donation_id }}">

                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-danger flex-fill w-100">
                                                                        <i class="fas fa-eye me-1"></i> View
                                                                    </button>
                                                                </div>
                                                            </form>
                                                            {{-- <a href="{{ route('due.outstanding.download', ['uuid' => $data['due']->uuid]) }}"
                                                                class="btn btn-sm btn-danger flex-fill w-100">
                                                                <i class="fas fa-print me-1"></i> Print
                                                            </a> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4">
                        <a class="btn btn-primary" href="{{ route('donations') }}"> <i class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>
                </div>
            </div>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
