@extends('layouts.userLayout')
@section('title', 'Invoice - ' . $data['invoice']->invoice_number)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Invoice - <span
                                        class="text-uppercase">{{ strtolower($data['invoice']->invoice_number) }}</span>
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Invoice Number:</span>
                                        <span class="text-black text-uppercase">
                                            {{ $data['invoice']->invoice_number }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Amount:</span>
                                        <span class="text-black">
                                            ₦{{ number_format($data['invoice']->amount, 2) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black">
                                            {{ $data['invoice']->description }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Issued Date:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['invoice']->issue_date)->format('l, d F, Y') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Due Date:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['invoice']->due_date)->format('l, d F, Y h:i A') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Status:</span>
                                        <span class="text-black">
                                            @switch($data['invoice']->status)
                                                @case('paid')
                                                    <span class="badge bg-green text-white">Paid</span> show payment transaction
                                                @break

                                                @case('expired')
                                                    <span class="badge bg-red text-white">Expired</span>
                                                @break

                                                @case('pending')
                                                    <span class="badge bg-yellow text-black">Pending</span>
                                                @break

                                                @default
                                                    <span class="badge bg-yellow text-black">{{ $data['invoice']->status }}</span>
                                            @endswitch </span>
                                    </div>
                                    @if ($data['invoice']->status == 'pending')
                                        {{-- expiry countdown start --}}
                                        <div id="invoice-countdown-alert" class="alert alert-warning text-center shadow-sm my-3"
                                            role="alert">

                                            <div class="mb-2">
                                                <i class="fas fa-clock me-2"></i>
                                                <strong>Invoice Payment Deadline</strong>
                                            </div>

                                            <div>
                                                Please complete your payment before this invoice expires.
                                            </div>

                                            <div id="invoice-countdown" class="fw-bold fs-3 mt-2">
                                                Loading...
                                            </div>

                                            <small class="d-block mt-1">
                                                Due:
                                                {{ \Carbon\Carbon::parse($data['invoice']->due_date)->format('d M Y, h:i A') }}
                                            </small>
                                        </div>
                                        <script>
                                            const dueDate = new Date(
                                                "{{ \Carbon\Carbon::parse($data['invoice']->due_date)->format('Y-m-d H:i:s') }}"
                                                .replace(' ', 'T')
                                            ).getTime();

                                            const countdown = document.getElementById('invoice-countdown');
                                            const alertBox = document.getElementById('invoice-countdown-alert');

                                            function updateCountdown() {

                                                const now = new Date().getTime();
                                                const distance = dueDate - now;

                                                if (distance <= 0) {

                                                    countdown.innerHTML = 'Invoice Expired';
                                                    alertBox.classList.remove('alert-warning');
                                                    alertBox.classList.add('alert-danger');

                                                    return;
                                                }

                                                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                const hours = Math.floor(
                                                    (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                                                );
                                                const minutes = Math.floor(
                                                    (distance % (1000 * 60 * 60)) / (1000 * 60)
                                                );
                                                const seconds = Math.floor(
                                                    (distance % (1000 * 60)) / 1000
                                                );

                                                countdown.innerHTML =
                                                    `${days}d ${hours}h ${minutes}m ${seconds}s`;
                                            }

                                            updateCountdown();

                                            setInterval(updateCountdown, 1000);
                                        </script>
                                        {{-- expiry countdown ends --}}
                                        {{-- Pay Now start --}}

                                        <div class="m-4">

                                            <hr>
                                            <form class="form my_form" method="POST"
                                                action="{{ route('memberPayInvoice', ['uuid' => $data['invoice']->uuid]) }}"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <input type="hidden" name="mode" value="electronic">


                                                <div class="mb-4">

                                                    <label class="form-label fw-bold">
                                                        Select Electronic Payment Method
                                                    </label>

                                                    <div class="row">

                                                        @foreach (electronicsPaymentMethods() as $method)
                                                            <div class="col-lg-4 col-md-6 mb-3 electronic-payment-option">

                                                                <input type="radio"
                                                                    id="electronic-payment-method-{{ $data['invoice']->id }}-{{ $method->slug }}"
                                                                    name="electronic_payment_method"
                                                                    value="{{ $method->slug }}" required>

                                                                <label
                                                                    for="electronic-payment-method-{{ $data['invoice']->id }}-{{ $method->slug }}">

                                                                    <div class="electronic-payment-logo">

                                                                        @if ($method->logo)
                                                                            <img src="{{ asset('system/images/paymentMethods/logo/' . $method->logo) }}"
                                                                                class="img-fluid"
                                                                                alt="{{ $method->name }}">
                                                                        @else
                                                                            <i class="fas fa-credit-card fa-3x"></i>
                                                                        @endif

                                                                    </div>

                                                                    <h5 class="mt-3">
                                                                        {{ $method->name }}
                                                                    </h5>

                                                                    <p>
                                                                        Instant confirmation after
                                                                        successful payment.
                                                                    </p>

                                                                    <span class="electronic-payment-selected">
                                                                        <i class="fas fa-check"></i>
                                                                    </span>

                                                                </label>

                                                            </div>
                                                        @endforeach

                                                    </div>

                                                </div>
                                                <hr>

                                                <button type="submit" class="green-button submit-btn">
                                                    <span class="btn-text">
                                                        Pay Now
                                                    </span>

                                                    <span class="btn-loading" style="display:none;">
                                                        <i class="fas fa-spinner fa-spin"></i> Processing...
                                                    </span>
                                                </button>
                                            </form>
                                            <hr>
                                        </div>
                                        {{-- Pay Now end --}}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('myInvoices') }}">
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
