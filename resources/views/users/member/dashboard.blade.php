@extends('layouts.userLayout')
@section('title', 'Dashboard')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <h1 class="app-page-title mb-0">Dashboard</h1>
            <div class="mb-4">
                <div class="individual-review-card-container">
                    <div class="col-md-3 individual-review-card text-black mx-2">

                        {{-- includes --}}
                        @if (
                            $data['member']->photo &&
                                file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['member']->photo)))
                            <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['member']->photo) }}"
                                alt="{{ $data['member']->username }}" class="img-fluid w-100">
                        @else
                            <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture"
                                class="img-fluid w-100">
                        @endif
                        <h2 class="individual-review-card-header text-center text-capitalize">
                            @php
                                $personal = $data['member']->memberPersonalProfile;
                                $middleInitial = $personal?->middle_name
                                    ? strtoupper(substr($personal->middle_name, 0, 1)) . '. '
                                    : '';
                            @endphp

                            {{ ($personal?->first_name ?? $data['member']->username) . ' ' . $middleInitial . ($personal?->last_name ?? '') }}
                        </h2>
                        <p class="m-0"><span style="font-weight: bold">Matric No:</span>
                            {{ $data['member']->memberAcademicInfo->matric_number }}
                        </p>
                        <p class="m-0"><span style="font-weight: bold">Department:</span>
                            {{ $data['member']->memberAcademicInfo->theDepartment->name }}
                        </p>
                        <p class="m-0"><span style="font-weight: bold">Program:</span>
                            {{ $data['member']->memberAcademicInfo->theProgram?->name }} @if ($data['member']->memberAcademicInfo->theProgram?->short_name)
                                ({{ $data['member']->memberAcademicInfo->theProgram?->short_name }})
                            @endif
                        </p>
                        <p class="m-0"><span style="font-weight: bold">Level:</span>
                            <span style="font-weight: bold">
                                @php
                                    $academic = $data['member']->memberAcademicInfo;
                                @endphp

                                <span style="font-weight: bold">
                                    @if ($academic?->academic_status == 'student')
                                        {{-- Current Level: --}}
                                    @elseif($academic?->academic_status == 'alumni')
                                        Graduated:
                                    @else
                                        {{ $academic?->academic_status }}
                                    @endif
                                </span>

                                @if ($academic?->academic_status == 'student')

                                    {{ currentLevel($academic->theProgram, studentCurrentLevel($academic)) }}
                                @elseif($academic?->academic_status == 'alumni')
                                    {{ $academic->graduation_session
                                        ? $academic->graduation_session . '/' . ($academic->graduation_session + 1)
                                        : '-' }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $academic?->academic_status ?? '-')) }}

                                    @if ($academic?->graduation_session)
                                        ({{ $academic->graduation_session }}/{{ $academic->graduation_session + 1 }})
                                    @endif

                                @endif
                            </span>
                        </p>

                        <p class="text-capitalize"><span style="font-weight: bold">Account Status:
                            </span>
                            @switch($data['member']->account_status)
                                @case('0')
                                    <span class="badge bg-yellow text-black">
                                        Unverified
                                    </span>
                                @break

                                @case('1')
                                    <span class="badge bg-blue text-white">
                                        Awaiting Verification
                                    </span>
                                @break

                                @case('2')
                                    <span class="badge bg-green text-white">
                                        Active
                                    </span>
                                @break

                                @case('3')
                                    <span class="badge bg-red text-white">
                                        Disabled
                                    </span>
                                @break

                                @default
                                    <span class="badge bg-red text-white">
                                        Unknown
                                    </span>
                            @endswitch
                        </p>
                    </div>
                    <div class="col-md-9 individual-review-card">
                        <div class="my_form_container">
                            <h4 class="block text-center p-1 type-b-page-header">
                                {{ $data['member']->memberPersonalProfile->first_name ?? '-' }}
                                {{ $data['member']->memberPersonalProfile->middle_name ?? '-' }}
                                {{ $data['member']->memberPersonalProfile->last_name ?? '-' }}
                            </h4>
                            <div class="row m-3 pb-5">
                                <h5 class="font-weight-bold text-decoration-underline">Basic Info</h5>

                                <div class="col-md-6">
                                    <p class="m-0">
                                        First Name: <span
                                            class="text-black">{{ $data['member']->memberPersonalProfile->first_name }}</span>
                                    </p>
                                    <p class="m-0">
                                        Middle Name: <span
                                            class="text-black">{{ $data['member']->memberPersonalProfile->middle_name }}</span>
                                    </p>
                                    <p class="m-0">
                                        Last Name: <span
                                            class="text-black">{{ $data['member']->memberPersonalProfile->last_name }}</span>
                                    </p>
                                    <p class="m-0">
                                        Phone: <span class="text-black">
                                            {{ $data['member']->memberPersonalProfile->phone1 }}
                                            @if ($data['member']->memberPersonalProfile->phone2)
                                                /{{ $data['member']->memberPersonalProfile->phone2 }}
                                            @endif
                                        </span>
                                    </p>
                                    <p class="m-0">
                                        Email: <span class="text-black">
                                            {{ $data['member']->memberPersonalProfile->email1 }}
                                            @if ($data['member']->memberPersonalProfile->email2)
                                                /{{ $data['member']->memberPersonalProfile->email2 }}
                                            @endif
                                        </span>
                                    </p>


                                </div>
                                <div class="col-md-6">
                                    <p class="m-0">
                                        Gender: <span class="text-black">
                                            @switch($data['member']->memberPersonalProfile->gender)
                                                @case('male')
                                                    Male
                                                @break

                                                @case('female')
                                                    Female
                                                @break

                                                @default
                                            @endswitch
                                        </span>
                                    </p>
                                    <p class="m-0">
                                        DOB: <span
                                            class="text-black">{{ \Carbon\Carbon::parse($data['member']->memberPersonalProfile->dob)->format('l, d F, Y') }}</span>
                                    </p>

                                    <p class="m-0">
                                        Nigerian: <span
                                            class="text-black">{{ $data['member']->memberPersonalProfile->nationality }}</span>
                                    </p>
                                    <p class="m-0">
                                        State of Origin: <span
                                            class="text-black">{{ $data['member']->memberPersonalProfile->state_of_origin }}</span>
                                    </p>
                                    <p class="m-0">
                                        L.G.A of Origin: <span
                                            class="text-black">{{ $data['member']->memberPersonalProfile->lga_of_origin }}</span>
                                    </p>
                                </div>
                                <div class="col-md-12">
                                    <p class="m-0">
                                        Address 1: <span class="text-black">
                                            {{ $data['member']->memberPersonalProfile->address1 }}
                                        </span>
                                    </p>
                                    @if ($data['member']->memberPersonalProfile->address2)
                                        <p class="m-0">
                                            Address 2: <span class="text-black">
                                                {{ $data['member']->memberPersonalProfile->address2 }}
                                            </span>
                                        </p>
                                    @endif
                                    <p class="m-0">
                                        Email Verification: <span class="text-black">
                                            @switch($data['member']->email_verification)
                                                @case('verified')
                                                    Verified &nbsp;<span
                                                        class="badge bg-blue text-white">at</span>&nbsp;{{ \Carbon\Carbon::parse($data['member']->email_verified_at)->format('l, d F, Y h:i A') }}.
                                                @break

                                                @default
                                                    Not Verified
                                            @endswitch
                                        </span>
                                    </p>

                                </div>
                                <hr class="m-3">
                                <h5 class="font-weight-bold text-decoration-underline">Academic Info</h5>
                                <div class="row col-md-12">
                                    <div class="col-md-6">
                                        <p class="m-1">
                                            Academic Status: <span class="text-black">
                                                @switch($data['member']->memberAcademicInfo->academic_status)
                                                    @case('student')
                                                        Student
                                                    @break

                                                    @case('extra_year')
                                                        Extra Year
                                                    @break

                                                    @case('alumni')
                                                        Alumni
                                                    @break

                                                    @case('withdrawn')
                                                        Withdrawn
                                                    @break

                                                    @case('suspended')
                                                        Suspended
                                                    @break

                                                    @case('expelled')
                                                        Expelled
                                                    @break

                                                    @default
                                                        Unknown
                                                @endswitch
                                            </span>
                                        </p>
                                        <p class="m-1">
                                            Matric Number: <span class="text-black">
                                                {{ $data['member']->memberAcademicInfo->matric_number }}
                                            </span>
                                        </p>
                                        <p class="m-1">
                                            Level: <span class="text-black">
                                                @php
                                                    $academic = $data['member']->memberAcademicInfo;
                                                @endphp

                                                <span style="font-weight: bold">
                                                    @if ($academic?->academic_status == 'student')
                                                        {{-- Current Level: --}}
                                                    @elseif($academic?->academic_status == 'alumni')
                                                        Graduated:
                                                    @else
                                                        {{ $academic?->academic_status }}
                                                    @endif
                                                </span>

                                                @if ($academic?->academic_status == 'student')

                                                    {{ currentLevel($academic->theProgram, studentCurrentLevel($academic)) }}
                                                @elseif($academic?->academic_status == 'alumni')
                                                    {{ $academic->graduation_session
                                                        ? $academic->graduation_session . '/' . ($academic->graduation_session + 1)
                                                        : '-' }}
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $academic?->academic_status ?? '-')) }}

                                                    @if ($academic?->graduation_session)
                                                        ({{ $academic->graduation_session }}/{{ $academic->graduation_session + 1 }})
                                                    @endif

                                                @endif
                                            </span>
                                        </p>
                                        <p class="m-1">
                                            Department: <span class="text-black">
                                                {{ $data['member']->memberAcademicInfo->theDepartment->name }}
                                            </span>
                                        </p>
                                        @if ($data['member']->memberAcademicInfo->graduation_session)
                                            <p class="m-1">
                                                Graduation Session: <span class="text-black">
                                                    {{ $data['member']->memberAcademicInfo->graduation_session }}/{{ $data['member']->memberAcademicInfo->graduation_session + 1 }}
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <p class="m-1">
                                            Program: <span class="text-black">
                                                {{ $data['member']->memberAcademicInfo->theProgram->name }}
                                                @if ($data['member']->memberAcademicInfo->theProgram->short_name)
                                                    ({{ $data['member']->memberAcademicInfo->theProgram->short_name }})
                                                @endif
                                                - {{ $data['member']->memberAcademicInfo->programType->type_name }}
                                            </span>
                                        </p>
                                        <p class="m-1">
                                            Program Duration: <span class="text-black">
                                                @php
                                                    $academic = $data['member']->memberAcademicInfo;
                                                @endphp

                                                {{ getProgramDuration($academic->theProgram, $academic->programType, $academic->theDepartment) }}
                                                Years
                                            </span>
                                        </p>
                                        <p class="m-1">
                                            Faculty: <span class="text-black">
                                                {{ $data['member']->memberAcademicInfo->theFaculty->name }}
                                                @if ($data['member']->memberAcademicInfo->theFaculty->acronym)
                                                    ({{ $data['member']->memberAcademicInfo->theFaculty->acronym }})
                                                @endif
                                            </span>
                                        </p>
                                        <p class="m-1">
                                            Admission Session: <span class="text-black">
                                                {{ $data['member']->memberAcademicInfo->admission_session }}/{{ $data['member']->memberAcademicInfo->admission_session + 1 }}
                                            </span>
                                        </p>

                                    </div>
                                </div>
                                <div class="col-md-12">

                                    <hr class="m-3">
                                    <h5 class="font-weight-bold text-decoration-underline">Association Dues</h5>
                                    <div class="card-body p-1 row col-md-12">
                                        @forelse($data['member']->memberAcademicInfo->associationDues() as $due)
                                            <div class="col-md-12 mb-4">
                                                <div class="card shadow-sm border-0">

                                                    {{-- Header --}}
                                                    <div
                                                        class="card-header bg-blue text-white d-flex justify-content-between align-items-center">
                                                        <h5 class="mb-0 text-white">
                                                            {{ $due->title }}
                                                        </h5>

                                                        <span class="badge bg-light text-dark">
                                                            {{ $due->academic_year }}/{{ $due->academic_year + 1 }}
                                                        </span>
                                                    </div>

                                                    {{-- Body --}}
                                                    <div class="card-body">

                                                        @if ($due->description)
                                                            <p class="text-muted mb-4">
                                                                {{ $due->description }}
                                                            </p>
                                                        @endif

                                                        <div class="row">

                                                            <div class="col-md-4 mb-3">
                                                                <small class="text-muted d-block">Amount Payable</small>
                                                                <h4 class="mb-0 text-success">
                                                                    ₦{{ number_format($due->payable_amount, 2) }}
                                                                </h4>
                                                            </div>

                                                            {{-- <div class="col-md-4 mb-3">
                                                                <small class="text-muted d-block">Default Amount</small>
                                                                ₦{{ number_format($due->amount, 2) }}
                                                            </div> --}}

                                                            <div class="col-md-4 mb-3">
                                                                <small class="text-muted d-block">Variation Applied</small>

                                                                @if ($due->matchedVariation)
                                                                    <span class="badge bg-success">
                                                                        Yes
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-secondary">
                                                                        No
                                                                    </span>
                                                                @endif
                                                            </div>

                                                        </div>

                                                        @if ($due->matchedVariation)
                                                            <hr>

                                                            <h6 class="mb-3">
                                                                Applied Variation
                                                            </h6>

                                                            <div class="row">

                                                                <div class="col-md-4">
                                                                    <strong>Admission Session</strong><br>

                                                                    @if ($due->matchedVariation?->admission_session)
                                                                        {{ $due->matchedVariation->admission_session }}/{{ $due->matchedVariation->admission_session + 1 }}

                                                                        @php
                                                                            $year =
                                                                                $due->academic_year -
                                                                                $due->matchedVariation
                                                                                    ->admission_session +
                                                                                1;
                                                                        @endphp

                                                                        -
                                                                        @if ($year == 1)
                                                                            (Freshers)
                                                                        @else
                                                                            (Year {{ $year }})
                                                                        @endif
                                                                    @else
                                                                        All
                                                                    @endif
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <strong>Program</strong><br>

                                                                    {{ $due->matchedVariation->program?->name ?? 'All' }}
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <strong>Program Type</strong><br>

                                                                    {{ $due->matchedVariation->programType?->type_name ?? 'All' }}
                                                                </div>

                                                            </div>
                                                        @endif

                                                        <hr>


                                                        @if ($transaction = $data['member']->successfulPaymentForDue($due->fee_id))
                                                            <div class="d-flex justify-content-end align-items-center">
                                                                <a {{-- href="{{ route('printPaymentReceipt', $transaction->uuid) }}" --}} class="btn btn-success">
                                                                    <i class="fas fa-print me-1"></i>
                                                                    Print Receipt
                                                                </a>
                                                            </div>
                                                        @elseif ($data['member']->pendingPaymentForDue($due->fee_id))
                                                            <div class="d-flex justify-content-end align-items-center">

                                                                <button type="button" class="btn btn-warning" disabled>
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    Pending Approval
                                                                </button>

                                                                {{-- refresh status start --}}
                                                                @php
                                                                    $transaction = $data[
                                                                        'member'
                                                                    ]->pendingPaymentForDue($due->fee_id);
                                                                @endphp
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
                                                                                    class="border-0 bg-transparent p-0 fs-3"><i
                                                                                        class="fa-solid fa-rotate-right text-danger"></i></button>
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
                                                                                    class="border-0 bg-transparent p-0 fs-3"><i
                                                                                        class="fa-solid fa-rotate-right text-danger"></i></button>
                                                                            </form>
                                                                        @break

                                                                        @default
                                                                    @endswitch
                                                                @endif
                                                                {{-- refresh status end --}}
                                                            </div>
                                                        @else
                                                            {{-- Pay Now --}}
                                                            @php
                                                                $collapseId = 'payNowSection-' . $due->id;
                                                            @endphp

                                                            @include('users.member.dues.include.payDueNowModal')
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>

                                            @empty

                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body text-center py-5">
                                                            <p class="text-muted mb-0">
                                                                There are no dues applicable to your membership period. </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                        {{-- <div class="col-md-12">
                                            @if ($data['facultyAssociationMemberships']->count() > 0)
                                                @foreach ($data['facultyAssociationMemberships'] as $facultyAssociation)
                                                    <p class="text-black"> * {{ $facultyAssociation->name }}
                                                        ({{ $facultyAssociation->acronym }})
                                                        <a href="{{ route('studentViewAssociation', ['username' => $facultyAssociation->username]) }}"
                                                            class="btn btn-primary btn-sm ms-2">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </p>
                                                @endforeach
                                            @endif
                                            @if ($data['departmentalAssociationMemberships']->count() > 0)
                                                @foreach ($data['departmentalAssociationMemberships'] as $departmentAssociation)
                                                    <p class="text-black">
                                                        * {{ $departmentAssociation->name }}
                                                        ({{ $departmentAssociation->acronym }})
                                                        <a href="{{ route('studentViewAssociation', ['username' => $departmentAssociation->username]) }}" class="btn btn-primary btn-sm ms-2">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </p>
                                                @endforeach
                                            @endif
                                        </div> --}}
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>


                </div>


                {{-- modals starts here --}}
                {{-- @include('users.students.profile.include.updateAcademicLevelModal') --}}
                {{-- modals ends here --}}

            </div><!--//container-fluid-->
        </div><!--//app-content-->
    @endsection
