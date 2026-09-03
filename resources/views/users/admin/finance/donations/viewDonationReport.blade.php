@extends('layouts.userLayout')
@section('title', $data['donation']->title . ' :: Report')
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
                                        {{ $data['donation']->title }} :: Report
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


                                    </div>

                                </div>

                                <form action="{{ route('viewDonationReport', ['uuid' => $data['donation']->uuid]) }}"
                                    method="GET">
                                    <input type="hidden" name="page_type" id="page_type_input"
                                        value="{{ $data['pageType'] }}">

                                    <div class="d-flex gap-2 justify-content-center my-3 mt-5 flex-wrap">

                                        <button type="button" onclick="submitPageType('successful_page')"
                                            class="btn {{ $data['pageType'] === 'successful_page' ? 'btn-success' : 'btn-outline-success' }}">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Successful
                                            @if (isset($data['successfulPayments']))
                                                <span
                                                    class="badge {{ $data['pageType'] === 'successful_page' ? 'bg-white text-success' : 'bg-success text-white' }} ms-1">
                                                    {{ $data['successfulPayments']->total() }}
                                                </span>
                                            @endif
                                        </button>

                                        <button type="button" onclick="submitPageType('pending_page')"
                                            class="btn {{ $data['pageType'] === 'pending_page' ? 'btn-warning' : 'btn-outline-warning' }}">
                                            <i class="fas fa-clock me-1"></i>
                                            Pending Approval
                                            @if (isset($data['paymentsPendingApproval']))
                                                <span
                                                    class="badge {{ $data['pageType'] === 'pending_page' ? 'bg-white text-warning' : 'bg-warning text-white' }} ms-1">
                                                    {{ $data['paymentsPendingApproval']->total() }}
                                                </span>
                                            @endif
                                        </button>

                                        <button type="button" onclick="submitPageType('failed_page')"
                                            class="btn {{ $data['pageType'] === 'failed_page' ? 'btn-danger' : 'btn-outline-danger' }}">
                                            <i class="fas fa-times-circle me-1"></i>
                                            Failed
                                            @if (isset($data['failedPayments']))
                                                <span
                                                    class="badge {{ $data['pageType'] === 'failed_page' ? 'bg-white text-danger' : 'bg-danger text-white' }} ms-1">
                                                    {{ $data['failedPayments']->total() }}
                                                </span>
                                            @endif
                                        </button>

                                    </div>
                                </form>

                                <script>
                                    function submitPageType(type) {
                                        document.getElementById('page_type_input').value = type;
                                        document.getElementById('page_type_input').closest('form').submit();
                                    }
                                </script>

                                <div class="my_form_container mt-4">
                                    <div class=" pb-4">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered p-3">
                                                    <thead class="justify-content-center text-center align-middle">
                                                        <tr>
                                                            <th scope="col" style="width: 10px" class="table_header">
                                                                S/N
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Donator
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                No of Donations
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Amount Donated
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Amount + Tax
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Amount Settled
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Date/Time
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Payment Status
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Transaction Reference
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    @if ($data['payments']->isEmpty())
                                                        <tr>
                                                            <td colspan="9" class="text-center">
                                                                <h3 class="p-1 m-1 text-red">
                                                                    No Records Found!
                                                                </h3>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tbody>
                                                            @foreach ($data['payments'] as $payment)
                                                                <tr class=" align-middle text-center">
                                                                    <th>{{ $loop->iteration }}</th>
                                                                    <td>
                                                                        @if ($payment->registeredPayer)
                                                                            {{ $payment->registeredPayer->memberPersonalProfile->first_name }}
                                                                            {{ $payment->registeredPayer->memberPersonalProfile->last_name }}
                                                                            <a href="{{ route('viewMember', ['uuid' => $payment->registeredPayer->uuid]) }}"
                                                                                target="_blank"
                                                                                class="badge bg-secondary text-white"
                                                                                title="View Donator">
                                                                                <i class="fas fa-link"></i>
                                                                            </a>
                                                                        @else
                                                                            {{ $payment->non_registered_payer }}
                                                                        @endif
                                                                    </td>
                                                                    @php
                                                                        $samePayerCount = $data['payments']
                                                                            ->getCollection()
                                                                            ->filter(function ($payment) use ($data) {
                                                                                return $payment->donation_id ==
                                                                                    $data['donation']->donation_id &&
                                                                                    ($payment->registered_payer ==
                                                                                        $data['donation']
                                                                                            ->registered_payer ||
                                                                                        $payment->non_registered_payer ==
                                                                                            $data['donation']
                                                                                                ->non_registered_payer);
                                                                            })
                                                                            ->count();
                                                                    @endphp

                                                                    <td>
                                                                        {{ $samePayerCount }}
                                                                    </td>
                                                                    <td>
                                                                        ₦{{ number_format($payment->amount_payable, 2) }}
                                                                    </td>
                                                                    <td>
                                                                        ₦{{ number_format($payment->amount_paid, 2) }}
                                                                    </td>
                                                                    <td>
                                                                        ₦{{ number_format($payment->amount_settled, 2) }}
                                                                    </td>
                                                                    <td>
                                                                        {{ $payment->created_at->format('l, d F Y \a\t h:i A') }}
                                                                    </td>
                                                                    <td>
                                                                        @switch($payment->payment_status)
                                                                            @case('successful')
                                                                                <span
                                                                                    class="badge bg-green text-white">Successful</span>
                                                                            @break

                                                                            @case('failed')
                                                                                <span class="badge bg-red text-white">Failed</span>
                                                                            @break

                                                                            @case('pending')
                                                                                <span
                                                                                    class="badge bg-yellow text-black">Pending</span>
                                                                            @break

                                                                            @default
                                                                                <span
                                                                                    class="badge bg-yellow text-black">{{ $payment->payment_status }}</span>
                                                                        @endswitch

                                                                        @switch($payment->approval_status)
                                                                            @case('approved')
                                                                                <span
                                                                                    class="badge bg-green text-white">Approved</span>
                                                                            @break

                                                                            @case('declined')
                                                                                <span
                                                                                    class="badge bg-red text-white">Declined</span>
                                                                            @break

                                                                            @case('pending')
                                                                                <span
                                                                                    class="badge bg-yellow text-black">Pending</span>
                                                                            @break

                                                                            @default
                                                                                <span
                                                                                    class="badge bg-yellow text-black">Unknown</span>
                                                                        @endswitch
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="text-uppercase">{{ $payment->payment_transaction_id }}</span>
                                                                        <a href="{{ route('viewPaymentTransaction', ['payment_transaction_id' => $payment->payment_transaction_id]) }}"
                                                                            target="_blank"
                                                                            class="badge bg-secondary text-white"
                                                                            title="View Approver">
                                                                            <i class="fas fa-link"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    @endif
                                                </table>
                                                {{-- Pagination --}}
                                                @php
                                                    $paginatedArray = $data['payments'];
                                                @endphp

                                                @include('layouts.include.pagination')

                                                     <div class="px-5">
                                            <a href="{{ route('downloadDonationReport', ['uuid' => $data['donation']->uuid]) }}"
                                                target="_blank" class="btn w-100 btn-danger mt-5"> <i
                                                    class="fas fa-print me-2"></i>
                                                Print Report</a>
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
                        <a class="btn btn-primary"
                            href="{{ route('viewDonation', ['uuid' => $data['donation']->uuid]) }}"> <i
                                class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>
                </div>
            </div>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
