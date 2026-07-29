@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.activeAccount.payments.include.inner_nav') --}}
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
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['transactions']->isEmpty())
                                                <tr>
                                                    <td colspan="8" class="text-center">
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
                                                                            @php
                                                                                $academic =
                                                                                    $transaction->registeredPayer
                                                                                        ->memberAcademicInfo;
                                                                                $level = studentLevelAtDue(
                                                                                    $academic,
                                                                                    $transaction->due,
                                                                                );
                                                                            @endphp

                                                                            {{ $transaction->due->title }}
                                                                            ({{ $transaction->due->academic_year }}/{{ $transaction->due->academic_year + 1 }}
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
                                                            </td>
                                                            <td class="text-start">
                                                                ₦{{ number_format($transaction->amount_paid, 2) }}
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
                                                                            {{-- <form
                                                                                action="{{ route('associationFlutterwavePaymentCallback') }}"
                                                                                method="GET" class="d-inline">
                                                                                @csrf
                                                                                <input type="hidden" name="transaction_id"
                                                                                    value="{{ $transaction->service_provider_id }}">
                                                                                <input type="hidden" name="tx_ref"
                                                                                    value="{{ $transaction->payment_transaction_id }}">
                                                                                <button type="submit"
                                                                                    class="border-0 bg-transparent p-0"><i
                                                                                        class="fa-solid fa-rotate-right text-primary"></i></button>
                                                                            </form> --}}
                                                                        @break

                                                                        @case('paystack')
                                                                            {{-- <form
                                                                                action="{{ route('associationPaystackPaymentCallback', ['transaction_id' => $transaction->payment_transaction_id]) }}"
                                                                                method="GET" class="d-inline">
                                                                                @csrf
                                                                                <input type="hidden" name="reference"
                                                                                    value="{{ $transaction->paystack_reference }}">
                                                                                <button type="submit"
                                                                                    class="border-0 bg-transparent p-0"><i
                                                                                        class="fa-solid fa-rotate-right text-primary"></i></button>
                                                                            </form> --}}
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
