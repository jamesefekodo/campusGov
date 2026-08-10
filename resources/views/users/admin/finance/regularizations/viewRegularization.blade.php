@extends('layouts.userLayout')
@section('title', 'Payment Regularization - ' . $data['regularization']->regularization_id)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @if ($data['regularization']->payer)
                                @php
                                    $data['member'] = $data['regularization']->payer;
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
                                        class="text-uppercase">{{ strtolower($data['regularization']->regularization_id) }}</span>
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">ID:</span>
                                        <span class="text-black text-uppercase">
                                            {{ $data['regularization']->regularization_id }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Paid By:</span>
                                        <span class="text-black">
                                            {{ $data['regularization']->payer?->memberPersonalProfile->first_name . ' ' . $data['regularization']->payer?->memberPersonalProfile->last_name }}
                                            <a href="{{ route('viewMember', ['uuid' => $data['regularization']->payer->uuid]) }}"
                                                target="_blank" class="bg-secondary text-white badge" title="View Payer">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Phone:</span>
                                        <span class="text-black">
                                            {{ $data['regularization']->payer->phone }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Email:</span>
                                        <span class="text-black">
                                            {{ $data['regularization']->payer->email }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black">
                                            {{ $data['regularization']->description }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-2">
                                        <span style="font-weight: bold">Payment Purpose:</span>
                                        <span class="text-black">
                                            @php
                                                $academic = $data['regularization']->payer->memberAcademicInfo;
                                                $level = studentLevelAtDue($academic, $data['regularization']->due);
                                            @endphp

                                            {{ $data['regularization']->due->title }}
                                            ({{ $data['regularization']->due->academic_year }}/{{ $data['regularization']->due->academic_year + 1 }}
                                            Academic Session)
                                            -
                                            {{ currentLevel($academic->theProgram, $level) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Amount Paid:</span>
                                        <span class="text-blue fs-5 fw-bold">
                                            ₦{{ number_format($data['regularization']->amount_paid, 2) }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Payment Method:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['regularization']->payment_mode == 'manual')
                                                    @php
                                                        $methods = [
                                                            'cash' => 'Cash',
                                                            'bank_transfer' => 'Bank Transfer/Deposit',
                                                            'cheque' => 'Cheque',
                                                        ];
                                                    @endphp

                                                    {{ $methods[$data['regularization']->manual_payment_method] ?? ucfirst(str_replace('_', ' ', $data['regularization']->manual_payment_method)) }}
                                                @endif
                                                @if ($data['regularization']->payment_mode == 'electronic')
                                                    <span class="text-capitalize">
                                                        {{ $data['regularization']->electronic_payment_method }}
                                                    </span>
                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Status:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @switch($data['regularization']->approval_status)
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
                                                            class="badge bg-yellow text-black">{{ $data['regularization']->approval_status }}</span>
                                                @endswitch

                                            </span>
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Payment Date:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                {{ \Carbon\Carbon::parse($data['regularization']->payment_date)->format('l, d F, Y') }}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Initiated:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['regularization']->initiated_at)
                                                    {{ \Carbon\Carbon::parse($data['regularization']->initiated_at)->format('l, d F, Y h:i A') }}
                                                @endif
                                                -
                                                @if ($data['regularization']->initiated_by)

                                                    @if ($data['regularization']->initiatedBy?->memberPersonalProfile)
                                                        {{ trim(
                                                            $data['regularization']->initiatedBy->memberPersonalProfile->first_name .
                                                                ' ' .
                                                                ($data['regularization']->initiatedBy->memberPersonalProfile->middle_name
                                                                    ? $data['regularization']->initiatedBy->memberPersonalProfile->middle_name . ' '
                                                                    : '') .
                                                                $data['regularization']->initiatedBy->memberPersonalProfile->last_name,
                                                        ) }}
                                                    @else
                                                        {{ $data['regularization']->initiated_by }}
                                                    @endif

                                                    @if ($data['regularization']->initiatedBy)
                                                        <a href="{{ route('viewMember', ['uuid' => $data['regularization']->initiatedBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Approver">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif

                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Processed:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['regularization']->approved_at)
                                                    {{ \Carbon\Carbon::parse($data['regularization']->approved_at)->format('l, d F, Y h:i A') }}
                                                @endif
                                                -
                                                @if ($data['regularization']->processed_by)

                                                    @if ($data['regularization']->processedBy?->memberPersonalProfile)
                                                        {{ trim(
                                                            $data['regularization']->processedBy->memberPersonalProfile->first_name .
                                                                ' ' .
                                                                ($data['regularization']->processedBy->memberPersonalProfile->middle_name
                                                                    ? $data['regularization']->processedBy->memberPersonalProfile->middle_name . ' '
                                                                    : '') .
                                                                $data['regularization']->processedBy->memberPersonalProfile->last_name,
                                                        ) }}
                                                    @else
                                                        {{ $data['regularization']->processed_by }}
                                                    @endif

                                                    @if ($data['regularization']->processedBy)
                                                        <a href="{{ route('viewMember', ['uuid' => $data['regularization']->processedBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Approver">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif

                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Balance Update:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @switch($data['regularization']->update_balance)
                                                    @case(true)
                                                        <span class="badge bg-success-subtle text-success">True</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-danger-subtle text-danger">True</span>
                                                @endswitch
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Remarks:</span>
                                        <span class="text-black">
                                            {{ $data['regularization']->remarks }}
                                        </span>
                                    </div>
                                    @if (
                                        $data['regularization']->proof_of_payment &&
                                            file_exists(public_path(
                                                    'assets/tenant_' .
                                                        tenant()->id .
                                                        '/uploads/paymentTransactions/proof/' .
                                                        $data['regularization']->proof_of_payment)))
                                        @php
                                            $proofModalId = 'proofImageModal_' . $data['regularization']->id;
                                        @endphp

                                        <div class="col-md-12 p-1">
                                            <span style="font-weight: bold">Proof:</span>

                                            <span class="text-black">
                                                <span class="text-capitalize">

                                                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/paymentTransactions/proof/' . $data['regularization']->proof_of_payment) }}"
                                                        alt="Proof of Payment" class="img-thumbnail ms-2"
                                                        style="max-width:150px; cursor: zoom-in;" data-bs-toggle="modal"
                                                        data-bs-target="#{{ $proofModalId }}">

                                                </span>
                                            </span>
                                        </div>

                                        <!-- Zoom Modal -->
                                        <div class="modal fade" id="{{ $proofModalId }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content border-0 bg-transparent">

                                                    <div class="text-end mb-2">
                                                        <button type="button" class="btn btn-light rounded-circle"
                                                            data-bs-dismiss="modal" aria-label="Close">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>

                                                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/paymentTransactions/proof/' . $data['regularization']->proof_of_payment) }}"
                                                        class="img-fluid rounded shadow" alt="Proof of Payment">

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($data['regularization']->approval_status == 'pending' && Auth::user()->hasPermission('transactions.update'))
                                        <div class="d-flex align-items-center mt-4 mb-4">
                                            <hr class="flex-grow-1">
                                            <span class="mx-2 text-black">Approval</span>
                                            <hr class="flex-grow-1">
                                        </div>

                                        <form class="form my_form" method="POST"
                                            action="{{ route('processPaymentRegularizationSubmit', ['uuid' => $data['regularization']->uuid]) }}"
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
                                                            {{ $data['regularization']->approval_status == 'approved' ? 'selected' : '' }}>
                                                            Approved
                                                        </option>
                                                        <option value="pending"
                                                            {{ $data['regularization']->approval_status == 'pending' ? 'selected' : '' }}>
                                                            Pending
                                                        </option>
                                                        <option value="declined"
                                                            {{ $data['regularization']->approval_status == 'declined' ? 'selected' : '' }}>
                                                            Declined
                                                        </option>
                                                    </select>
                                                    <p style="color: red">{{ $errors->first('approval_status') }}</p>
                                                </div>

                                                <div class="form-group mt-2 col-md-12">
                                                    <label for="update_balance"><strong>Require Balance
                                                            Update</strong>
                                                        <span class="text-muted fs-6">
                                                            (Automatically add the amount paid to the association's account
                                                            balance upon approval)
                                                        </span><span class="text-danger">*</span></label>
                                                    <select class="form-select" name="update_balance" required>
                                                        <option selected disabled>
                                                            -Select-</option>
                                                        <option value="1"
                                                            {{ $data['regularization']->update_balance == '1' ? 'selected' : '' }}>
                                                            Add to balance</option>
                                                        <option value="0"
                                                            {{ $data['regularization']->update_balance == '0' ? 'selected' : '' }}>
                                                            Do not Add</option>
                                                    </select>
                                                    <p style="color: red">{{ $errors->first('update_balance') }}
                                                    </p>
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="remarks">Remarks</label>
                                                    <input type="text" value="{{ $data['regularization']->remarks }}"
                                                        class="form-control" name="remarks">
                                                    <p style="color: red">{{ $errors->first('remarks') }}</p>
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
                        <a class="btn btn-primary" href="{{ route('paymentRegularizationRequests') }}">
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
