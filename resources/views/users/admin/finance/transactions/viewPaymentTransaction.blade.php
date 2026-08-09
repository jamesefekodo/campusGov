@extends('layouts.userLayout')
@section('title', 'Payment Transaction - ' . $data['transaction']->payment_transaction_id)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @if ($data['transaction']->registeredPayer)
                                @php
                                    $data['member'] = $data['transaction']->registeredPayer;
                                @endphp
                                @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')
                            @else
                                @include('users.admin.finance.transactions.include.nonRegisteredPayerSidebar')
                            @endif

                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Payment Transaction - <span
                                        class="text-uppercase">{{ strtolower($data['transaction']->payment_transaction_id) }}</span>
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Transaction ID:</span>
                                        <span class="text-black text-uppercase">
                                            {{ $data['transaction']->payment_transaction_id }}
                                        </span>
                                    </div>
                                    @php
                                        $paidBy = $data['transaction']->non_registered_payer;
                                        $phone = $data['transaction']->phone;
                                        $email = $data['transaction']->email;

                                        if (
                                            $data['transaction']?->registeredPayer &&
                                            $data['transaction']?->registeredPayer->memberPersonalProfile
                                        ) {
                                            $profile = $data['transaction']->registeredPayer->memberPersonalProfile;

                                            $paidBy = trim(
                                                $profile->first_name .
                                                    ' ' .
                                                    ($profile->middle_name ? $profile->middle_name . ' ' : '') .
                                                    $profile->last_name,
                                            );

                                            $phone = $profile->phone1;
                                            $email = $profile->email1;
                                        }
                                    @endphp
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Paid By:</span>
                                        <span class="text-black">
                                            {{ $paidBy }} @if ($data['transaction']->registeredPayer)
                                                <a href="{{ route('viewMember', ['uuid' => $data['transaction']->registeredPayer->uuid]) }}"
                                                    target="_blank" class="bg-secondary text-white badge"
                                                    title="View Payer">
                                                    <i class="fas fa-link"></i>
                                                </a>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Phone:</span>
                                        <span class="text-black">
                                            {{ $phone }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Email:</span>
                                        <span class="text-black">
                                            {{ $email }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-2">
                                        <span style="font-weight: bold">Payment Purpose:</span>
                                        <span class="text-black">
                                            @switch($data['transaction']->payment_purpose)
                                                @case('association_due')
                                                    @if ($data['transaction']->due)
                                                        @php
                                                            $academic =
                                                                $data['transaction']->registeredPayer
                                                                    ->memberAcademicInfo;
                                                            $level = studentLevelAtDue(
                                                                $academic,
                                                                $data['transaction']->due,
                                                            );
                                                        @endphp

                                                        {{ $data['transaction']->due->title }}
                                                        ({{ $data['transaction']->due->academic_year }}/{{ $data['transaction']->due->academic_year + 1 }}
                                                        Academic Session)
                                                        -
                                                        {{ currentLevel($academic->theProgram, $level) }}
                                                    @endif
                                                @break

                                                @case('donation')
                                                    Donation
                                                @break

                                                @case('invoice')
                                                    Invoice
                                                @break

                                                @case('election_form')
                                                    Election Form
                                                @break

                                                @case('voting')
                                                    Voting
                                                @break

                                                @default
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Amount Payable:</span>
                                        <span class="text-black fs-5 fw-bold">
                                            ₦{{ number_format($data['transaction']->amount_payable, 2) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Amount Paid:</span>
                                        <span class="text-blue fs-5 fw-bold">
                                            ₦{{ number_format($data['transaction']->amount_paid, 2) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Amount Settled:</span>
                                        <span class="text-success fs-5 fw-bold">
                                            ₦{{ number_format($data['transaction']->amount_settled, 2) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Payment Mode:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                {{ $data['transaction']->payment_mode }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Payment Method:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['transaction']->payment_mode == 'manual')
                                                    @php
                                                        $methods = [
                                                            'cash' => 'Cash',
                                                            'bank_transfer' => 'Bank Transfer/Deposit',
                                                            'cheque' => 'Cheque',
                                                        ];
                                                    @endphp

                                                    {{ $methods[$data['transaction']->manual_payment_method] ?? ucfirst(str_replace('_', ' ', $data['transaction']->manual_payment_method)) }}
                                                @endif
                                                @if ($data['transaction']->payment_mode == 'electronic')
                                                    <span class="text-capitalize">
                                                        {{ $data['transaction']->electronic_payment_method }}
                                                    </span>
                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Taxes Paid:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                {{ $data['transaction']->taxes_paid }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Total Tax:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                ₦{{ number_format($data['transaction']->total_tax, 2) }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Service Provider Key/Id/Ref:</span>
                                        <span class="text-black">
                                            @if ($data['transaction']->service_provider_id)
                                                {{ $data['transaction']->service_provider_id }}
                                            @endif
                                            @if ($data['transaction']->paystack_reference)
                                                {{ $data['transaction']->paystack_reference }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Status:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @switch($data['transaction']->payment_status)
                                                    @case('successful')
                                                        <span class="badge bg-green text-white">Successful</span>
                                                    @break

                                                    @case('failed')
                                                        <span class="badge bg-red text-white">Failed</span>
                                                    @break

                                                    @case('pending')
                                                        <span class="badge bg-yellow text-black">Pending</span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="badge bg-yellow text-black">{{ $data['transaction']->payment_status }}</span>
                                                @endswitch

                                                @switch($data['transaction']->approval_status)
                                                    @case('approved')
                                                        <span class="badge bg-green text-white">Approved</span>
                                                    @break

                                                    @case('declined')
                                                        <span class="badge bg-red text-white">Declined</span>
                                                    @break

                                                    @case('pending')
                                                        <span class="badge bg-yellow text-black">Pending</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-yellow text-black">Unknown</span>
                                                @endswitch

                                                @if ($data['transaction']->payment_status !== 'successful')
                                                    @switch($data['transaction']->electronic_payment_method)
                                                        @case('flutterwave')
                                                            <form action="{{ route('flutterwavePaymentCallback') }}"
                                                                method="GET" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="transaction_id"
                                                                    value="{{ $data['transaction']->service_provider_id }}">
                                                                <input type="hidden" name="tx_ref"
                                                                    value="{{ $data['transaction']->payment_transaction_id }}">
                                                                <button type="submit" class="border-0 bg-transparent p-0 fs-3"><i
                                                                        class="fa-solid fa-rotate-right btn btn-danger"></i></button>
                                                            </form>
                                                        @break

                                                        @case('paystack')
                                                            <form
                                                                action="{{ route('paystackPaymentCallback', ['transaction_id' => $data['transaction']->payment_transaction_id]) }}"
                                                                method="GET" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="reference"
                                                                    value="{{ $data['transaction']->paystack_reference }}">
                                                                <button type="submit" class="border-0 bg-transparent p-0 fs-3"><i
                                                                        class="fa-solid fa-rotate-right btn btn-danger"></i></button>
                                                            </form>

                                                            {{-- Update paystack reference number start --}}

                                                            <a class="badge bg-blue text-white p-1" data-toggle="modal"
                                                                data-target="#updatePastackReferenceModal"
                                                                style="cursor: pointer;">Update Reference
                                                                Number</a>

                                                            {{-- Update paystack reference number end --}}
                                                            @include('users.admin.finance.transactions.include.updatePastackReferenceModal')
                                                        @break

                                                        @default
                                                    @endswitch
                                                @endif
                                                {{-- Process Manual Payment Start --}}
                                                @if ($data['transaction']->manual_payment_method)

                                                    @if ($data['transaction']->payment_status == 'successful')

                                                        @php
                                                            $approvedAt = $data['transaction']->approved_at;

                                                            $canReverse =
                                                                $approvedAt &&
                                                                \Carbon\Carbon::parse($approvedAt)->gt(
                                                                    now()->subHours(48),
                                                                );

                                                            $remaining = $canReverse
                                                                ? now()->diff(
                                                                    \Carbon\Carbon::parse($approvedAt)->addHours(48),
                                                                )
                                                                : null;
                                                        @endphp

                                                        @if ($canReverse)
                                                            <div class="d-flex align-items-center gap-2">

                                                                <button type="button" class="btn btn-danger m-1"
                                                                    data-toggle="modal"
                                                                    data-target="#processManualPaymentModal">
                                                                    <i class="fas fa-undo-alt me-1"></i>
                                                                    Change Status
                                                                </button>

                                                                <span class="badge bg-warning text-dark">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    {{ $remaining->format('%h hrs %I mins') }} left
                                                                </span>

                                                            </div>
                                                        @endif
                                                    @else
                                                        <button type="button" class="btn btn-success m-1"
                                                            data-toggle="modal" data-target="#processManualPaymentModal">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            Process Payment
                                                        </button>

                                                    @endif

                                                    @if (Auth::user()->hasPermission('transactions.update'))
                                                        @include('users.admin.finance.transactions.include.processManualPaymentModal')
                                                    @endif
                                                @endif
                                                {{-- Process Manual Payment End --}}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Paid At:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                {{ \Carbon\Carbon::parse($data['transaction']->paid_at)->format('l, d F, Y h:i A') }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Processed At:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['transaction']->approved_at)
                                                    {{ \Carbon\Carbon::parse($data['transaction']->approved_at)->format('l, d F, Y h:i A') }}
                                                @endif
                                                -
                                                @if ($data['transaction']->approved_by)

                                                    @if ($data['transaction']->approvedBy?->memberPersonalProfile)
                                                        {{ trim(
                                                            $data['transaction']->approvedBy->memberPersonalProfile->first_name .
                                                                ' ' .
                                                                ($data['transaction']->approvedBy->memberPersonalProfile->middle_name
                                                                    ? $data['transaction']->approvedBy->memberPersonalProfile->middle_name . ' '
                                                                    : '') .
                                                                $data['transaction']->approvedBy->memberPersonalProfile->last_name,
                                                        ) }}
                                                    @else
                                                        {{ $data['transaction']->approved_by }}
                                                    @endif

                                                    @if ($data['transaction']->approvedBy)
                                                        <a href="{{ route('viewMember', ['uuid' => $data['transaction']->approvedBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Approver">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif

                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    @if (
                                        $data['transaction']->photo &&
                                            file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/paymentTransactions/proof/' . $data['transaction']->photo)))
                                        @php
                                            $proofModalId = 'proofImageModal_' . $data['transaction']->id;
                                        @endphp

                                        <div class="col-md-12 p-1">
                                            <span style="font-weight: bold">Proof:</span>

                                            <span class="text-black">
                                                <span class="text-capitalize">

                                                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/paymentTransactions/proof/' . $data['transaction']->photo) }}"
                                                        alt="Proof of Payment" class="img-thumbnail ms-2"
                                                        style="max-width:150px; cursor: zoom-in;" data-bs-toggle="modal"
                                                        data-bs-target="#{{ $proofModalId }}">

                                                </span>
                                            </span>
                                        </div>

                                        <!-- Zoom Modal -->
                                        <div class="modal fade" id="{{ $proofModalId }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content border-0 bg-transparent">

                                                    <div class="text-end mb-2">
                                                        <button type="button" class="btn btn-light rounded-circle"
                                                            data-bs-dismiss="modal" aria-label="Close">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>

                                                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/paymentTransactions/proof/' . $data['transaction']->photo) }}"
                                                        class="img-fluid rounded shadow" alt="Proof of Payment">

                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('paymentTransactions') }}">
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
