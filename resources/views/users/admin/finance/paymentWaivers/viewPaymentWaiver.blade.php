@extends('layouts.userLayout')
@section('title', 'Payment Waiver - ' . $data['waiver']->waiver_id)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @if ($data['waiver']->payer)
                                @php
                                    $data['member'] = $data['waiver']->payer;
                                @endphp
                                @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')
                            @else
                                @include('users.admin.finance.transactions.include.nonRegisteredPayerSidebar')
                            @endif

                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Payment Regularization - <span
                                        class="text-uppercase">{{ strtolower($data['waiver']->waiver_id) }}</span>
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">ID:</span>
                                        <span class="text-black text-uppercase">
                                            {{ $data['waiver']->waiver_id }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Paid By:</span>
                                        <span class="text-black">
                                            {{ $data['waiver']->payer?->memberPersonalProfile->first_name . ' ' . $data['waiver']->payer?->memberPersonalProfile->last_name }}
                                            <a href="{{ route('viewMember', ['uuid' => $data['waiver']->payer->uuid]) }}"
                                                target="_blank" class="bg-secondary text-white badge" title="View Payer">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Phone:</span>
                                        <span class="text-black">
                                            {{ $data['waiver']->payer->phone }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Email:</span>
                                        <span class="text-black">
                                            {{ $data['waiver']->payer->email }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-2">
                                        <span style="font-weight: bold">Due:</span>
                                        <span class="text-black">
                                            @php
                                                $academic = $data['waiver']->payer->memberAcademicInfo;
                                                $level = studentLevelAtDue($academic, $data['waiver']->due);
                                            @endphp

                                            {{ $data['waiver']->due->title }}
                                            ({{ $data['waiver']->due->academic_year }}/{{ $data['waiver']->due->academic_year + 1 }}
                                            Academic Session)
                                            -
                                            {{ currentLevel($academic->theProgram, $level) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Initiated:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['waiver']->initiated_at)
                                                    {{ \Carbon\Carbon::parse($data['waiver']->initiated_at)->format('l, d F, Y h:i A') }}
                                                @endif
                                                -
                                                @if ($data['waiver']->initiated_by)

                                                    @if ($data['waiver']->initiatedBy?->memberPersonalProfile)
                                                        {{ trim(
                                                            $data['waiver']->initiatedBy->memberPersonalProfile->first_name .
                                                                ' ' .
                                                                ($data['waiver']->initiatedBy->memberPersonalProfile->middle_name
                                                                    ? $data['waiver']->initiatedBy->memberPersonalProfile->middle_name . ' '
                                                                    : '') .
                                                                $data['waiver']->initiatedBy->memberPersonalProfile->last_name,
                                                        ) }}
                                                    @else
                                                        {{ $data['waiver']->initiated_by }}
                                                    @endif

                                                    @if ($data['waiver']->initiatedBy)
                                                        <a href="{{ route('viewMember', ['uuid' => $data['waiver']->initiatedBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Initiator">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif

                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Reason for waiver:</span>
                                        <span class="text-black text-capitalize">
                                            {{ $data['waiver']->reason }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Status:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @switch($data['waiver']->approval_status)
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
                                                        <span
                                                            class="badge bg-yellow text-black">{{ $data['waiver']->approval_status }}</span>
                                                @endswitch

                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Processed:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['waiver']->approved_at)
                                                    {{ \Carbon\Carbon::parse($data['waiver']->approved_at)->format('l, d F, Y h:i A') }}
                                                @endif
                                                -
                                                @if ($data['waiver']->processed_by)

                                                    @if ($data['waiver']->processedBy?->memberPersonalProfile)
                                                        {{ trim(
                                                            $data['waiver']->processedBy->memberPersonalProfile->first_name .
                                                                ' ' .
                                                                ($data['waiver']->processedBy->memberPersonalProfile->middle_name
                                                                    ? $data['waiver']->processedBy->memberPersonalProfile->middle_name . ' '
                                                                    : '') .
                                                                $data['waiver']->processedBy->memberPersonalProfile->last_name,
                                                        ) }}
                                                    @else
                                                        {{ $data['waiver']->processed_by }}
                                                    @endif

                                                    @if ($data['waiver']->processedBy)
                                                        <a href="{{ route('viewMember', ['uuid' => $data['waiver']->processedBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Approver">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif

                                                @endif
                                            </span>
                                        </span>
                                    </div>



                                    @php
                                        $approvalDeadline = $data['waiver']->approved_at
                                            ? $data['waiver']->approved_at->copy()->addHours(48)
                                            : null;

                                        $hasGracePeriod = $approvalDeadline && now()->lt($approvalDeadline);

                                        $canEdit =
                                            Auth::user()->hasPermission('transactions.update') &&
                                            ($data['waiver']->approval_status === 'pending' || $hasGracePeriod);
                                    @endphp


                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {

                                            const countdown = document.getElementById('waiver-countdown');

                                            if (!countdown) {
                                                return;
                                            }

                                            const deadline = new Date(
                                                countdown.dataset.deadline
                                            ).getTime();

                                            function updateCountdown() {

                                                const now = new Date().getTime();
                                                const remaining = deadline - now;

                                                if (remaining <= 0) {

                                                    countdown.innerHTML = 'Expired';
                                                    countdown.classList.remove('text-danger');
                                                    countdown.classList.add('text-muted');

                                                    // Disable/remove edit controls
                                                    document.querySelectorAll('.waiver-edit-control')
                                                        .forEach(function(element) {
                                                            element.style.display = 'none';
                                                        });

                                                    return;
                                                }

                                                const totalSeconds = Math.floor(remaining / 1000);

                                                const days = Math.floor(totalSeconds / 86400);
                                                const hours = Math.floor((totalSeconds % 86400) / 3600);
                                                const minutes = Math.floor((totalSeconds % 3600) / 60);
                                                const seconds = totalSeconds % 60;

                                                countdown.textContent =
                                                    `${days}d ${String(hours).padStart(2, '0')}:` +
                                                    `${String(minutes).padStart(2, '0')}:` +
                                                    `${String(seconds).padStart(2, '0')}`;
                                            }

                                            updateCountdown();

                                            setInterval(updateCountdown, 1000);

                                        });
                                    </script>

                                    @if ($canEdit)
                                        @if ($hasGracePeriod && $approvalDeadline)
                                            <span class="text-warning fs-4 m-2">
                                                <i class="fas fa-clock me-1"></i>
                                                Modification window:
                                                <strong id="waiver-countdown"
                                                    data-deadline="{{ $approvalDeadline->toIso8601String() }}">
                                                    Calculating...
                                                </strong>
                                            </span>
                                        @endif
                                        <div class="d-flex align-items-center mt-4 mb-4">
                                            <hr class="flex-grow-1">
                                            <span class="mx-2 text-black">Approval</span>
                                            <hr class="flex-grow-1">
                                        </div>

                                        <form class="form my_form" method="POST"
                                            action="{{ route('processPaymentWaiverSubmit', ['uuid' => $data['waiver']->uuid]) }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="approval_status">Approval Status: <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" name="approval_status" required>
                                                        <option selected disabled>
                                                            -Select-</option>
                                                        <option value="approved"
                                                            {{ $data['waiver']->approval_status == 'approved' ? 'selected' : '' }}>
                                                            Approved
                                                        </option>
                                                        <option value="pending"
                                                            {{ $data['waiver']->approval_status == 'pending' ? 'selected' : '' }}>
                                                            Pending
                                                        </option>
                                                        <option value="declined"
                                                            {{ $data['waiver']->approval_status == 'declined' ? 'selected' : '' }}>
                                                            Declined
                                                        </option>
                                                    </select>
                                                    <p style="color: red">{{ $errors->first('approval_status') }}</p>
                                                </div>
                                            </div>
                                            <hr>

                                            <button type="submit" class="green-button submit-btn">
                                                <span class="btn-text">
                                                    Submit
                                                </span>

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
                        <a class="btn btn-primary" href="{{ route('paymentWaiverRequests') }}">
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
