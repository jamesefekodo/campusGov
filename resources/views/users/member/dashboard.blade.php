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
                                    <p class="m-0">
                                        {{-- Login Mfa: @livewire('user-mfa-toggle', ['userId' => $data['member']->id]) --}}

                                    </p>
                                    <div class="p-1">
                                        <span style="font-weight: bold">Login Mfa:</span>
                                        @livewire('user-mfa-toggle', ['userId' => $data['member']->id])
                                    </div>
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

                                    @php
                                        $academicInfo = $data['member']->memberAcademicInfo;

                                        $academicStatus = $academicInfo?->academic_status;

                                        $statusMessage = match ($academicStatus) {
                                            'alumni' => 'You previously completed your',
                                            'withdrawn'
                                                => 'Your admission was previously withdrawn while you were enrolled in',
                                            'expelled' => 'You were previously expelled from',
                                            default => 'You were previously enrolled in',
                                        };

                                        // ##########################
                                        $outstandingDue = null;

                                        foreach ($data['member']->memberAcademicInfo->associationDues() as $due) {
                                            $transaction = $data['member']->successfulPaymentForDue($due->fee_id);

                                            if (!$transaction) {
                                                $outstandingDue = $due;
                                                break;
                                            }
                                        }
                                    @endphp

                                    @if (in_array($academicInfo?->academic_status, ['alumni', 'withdrawn', 'expelled']))
                                        <div class="alert alert-info">
                                            <h5 class="alert-heading">
                                                <i class="fas fa-user-graduate me-2"></i>
                                                Are you returning for a new program?
                                            </h5>
                                            @if ($outstandingDue)
                                                <h4 class="text-danger m-2 mt-3">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Please pay all your outstanding dues before applying for re-admission.
                                                </h4>
                                            @else
                                                <p>
                                                    @if ($academicStatus === 'alumni')
                                                        You previously completed your
                                                        <strong>{{ $academicInfo?->theProgram?->award }}</strong>
                                                        in
                                                        <strong>{{ $academicInfo?->theDepartment?->name }}</strong>
                                                        during the
                                                        <strong>
                                                            {{ $academicInfo?->graduation_session }}/{{ $academicInfo?->graduation_session + 1 }}
                                                        </strong>
                                                        academic session.
                                                    @elseif ($academicStatus === 'withdrawn')
                                                        Your admission was previously withdrawn while you were enrolled in
                                                        <strong>{{ $academicInfo?->theProgram?->award }}</strong>
                                                        in
                                                        <strong>{{ $academicInfo?->theDepartment?->name }}</strong>
                                                        during the
                                                        <strong>
                                                            {{ $academicInfo?->admission_session }}/{{ $academicInfo?->admission_session + 1 }}
                                                        </strong>
                                                        academic session.
                                                    @elseif ($academicStatus === 'expelled')
                                                        You were previously expelled from
                                                        <strong>{{ $academicInfo?->theProgram?->award }}</strong>
                                                        in
                                                        <strong>{{ $academicInfo?->theDepartment?->name }}</strong>
                                                        during the
                                                        <strong>
                                                            {{ $academicInfo?->admission_session }}/{{ $academicInfo?->admission_session + 1 }}
                                                        </strong>
                                                        academic session.
                                                    @endif
                                                </p>

                                                <p>
                                                    If you are returning to begin a new program, you may apply for
                                                    readmission.
                                                    Click the button below to submit your application. Your account will be
                                                    updated
                                                    after your application has been reviewed and approved.
                                                </p>

                                                <hr>
                                                @php
                                                    $hasNotSubmittedApplication = $data[
                                                        'member'
                                                    ]->reAdmissionRequests->contains('status', 'awaiting_submission');
                                                    $hasPendingApplication = $data[
                                                        'member'
                                                    ]->reAdmissionRequests->contains('status', 'pending');
                                                @endphp

                                                @if ($hasPendingApplication)
                                                    <p class="fs-3 text-bold text-center"> <i class="fas fa-info-circle me-2"></i>
                                                        You have a pending request that is currently under review.
                                                    </p>
                                                @else
                                                    <a href="{{ route('memberApplyForReAdmission') }}"
                                                        class="btn {{ $hasNotSubmittedApplication ? 'btn-warning' : 'btn-primary' }}">
                                                        <i class="fas fa-file-alt me-2"></i>
                                                        {{ $hasNotSubmittedApplication ? 'Continue Application' : 'Apply for Readmission' }}
                                                    </a>
                                                @endif

                                            @endif
                                        </div>
                                    @endif
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
