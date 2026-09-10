@extends('layouts.userLayout')
@section('title', 'Payment Transaction - ' . $data['transaction']->payment_transaction_id)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                       
                        <div class="col-md-12 individual-review-card">
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
                                    
                                    
                                    <div class="col-md-6 p-2">
                                        <span style="font-weight: bold">Payment Purpose:</span>
                                        <span class="text-black">
                                            @switch($data['transaction']->payment_purpose)
                                                @case('association_due')
                                                    @if ($data['transaction']->due)
                                                      
                                                        @php
                                                            $academic =
                                                                $data['transaction']->registeredPayer
                                                                    ?->memberAcademicInfo;

                                                            $programLevel = studentLevelAtDue(
                                                                $academic,
                                                                $data['transaction']->due,
                                                            );

                                                            $program = $programLevel['program'];
                                                            $level = $programLevel['level'];
                                                        @endphp

                                                        {{ $data['transaction']->due->title }}
                                                        ({{ $data['transaction']->due->academic_year }}/{{ $data['transaction']->due->academic_year + 1 }}
                                                        Academic Session)
                                                        -
                                                        {{-- {{ currentLevel($academic->theProgram, $level) }} --}} @if ($program)
                                                            {{ currentLevel($program, $level) }}
                                                        @else
                                                            <span class="text-danger">
                                                                Previous programme is missing
                                                            </span>
                                                        @endif
                                                    @endif

                                                    <a href="{{ route('myAssociationDues') }}"
                                                        target="_blank" class="badge bg-secondary text-white" title="View Due">
                                                        <i class="fas fa-link"></i>
                                                    </a>
                                                @break

                                                @case('donation')
                                                    Donation @if ($data['transaction']->donation)
                                                        ({{ $data['transaction']->donation->title }})
                                                        <a href="{{ route('memberViewDonation', ['uuid' => $data['transaction']->donation->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Donation">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif
                                                @break

                                                @case('invoice')
                                                    Invoice
                                                    @if ($data['transaction']->invoice)
                                                        ({{ \Illuminate\Support\Str::words($data['transaction']->invoice->description, 5, '...') }})
                                                        <a href="{{ route('viewMyInvoice', ['uuid' => $data['transaction']->invoice->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Invoice">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif
                                                @break

                                                @case('election_form')
                                                    Election Form @if ($data['transaction']->election)
                                                        (Candidacy Fee:
                                                        {{ $data['transaction']?->electionCandidateApplication?->position?->position ?? 'Position' }}
                                                        — {{ $data['transaction']->election->title }}
                                                        ({{ $data['transaction']->election->academic_session }}/{{ $data['transaction']->election->academic_session + 1 }}))
                                                        <a href="{{ route('memberViewElection', ['uuid' => $data['transaction']->election->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Donation">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif
                                                @break

                                                @case('voting')
                                                    Voting
                                                    @if ($data['transaction']->monetaryVote) -
                                                        {{ $data['transaction']->monetaryVote->no_of_votes }} votes for
                                                        {{ $data['transaction']->monetaryVote->thePosition }} —
                                                        {{ $data['transaction']->election->title }}
                                                        ({{ $data['transaction']->election->academic_session }}/{{ $data['transaction']->election->academic_session + 1 }})
                                                    @endif
                                                @break

                                                @default
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Amount:</span>
                                        <span class="text-black fw-bold">
                                            ₦{{ number_format($data['transaction']->amount_payable, 2) }}
                                        </span>
                                    </div>
                                   
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Payment Type:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                {{ $data['transaction']->payment_type }}
                                            </span>
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
                                                            <form action="{{ route('flutterwavePaymentCallback') }}" method="GET"
                                                                class="d-inline">
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

                                                        @break

                                                        @default
                                                    @endswitch
                                                @endif
                                               
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
                                    
                                    @if (
                                        $data['transaction']->photo &&
                                            file_exists(public_path(
                                                    'assets/tenant_' . tenant()->id . '/uploads/paymentTransactions/proof/' . $data['transaction']->photo)))
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

                                    <hr>
                                    <div class="d-flex justify-content-end flex-wrap mt-3">
                                        @if($data['transaction']->approval_status == 'approved')
                                        <a href="{{ route('memberDownloadPaymentReceipt', ['payment_transaction_id' => $data['transaction']->payment_transaction_id]) }}"
                                            class="btn btn-warning m-1 d-flex align-items-center">
                                            <i class="fas fa-download me-2"></i>
                                            <span>Download Receipt</span>
                                        </a>
                                        @else
                                         <a href="#"
                                            class="btn btn-warning m-1 d-flex align-items-center disabled">
                                            <i class="fas fa-download me-2"></i>
                                            <span>Download Receipt</span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('myPaymentTransactions') }}">
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
