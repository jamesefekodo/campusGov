@extends('layouts.userLayout')
@section('title', $data['application']->position->position . ' - ' . $data['election']->title)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.member.elections.include.view_election_inner_nav')
            </div>
            <div class="">
                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="my_form_container pb-5">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Application for {{ $data['application']->position->position }} - <span
                                        class="text-uppercase">{{ strtolower($data['election']->title) }}
                                        {{ $data['election']->academic_session . '/' . $data['election']->academic_session + 1 }}</span>
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">ID:</span>
                                        <span class="text-black text-uppercase">
                                            {{ $data['election']->election_id }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Status:</span>
                                        <span class="text-black">
                                            @switch($data['election']->status)
                                                @case('draft')
                                                    <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                                @break

                                                @case('awaiting_approval')
                                                    <span class="badge bg-warning-subtle text-warning">Awaiting
                                                        Approval</span>
                                                @break

                                                @case('new')
                                                    <span class="badge bg-primary-subtle text-primary">New</span>
                                                @break

                                                @case('registration')
                                                    <span class="badge bg-primary-subtle text-primary">Registration</span>
                                                @break

                                                @case('screening')
                                                    <span class="badge bg-warning-subtle text-warning">Screening</span>
                                                @break

                                                @case('campaign')
                                                    <span class="badge bg-warning-subtle text-warning">Campaign</span>
                                                @break

                                                @case('voting')
                                                    <span class="badge bg-blue text-white">Ongoing</span>
                                                @break

                                                @case('completed')
                                                    <span class="badge bg-success-subtle text-success">Completed</span>
                                                @break

                                                @case('cancelled')
                                                    <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                                @break

                                                @case('suspended')
                                                    <span class="badge bg-danger-subtle text-danger">Suspended</span>
                                                @break

                                                @case('declined')
                                                    <span class="badge bg-danger-subtle text-danger">Declined</span>
                                                @break

                                                @default
                                                    <span
                                                        class="badge bg-danger-subtle text-danger">{{ $data['election']->status }}</span>
                                            @endswitch
                                            {{-- countdown for sales of form closure start --}}
                                            @if ($data['election']->status == 'registration')
                                                <span
                                                    class="badge rounded-pill bg-danger-subtle text-danger border border-danger px-3 py-2">
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    Registration closes in
                                                    <strong id="registration-countdown" class="ms-1"></strong>
                                                </span>

                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {

                                                        const deadline = new Date(
                                                            "{{ \Carbon\Carbon::parse($data['election']->form_sales_ends)->toIso8601String() }}"
                                                        ).getTime();

                                                        const countdown = document.getElementById('registration-countdown');

                                                        function updateCountdown() {

                                                            const now = new Date().getTime();
                                                            const distance = deadline - now;

                                                            if (distance <= 0) {
                                                                countdown.innerHTML = 'Registration has ended.';
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
                                                    });
                                                </script>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {

                                                        const deadline = new Date(
                                                            "{{ \Carbon\Carbon::parse($data['election']->form_sales_ends)->toIso8601String() }}"
                                                        ).getTime();

                                                        const countdown = document.getElementById('registration-countdown-large');

                                                        if (!countdown) {
                                                            return;
                                                        }

                                                        function updateCountdown() {

                                                            const now = new Date().getTime();
                                                            const distance = deadline - now;

                                                            if (distance <= 0) {
                                                                countdown.innerHTML = 'Registration has ended.';
                                                                return;
                                                            }

                                                            const days = Math.floor(
                                                                distance / (1000 * 60 * 60 * 24)
                                                            );

                                                            const hours = Math.floor(
                                                                (distance % (1000 * 60 * 60 * 24)) /
                                                                (1000 * 60 * 60)
                                                            );

                                                            const minutes = Math.floor(
                                                                (distance % (1000 * 60 * 60)) /
                                                                (1000 * 60)
                                                            );

                                                            const seconds = Math.floor(
                                                                (distance % (1000 * 60)) / 1000
                                                            );

                                                            countdown.innerHTML =
                                                                `${days}d ${hours}h ${minutes}m ${seconds}s`;
                                                        }

                                                        updateCountdown();

                                                        setInterval(updateCountdown, 1000);

                                                    });
                                                </script>
                                            @endif
                                            {{-- countdown for sales of form closure end --}}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Title:</span>
                                        <span class="text-black">
                                            {{ $data['election']->title }}
                                            {{ $data['election']->academic_session . '/' . $data['election']->academic_session + 1 }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black">
                                            {{ $data['election']->description }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Form sales starts:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['election']->form_sales_starts)->format('l, d F, Y h:i A') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Form sales ends:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['election']->form_sales_ends)->format('l, d F, Y h:i A') }}
                                        </span> <a class="badge bg-secondary"
                                            href="{{ route('memberViewElection', ['uuid' => $data['election']->uuid]) }}"><i
                                                class="fas fa-link"></i></a>
                                    </div>
                                    <hr>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Position:</span>
                                        <span class="text-black">
                                            {{ $data['application']->position->position }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Application Fee:</span>
                                        <span class="text-black">
                                            ₦{{ number_format($data['application']->position->application_fee, 2) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Candidate:</span>
                                        <span class="text-black">
                                            {{ $data['application']->theCandidate->username }} <a
                                                class="badge bg-secondary" href="#"><i class="fas fa-link"></i></a>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Application Type:</span>
                                        <span class="text-black text-capitalize">
                                            {{ $data['application']->application_type }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Initiated By:</span>
                                        <span class="text-black">
                                            {{ $data['application']->initiatedBy->username }} <a class="badge bg-secondary"
                                                href="#"><i class="fas fa-link"></i></a>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Application Status:</span>
                                        <span class="text-black">
                                            @switch($data['application']->status)
                                                @case('draft')
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        Draft
                                                    </span>
                                                @break

                                                @case('pending_approval')
                                                    <span class="badge bg-warning-subtle text-warning">
                                                        Pending Approval
                                                    </span>
                                                @break

                                                @case('approved')
                                                    <span class="badge bg-success-subtle text-success">
                                                        Approved
                                                    </span>
                                                @break

                                                @case('declined')
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        Declined
                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        {{ $data['application']->status }}
                                                    </span>
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Payment Status:</span>
                                        <span class="text-black">
                                            @if ($data['application']->successfulPaymentTransaction()->exists())
                                                @switch($data['application']->successfulPaymentTransaction?->payment_status
                                                    === 'successful')
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
                                                            class="badge bg-yellow text-black">{{ $data['application']->successfulPaymentTransaction()->payment_status }}</span>
                                                @endswitch

                                                @switch($data['application']->successfulPaymentTransaction?->payment_status
                                                    === 'successful')
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
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-12 p-1">
                                        <span style="font-weight: bold">Bio:</span>
                                        <span class="text-black">
                                            {!! $data['application']->bio !!}
                                        </span>
                                    </div>
                                    <div class="col-md-12 p-1">
                                        <span style="font-weight: bold">Manifesto:</span>
                                        <span class="text-black">
                                            {!! $data['application']->manifesto !!}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-end flex-wrap mt-3">
                                        @if ($data['application']->status == 'pending_approval')
                                            <a href="{{ route('printElectionApplicationEndorsementForm', ['uuid' => $data['application']->uuid]) }}"
                                                class="btn btn-primary m-1 d-flex align-items-center">
                                                <i class="fas fa-print me-2"></i>
                                                <span>Print Endorsement Form</span>
                                            </a>
                                        @endif
                                        @if (in_array($data['application']->status, ['draft', 'pending_approval']))
                                            <button class="btn btn-warning m-1 d-flex align-items-center"
                                                data-toggle="modal" data-target="#updateApplicationBioManifestoModal">
                                                <i class="fas fa-edit me-2"></i>
                                                <span>Update Bio/Manifesto</span>
                                            </button>
                                        @endif

                                    </div>
                                    @include('users.member.elections.include.updateApplicationBioManifestoModal')
                                    @if (
                                        $data['election']->status == 'registration' &&
                                            $data['application']->status == 'draft' &&
                                            !$data['application']->successfulPaymentTransaction()->exists())
                                        <hr class="mt-3">
                                        <div class="alert alert-danger text-center py-4 px-4 shadow-sm" role="alert">

                                            <div class="mb-2">
                                                <i class="fas fa-hourglass-half fa-2x me-2"></i>
                                                <strong class="fs-4">Registration Closes In</strong>
                                            </div>

                                            <div id="registration-countdown-large" class="fw-bold display-5 mb-3">
                                            </div>

                                            <hr>

                                            <p class="fs-5 mb-0">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Your application has not been submitted because you have not yet completed
                                                the application process. Kindly complete all required forms, make the
                                                necessary payment, and submit your application.
                                            </p>

                                        </div>
                                        <hr class="mb-3">
                                    @endif

                                    @if (!$data['application']->successfulPaymentTransaction()->exists())
                                        <form class="form my_form" method="POST"
                                            action="{{ route('memberPayForElectionCandidacy', ['uuid' => $data['application']->uuid]) }}"
                                            enctype="multipart/form-data">
                                            @csrf

                                            <p class="text-blue fs-1 text-center w-100">Pay
                                                ₦{{ number_format($data['application']->position->application_fee, 2) }}
                                            </p>

                                            <input type="hidden" name="mode" value="electronic">


                                            <div class="mb-4">

                                                <label class="form-label fw-bold">
                                                    Select Electronic Payment Method
                                                </label>

                                                <div class="row">

                                                    @foreach (electronicsPaymentMethods() as $method)
                                                        <div class="col-lg-4 col-md-6 mb-3 electronic-payment-option">

                                                            <!-- Added unique ID using $method->slug -->
                                                            <input type="radio"
                                                                id="electronic-payment-method-{{ $method->slug }}"
                                                                name="electronic_payment_method"
                                                                value="{{ $method->slug }}" required>

                                                            <!-- Matched the 'for' attribute to the unique ID -->
                                                            <label for="electronic-payment-method-{{ $method->slug }}">

                                                                <div class="electronic-payment-logo">
                                                                    @if ($method->logo)
                                                                        <img src="{{ asset('system/images/paymentMethods/logo/' . $method->logo) }}"
                                                                            class="img-fluid" alt="{{ $method->name }}">
                                                                    @else
                                                                        <i class="fas fa-credit-card fa-3x"></i>
                                                                    @endif
                                                                </div>

                                                                <h5 class="mt-3">
                                                                    {{ $method->name }}
                                                                </h5>

                                                                <p>
                                                                    Instant confirmation after successful payment.
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
                                                    <i class="fas fa-money-bill-wave"></i> Pay Now </span>

                                                <span class="btn-loading" style="display:none;">
                                                    <i class="fas fa-spinner fa-spin"></i> Processing...
                                                </span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('memberNewElections') }}">
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
