@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.finance.transactions.include.inner_nav')
            </div>

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-8">
                            <div class="table_title">
                                {{ $data['pageTitle'] }}
                            </div>
                        </div>

                        <div class="table_head_item col-md-4">
                            <div class="custom_search">
                                <form type="GET" action="{{ route('paymentTransactionsSearch') }}">
                                    @csrf
                                    <div class="input-group-append">
                                        <input type="search" class="table_search my-2 my-sm-0" name="searchKey"
                                            value="{{ $data['searchKey'] }}"><button type="submit"
                                            class="table_search_button btn btn-primary
                                                "><i
                                                class="fas fa-search"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 m-auto">
                        <div class="my_form_container">
                            <div class=" pb-4">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered p-3">
                                            <thead class="justify-content-center text-center align-middle">
                                                <tr>
                                                    <th scope="col" style="width: 10px" class="table_header">S/N
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Transaction ID
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Purpose
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Amount
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Paid By
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Date
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Method
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['transactions']->isEmpty())
                                                <tr>
                                                    <td colspan="9" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['transactions'] as $transaction)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start text-uppercase">
                                                                {{ $transaction->payment_transaction_id }}
                                                            </td>
                                                            <td class="text-start">
                                                                @switch($transaction->payment_purpose)
                                                                    @case('association_due')
                                                                        @if ($transaction->due)
                                                                            {{-- @php
                                                            $academic =
                                                                $transaction->registeredPayer
                                                                    ->memberAcademicInfo;
                                                            $level = studentLevelAtDue(
                                                                $academic,
                                                                $transaction->due,
                                                            );
                                                        @endphp --}}
                                                                            @php
                                                                                $academic =
                                                                                    $transaction->registeredPayer
                                                                                        ?->memberAcademicInfo;

                                                                                $programLevel = studentLevelAtDue(
                                                                                    $academic,
                                                                                    $transaction->due,
                                                                                );

                                                                                $program = $programLevel['program'];
                                                                                $level = $programLevel['level'];
                                                                            @endphp

                                                                            {{ $transaction->due->title }}
                                                                            ({{ $transaction->due->academic_year }}/{{ $transaction->due->academic_year + 1 }}
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
                                                                        @if ($transaction->due)
                                                                            <a href="{{ route('viewDue', ['uuid' => $transaction->due->uuid]) }}"
                                                                                target="_blank"
                                                                                class="badge bg-secondary text-white"
                                                                                title="View Due">
                                                                                <i class="fas fa-link"></i>
                                                                            </a>
                                                                        @endif
                                                                    @break

                                                                    @case('donation')
                                                                        Donation @if ($transaction->donation)
                                                                            ({{ $transaction->donation->title }})
                                                                            <a href="{{ route('viewDonation', ['uuid' => $transaction->donation->uuid]) }}"
                                                                                target="_blank"
                                                                                class="badge bg-secondary text-white"
                                                                                title="View Donation">
                                                                                <i class="fas fa-link"></i>
                                                                            </a>
                                                                        @endif
                                                                    @break

                                                                    @case('invoice')
                                                                        Invoice
                                                                        @if ($transaction->invoice)
                                                                            ({{ \Illuminate\Support\Str::words($transaction->invoice->description, 5, '...') }})
                                                                            <a href="{{ route('viewInvoice', ['uuid' => $transaction->invoice->uuid]) }}"
                                                                                target="_blank"
                                                                                class="badge bg-secondary text-white"
                                                                                title="View Invoice">
                                                                                <i class="fas fa-link"></i>
                                                                            </a>
                                                                        @endif
                                                                    @break

                                                                    @case('election_form')
                                                                        Election Form @if ($transaction->election)
                                                                            (Candidacy Fee:
                                                                            {{ $transaction?->electionCandidateApplication?->position?->position ?? 'Position' }}
                                                                            — {{ $transaction->election->title }}
                                                                            ({{ $transaction->election->academic_session }}/{{ $transaction->election->academic_session + 1 }}))
                                                                            <a href="{{ route('viewElectionCandidateApplication', ['uuid' => $transaction->electionCandidateApplication->uuid]) }}"
                                                                                target="_blank"
                                                                                class="badge bg-secondary text-white"
                                                                                title="View Donation">
                                                                                <i class="fas fa-link"></i>
                                                                            </a>
                                                                        @endif
                                                                    @break

                                                                    @case('voting')
                                                                        Voting
                                                                        @if ($transaction->monetaryVote)
                                                                            -
                                                                            {{ $transaction->monetaryVote->no_of_votes }} votes for
                                                                            {{ $transaction->monetaryVote->thePosition }} —
                                                                            {{ $transaction->election->title }}
                                                                            ({{ $transaction->election->academic_session }}/{{ $transaction->election->academic_session + 1 }})
                                                                        @endif
                                                                    @break

                                                                    @default
                                                                    {{ $transaction->payment_purpose }}
                                                                @endswitch
                                                            </td>
                                                            <td class="text-start">
                                                                ₦{{ number_format($transaction->amount_payable, 2) }}
                                                            </td>
                                                            <td class="text-start">
                                                                @if ($transaction->registeredPayer)
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        {{-- Avatar --}}
                                                                        <div class="flex-shrink-0">
                                                                            @php
                                                                                $user = $transaction->registeredPayer;
                                                                            @endphp
                                                                            @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                        </div>

                                                                        {{-- Name & Code --}}
                                                                        <div class="flex-grow-1 min-width-0 text-center">
                                                                            <p
                                                                                class="fw-bold mb-0 text-truncate text-capitalize">
                                                                                @php
                                                                                    $member =
                                                                                        $transaction->registeredPayer
                                                                                            ->memberAcademicInfo;
                                                                                    $middleInitial = $member
                                                                                        ->userProfile
                                                                                        ->memberPersonalProfile
                                                                                        ->middle_name
                                                                                        ? strtoupper(
                                                                                                substr(
                                                                                                    $member->userProfile
                                                                                                        ->memberPersonalProfile
                                                                                                        ->middle_name,
                                                                                                    0,
                                                                                                    1,
                                                                                                ),
                                                                                            ) . '. '
                                                                                        : '';
                                                                                @endphp

                                                                                {{ strtolower($member->userProfile->memberPersonalProfile->first_name) . ' ' . $middleInitial . strtolower($member->userProfile->memberPersonalProfile->last_name) }}
                                                                            </p>
                                                                            <p class="text-muted mb-0 small text-truncate">
                                                                                {{ strtoupper($member->matric_number) }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    {{ $transaction->non_registered_payer }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($transaction->paid_at)->format('l, d F, Y h:i A') }}
                                                            </td>
                                                            <td>
                                                                @if ($transaction->payment_mode == 'manual')
                                                                    @php
                                                                        $methods = [
                                                                            'cash' => 'Cash',
                                                                            'bank_transfer' => 'Bank Transfer/Deposit',
                                                                            'cheque' => 'Cheque',
                                                                        ];
                                                                    @endphp

                                                                    {{ $methods[$transaction->manual_payment_method] ?? ucfirst(str_replace('_', ' ', $transaction->manual_payment_method)) }}
                                                                @endif
                                                                @if ($transaction->payment_mode == 'electronic')
                                                                    <span class="text-capitalize">
                                                                        {{ $transaction->electronic_payment_method }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @switch($transaction->payment_status)
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
                                                                            class="badge bg-yellow text-black">{{ $transaction->payment_status }}</span>
                                                                @endswitch

                                                                @switch($transaction->approval_status)
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

                                                                @if ($transaction->payment_status !== 'successful')
                                                                    @switch($transaction->electronic_payment_method)
                                                                        @case('flutterwave')
                                                                            <form
                                                                                action="{{ route('flutterwavePaymentCallback') }}"
                                                                                method="GET" class="d-inline">
                                                                                @csrf
                                                                                <input type="hidden" name="transaction_id"
                                                                                    value="{{ $transaction->service_provider_id }}">
                                                                                <input type="hidden" name="tx_ref"
                                                                                    value="{{ $transaction->payment_transaction_id }}">
                                                                                <button type="submit"
                                                                                    class="border-0 bg-transparent p-0"><i
                                                                                        class="fa-solid fa-rotate-right text-primary"></i></button>
                                                                            </form>
                                                                        @break

                                                                        @case('paystack')
                                                                            <form
                                                                                action="{{ route('paystackPaymentCallback', ['transaction_id' => $transaction->payment_transaction_id]) }}"
                                                                                method="GET" class="d-inline">
                                                                                @csrf
                                                                                <input type="hidden" name="reference"
                                                                                    value="{{ $transaction->paystack_reference }}">
                                                                                <button type="submit"
                                                                                    class="border-0 bg-transparent p-0"><i
                                                                                        class="fa-solid fa-rotate-right text-primary"></i></button>
                                                                            </form>
                                                                        @break

                                                                        @default
                                                                    @endswitch
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewPaymentTransaction', ['payment_transaction_id' => $transaction->payment_transaction_id]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['transactions'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            @switch($data['requestType'])
                                                @case('all')
                                                    {{ '' }}
                                                @break

                                                @case('search')
                                                    <a class="btn btn-success" href="{{ route('paymentTransactions') }}"> <i
                                                            class="fas fa-chevron-left"></i>
                                                        Back
                                                    </a>
                                                @break

                                                @default
                                            @endswitch

                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>





        </div>
    </div><!--//app-content-->
@endsection
