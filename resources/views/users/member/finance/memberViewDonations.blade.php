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
                                <div class="my_form_container p-3">
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
                                                <span style="font-weight: bold">Amount Raised:</span>
                                                <span class="text-black">
                                                    ₦{{ number_format($data['donation']->getAmountRaisedAttribute(), 2) }}
                                                </span>
                                            </div>

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Your contributions:</span>
                                                <span class="text-black">
                                                    ₦{{ number_format($data['myContribution'], 2) }}
                                                </span>
                                            </div>

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Status:</span>
                                                <span class="text-black">
                                                    @switch($data['donation']->is_active)
                                                        @case(true)
                                                            <span class="badge bg-success-subtle text-success">Active</span> |
                                                            Accepting Donations
                                                        @break

                                                        @default
                                                            <span class="badge bg-danger-subtle text-danger">Inactive</span> | Not
                                                            Accepting Donations
                                                    @endswitch
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Deadline:</span>
                                                <span class="text-black">
                                                    @if (filled($data['donation']->deadline))
                                                        <span>
                                                            {{ \Carbon\Carbon::parse($data['donation']->deadline)->format('l, d F, Y h:i A') }}
                                                        </span>
                                                        <span class="badge bg-warning text-dark ms-2"
                                                            id="donation-countdown">
                                                            Calculating...
                                                        </span>
                                                    @else
                                                        N/A
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

                                            {{-- Countdown script — handles both deadline and target conditions --}}
                                            @if (filled($data['donation']->deadline))
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {

                                                        const deadline = new Date(
                                                            "{{ \Carbon\Carbon::parse($data['donation']->deadline)->toIso8601String() }}").getTime();
                                                        const countdown = document.getElementById('donation-countdown');

                                                        @if ($hasTarget)
                                                            const targetReached = {{ $targetReached ? 'true' : 'false' }};
                                                        @else
                                                            const targetReached = false;
                                                        @endif

                                                        function updateCountdown() {
                                                            // If target already reached, override countdown
                                                            if (targetReached) {
                                                                countdown.textContent = 'Target reached';
                                                                countdown.className = 'badge bg-success text-white ms-2';
                                                                return;
                                                            }

                                                            const distance = deadline - Date.now();

                                                            if (distance <= 0) {
                                                                countdown.textContent = 'Deadline reached';
                                                                countdown.className = 'badge bg-danger text-white ms-2';
                                                                return;
                                                            }

                                                            const days = Math.floor(distance / 86400000);
                                                            const hours = Math.floor((distance % 86400000) / 3600000);
                                                            const minutes = Math.floor((distance % 3600000) / 60000);
                                                            const seconds = Math.floor((distance % 60000) / 1000);

                                                            // Change badge color based on urgency
                                                            if (days === 0 && hours < 6) {
                                                                countdown.className = 'badge bg-danger text-white ms-2';
                                                            } else if (days < 2) {
                                                                countdown.className = 'badge bg-warning text-dark ms-2';
                                                            } else {
                                                                countdown.className = 'badge bg-success text-white ms-2';
                                                            }

                                                            countdown.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s remaining`;
                                                        }

                                                        updateCountdown();
                                                        setInterval(updateCountdown, 1000);
                                                    });
                                                </script>
                                            @endif

                                            <div class="m-2"></div>
                                            @php
                                                $donation = $data['donation'];

                                                $amountRaised = $donation->getAmountRaisedAttribute();
                                                $myContribution = $data['myContribution'] ?? 0;

                                                // Deadline condition
                                                $deadlineReached =
                                                    filled($donation->deadline) &&
                                                    now()->greaterThanOrEqualTo(
                                                        \Carbon\Carbon::parse($donation->deadline),
                                                    );

                                                // Target amount condition
                                                $targetReached =
                                                    filled($donation->target_amount) &&
                                                    $donation->target_amount > 0 &&
                                                    $amountRaised >= $donation->target_amount;

                                                // Can the user still contribute?
                                                $canContribute =
                                                    $donation->is_active && !$deadlineReached && !$targetReached;

                                                // Has the user contributed before?
                                                $hasContributed = $myContribution > 0;
                                            @endphp

                                            @if ($canContribute)
                                                {{-- ── User HAS contributed ── --}}
                                                @if (isset($hasContributed) && $hasContributed)
                                                    <div class="mt-3">
                                                        <div class="alert alert-success">
                                                            <i class="fas fa-check-circle me-2"></i>
                                                            <strong>
                                                                Thank you for your generous contribution of
                                                                ₦{{ number_format($data['myContribution'], 2) }}.
                                                                @if ($data['donation']->is_active)
                                                                    This donation is still active and accepting
                                                                    contributions. Every bit counts - feel free to give more
                                                                    if you can.
                                                                @endif
                                                            </strong>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- ── Inline contribution form (shown for both first-time and repeat contributors) ── --}}
                                                <div class="card border-0 bg-light rounded-3 p-4 mt-3">
                                                    <h5 class="fw-bold mb-1">
                                                        <i class="fas fa-hand-holding-heart me-2 text-success"></i>
                                                        {{ isset($hasContributed) && $hasContributed ? 'Contribute More' : 'Support This Donation' }}
                                                    </h5>
                                                    <p class="text-muted mb-3" style="font-size:14px;">
                                                        Your contribution can make a difference. Enter your desired amount
                                                        below.
                                                    </p>

                                                    <form
                                                        action="{{ route('donation.contribute', $data['donation']->uuid) }}"
                                                        method="POST" id="contributionForm">
                                                        @csrf

                                                        {{-- Amount input with dynamic total display --}}
                                                        <div class="form-group mb-3">
                                                            <label for="contribution_amount" class="form-label fw-semibold">
                                                                Amount (₦) <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <span class="input-group-text fw-bold">₦</span>
                                                                <input type="number" id="contribution_amount"
                                                                    name="amount" class="form-control form-control-lg"
                                                                    placeholder="Enter amount e.g. 5000" min="1"
                                                                    step="any" value="{{ old('amount') }}" required>
                                                            </div>
                                                            @error('amount')
                                                                <p class="text-danger small mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>

                                                        {{-- Dynamic amount confirmation --}}
                                                        <div id="contribution-summary"
                                                            class="alert alert-success d-flex justify-content-between align-items-center mb-3"
                                                            style="display:none!important">
                                                            <span class="fw-semibold">
                                                                <i class="fas fa-wallet me-2"></i> You are contributing:
                                                            </span>
                                                            <span class="fw-bold fs-4"
                                                                id="contribution-amount-display">₦0.00</span>
                                                        </div>

                                                        {{-- Target left info --}}
                                                        @php
                                                            $hasTarget =
                                                                filled($data['donation']->target_amount) &&
                                                                $data['donation']->target_amount > 0;
                                                            $amountRaised = $data[
                                                                'donation'
                                                            ]->getAmountRaisedAttribute();
                                                            $amountLeft = $hasTarget
                                                                ? max(
                                                                    0,
                                                                    $data['donation']->target_amount - $amountRaised,
                                                                )
                                                                : null;
                                                        @endphp

                                                        @if ($hasTarget && $amountLeft > 0)
                                                            <div class="alert alert-info py-2 mb-3">
                                                                <i class="fas fa-info-circle me-2"></i>
                                                                <strong>₦{{ number_format($amountLeft, 2) }}</strong> still
                                                                needed to reach the target.
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-info ms-2"
                                                                    onclick="document.getElementById('contribution_amount').value = '{{ $amountLeft }}'; document.getElementById('contribution_amount').dispatchEvent(new Event('input'))">
                                                                    Contribute full amount
                                                                </button>
                                                            </div>
                                                        @endif

                                                        {{-- Payment methods --}}
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Select Payment Method</label>
                                                            <div class="row">
                                                                @foreach (electronicsPaymentMethods() as $method)
                                                                    <div
                                                                        class="col-lg-4 col-md-6 mb-3 electronic-payment-option">
                                                                        <input type="radio"
                                                                            id="donate-payment-{{ $data['donation']->id }}-{{ $method->slug }}"
                                                                            name="electronic_payment_method"
                                                                            value="{{ $method->slug }}" required>
                                                                        <label
                                                                            for="donate-payment-{{ $data['donation']->id }}-{{ $method->slug }}">
                                                                            <div class="electronic-payment-logo">
                                                                                @if ($method->logo)
                                                                                    <img src="{{ asset('system/images/paymentMethods/logo/' . $method->logo) }}"
                                                                                        class="img-fluid"
                                                                                        alt="{{ $method->name }}">
                                                                                @else
                                                                                    <i
                                                                                        class="fas fa-credit-card fa-3x"></i>
                                                                                @endif
                                                                            </div>
                                                                            <h5 class="mt-3">{{ $method->name }}</h5>
                                                                            <p>Instant confirmation after successful
                                                                                payment.</p>
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
                                                                <i class="fas fa-heart me-2"></i> Contribute Now
                                                            </span>
                                                            <span class="btn-loading" style="display:none;">
                                                                <i class="fas fa-spinner fa-spin"></i> Processing...
                                                            </span>
                                                        </button>

                                                    </form>
                                                </div>
                                            @else
                                                {{-- ── Donation closed ── --}}
                                                @if (isset($hasContributed) && $hasContributed)
                                                    <div class="mt-3">
                                                        <strong>This donation is no longer active. You can no longer
                                                            contribute.</strong>
                                                    </div>
                                                @else
                                                    <div class="alert alert-secondary text-center mt-3" role="alert">
                                                        <div class="mb-2">
                                                            <i class="fas fa-info-circle fa-lg me-2"></i>
                                                            <strong>Donation Closed</strong>
                                                        </div>
                                                        <div>This donation is no longer active. You can no longer
                                                            contribute.</div>
                                                    </div>
                                                @endif
                                            @endif

                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const input = document.getElementById('contribution_amount');
                                                    const summary = document.getElementById('contribution-summary');
                                                    const display = document.getElementById('contribution-amount-display');

                                                    if (!input) return;

                                                    function formatNaira(amount) {
                                                        return '₦' + parseFloat(amount).toLocaleString('en-NG', {
                                                            minimumFractionDigits: 2,
                                                            maximumFractionDigits: 2
                                                        });
                                                    }

                                                    input.addEventListener('input', function() {
                                                        const val = parseFloat(this.value) || 0;
                                                        if (val > 0) {
                                                            display.textContent = formatNaira(val);
                                                            summary.style.removeProperty('display');
                                                        } else {
                                                            summary.style.setProperty('display', 'none', 'important');
                                                        }
                                                    });

                                                    // Restore old value
                                                    if (input.value && parseFloat(input.value) > 0) {
                                                        input.dispatchEvent(new Event('input'));
                                                    }

                                                    // Prevent double submit
                                                    document.getElementById('contributionForm').addEventListener('submit', function() {
                                                        const btnText = this.querySelector('.btn-text');
                                                        const btnLoading = this.querySelector('.btn-loading');
                                                        if (btnText) btnText.style.display = 'none';
                                                        if (btnLoading) btnLoading.style.display = 'inline-block';
                                                    });
                                                });
                                            </script>
                                        </div>

                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4">
                        <a class="btn btn-primary" href="{{ route('memberDonations') }}"> <i
                                class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>
                </div>
            </div>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
