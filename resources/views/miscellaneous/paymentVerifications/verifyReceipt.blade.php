@extends('layouts.miscellaneousLayout')
@section('title', 'Verify Payment Receipt')
@section('content')
    <div class="container">
        <div class="col-md-12 m-auto">
            <div class="user_page_title">Verify Payment Receipt</div>

            @php
                $purposeOfPayment = 'unknown';
                // association due
                if ($data['payment']->payment_purpose == 'association_due' && $data['payment']->due) {
                    $purposeOfPayment = 'associationDue';
                }
                // donation
                if ($data['payment']->payment_purpose == 'donation' && $data['payment']->donation) {
                    $purposeOfPayment = 'donation';
                }
                // invoice
                if ($data['payment']->payment_purpose == 'invoice' && $data['payment']->invoice) {
                    $purposeOfPayment = 'invoice';
                }
                // election application
                if (
                    $data['payment']->payment_purpose == 'election_form' &&
                    $data['payment']->election &&
                    $data['payment']->electionPosition &&
                    $data['payment']->electionCandidateApplication
                ) {
                    $purposeOfPayment = 'electionApplicationForm';
                }
                // election voting
                if (
                    $data['payment']->payment_purpose == 'voting' &&
                    $data['payment']->election &&
                    $data['payment']->monetaryVote
                ) {
                    $purposeOfPayment = 'electionVoting';
                }
            @endphp

            <div class="container">
                <div class="user_page_container mb-5">
                    <div class="col-md-12 row d-flex justify-content-center align-items-center">
                        <div class="col md-5 d-flex justify-content-center align-items-center">
                            @if (
                                $data['payment']->registeredPayer &&
                                    $data['payment']->registeredPayer->photo &&
                                    file_exists(public_path(
                                            'assets/tenant_' .
                                                tenant()->id .
                                                '/uploads/users/members/photo/' .
                                                $data['payment']->registeredPayer->photo)))
                                <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['payment']->registeredPayer->photo) }}"
                                    style="max-width: 300px" alt="{{ $data['payment']->registeredPayer->username }}">
                            @else
                                <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture"
                                    style="max-width: 300px">
                            @endif
                        </div>
                        <div class="col-md-7">
                            @if ($data['payment']->registeredPayer)
                                <p class="m-0 pt-1">Name: <span style="font-weight: bold">
                                        @php
                                            $personalProfile = $data['payment']->registeredPayer->memberPersonalProfile;
                                            $academicInfo = $data['payment']->registeredPayer->memberAcademicInfo;
                                        @endphp
                                        {{ $personalProfile->first_name }} {{ $personalProfile->middle_name }}
                                        {{ $personalProfile->last_name }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Phone: <span style="font-weight: bold">
                                        {{ $personalProfile->phone1 }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Phone: <span style="font-weight: bold">
                                        {{ $personalProfile->email1 }}
                                    </span>
                                </p>
                            @else
                                <p class="m-0 pt-1">Name: <span style="font-weight: bold">
                                        {{ $data['payment']->non_registered_payer }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Phone: <span style="font-weight: bold">
                                        {{ $data['payment']->phone }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Email: <span style="font-weight: bold">
                                        {{ $data['payment']->email }}
                                    </span>
                                </p>
                            @endif
                            {{-- association due --}}
                            @if ($purposeOfPayment == 'associationDue')
                                @php
                                    $ctx = resolveAcademicContextForDue(
                                        $data['payment']->payerAcademicInfo,
                                        $data['payment']->due,
                                    );

                                    $academic = $data['payment']->registeredPayer?->memberAcademicInfo;

                                    $programLevel = studentLevelAtDue($academic, $data['payment']->due);

                                    $program = $programLevel['program'];
                                    $level = $programLevel['level'];
                                @endphp

                                <p class="m-0 pt-1">Matric No: <span style="font-weight: bold; text-transform: uppercase">
                                        {{ $ctx['matric_number'] ?? 'N/A' }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Level: <span style="font-weight: bold; text-transform: uppercase">
                                        @if ($program)
                                            {{ currentLevel($program, $level) }}
                                        @else
                                            <span class="text-danger">
                                                Previous programme is missing
                                            </span>
                                        @endif
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Program: <span style="font-weight: bold;">
                                        {{ $ctx['program'] ?? 'N/A' }}
                                        -
                                        {{ $ctx['program_type'] ?? 'N/A' }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Faculty: <span style="font-weight: bold;">
                                        {{ $ctx['faculty'] ?? 'N/A' }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Department: <span style="font-weight: bold;">
                                        {{ $ctx['department'] ?? 'N/A' }}
                                    </span>
                                </p>

                                {{-- election application --}}
                            @elseif ($purposeOfPayment == 'electionApplicationForm')
                                <p class="m-0 pt-1">Matric No: <span style="font-weight: bold; text-transform: uppercase">
                                        {{ $data['payment']->electionCandidateApplication->current_matric_no ?? $data['payment']->registeredPayer->memberAcademicInfo->matric_number }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Level: <span style="font-weight: bold; text-transform: uppercase">
                                        {{ $data['payment']->electionCandidateApplication->level_at_application }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Program: <span style="font-weight: bold;">
                                        {{ $data['payment']->electionCandidateApplication->program->name }} -
                                        {{ $data['payment']->electionCandidateApplication->programType->type_name }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Faculty: <span style="font-weight: bold;">
                                        {{ $data['payment']->electionCandidateApplication->faculty->name }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Department: <span style="font-weight: bold;">
                                        {{ $data['payment']->electionCandidateApplication->department->name }}
                                    </span>
                                </p>

                                {{--  donation + invoice + others + registered player exists --}}
                            @else
                                @php
                                    $personalProfile = $data['payment']->registeredPayer;
                                    $academicInfo = $data['payment']->payerAcademicInfo;
                                @endphp

                                <p class="m-0 pt-1">Matric No: <span style="font-weight: bold; text-transform: uppercase">
                                        {{ $academicInfo->matric_number }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Level: <span style="font-weight: bold; text-transform: uppercase">
                                        @if ($academicInfo?->academic_status == 'student')
                                            Current Level:
                                        @elseif($academicInfo?->academic_status == 'alumni')
                                            Graduated:
                                        @else
                                            {{ $academicInfo?->academic_status }}
                                        @endif
                                    </span>
                                </p>
                                @if ($academicInfo?->academic_status == 'student')

                                    {{ currentLevel($academicInfo?->theProgram, studentCurrentLevel($academicInfo)) }}
                                @elseif($academicInfo?->academic_status == 'alumni')
                                    {{ $academicInfo?->graduation_session
                                        ? $academicInfo?->graduation_session . '/' . ($academicInfo?->graduation_session + 1)
                                        : '-' }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $academicInfo?->academic_status ?? '-')) }}

                                    @if ($academicInfo?->graduation_session)
                                        ({{ $academicInfo?->graduation_session }}/{{ $academicInfo?->graduation_session + 1 }})
                                    @endif

                                @endif
                                <p class="m-0 pt-1">Program: <span style="font-weight: bold;">
                                        {{ $academicInfo->theProgram->name }}
                                        -
                                        {{ $academicInfo->programType->type_name }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Faculty: <span style="font-weight: bold;">
                                        {{ $academicInfo->theFaculty->name }}
                                    </span>
                                </p>
                                <p class="m-0 pt-1">Department: <span style="font-weight: bold;">
                                        {{ $academicInfo->theDepartment->name }}
                                    </span>
                                </p>

                            @endif

                        </div>
                    </div>
                    <hr>
                    <p style="font-weight: bold; text-decoration: underline; font-size: 14px">
                        Purpose</p>
                    <div class="col-md-12 m-2 row main_content_container">
                        {{-- association due start --}}
                        @if ($data['payment']->payment_purpose == 'association_due' && $data['payment']->due)
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Due:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['payment']->due->title }}
                                </span>
                            </div>
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Academic Session:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['payment']->due->academic_year }}/{{ $data['payment']->due->academic_year + 1 }}
                                </span>
                            </div>
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Description:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['payment']->due->description }}
                                </span>
                            </div>
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Level:</span>
                                <span class="text-black text-capitalize">
                                    @if ($program)
                                        {{ currentLevel($program, $level) }}
                                    @else
                                        <span class="text-danger">
                                            Previous programme is missing
                                        </span>
                                    @endif
                                </span>
                            </div>
                        @endif
                        {{-- association due start --}}

                        {{-- donation start --}}
                        @if ($purposeOfPayment == 'donation')
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Donation:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['payment']->donation->title }}
                                </span>
                            </div>
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">No of Contributions:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['numberOfContributions'] }}
                                </span>
                            </div>
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Description:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['payment']->donation->description }}
                                </span>
                            </div>
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Total Amount Contributed:</span>
                                <span class="text-black text-capitalize">
                                    ₦{{ number_format($data['amountHeContributed'], 2) }}
                                </span>
                            </div>
                        @endif
                        {{-- donation end --}}

                        {{-- invoice start --}}
                        @if ($data['payment']->payment_purpose == 'invoice' && $data['payment']->invoice)
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Invoice Number:</span>
                                <span class="text-black text-uppercase">
                                    {{ $data['payment']->donation->description }}
                                </span>
                            </div>
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Description:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['payment']->invoice->description }}
                                </span>
                            </div>
                        @endif
                        {{-- incoice end --}}

                        {{-- election application start --}}
                        @if (
                            $data['payment']->payment_purpose == 'election_form' &&
                                $data['payment']->election &&
                                $data['payment']->electionPosition &&
                                $data['payment']->electionCandidateApplication)
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Election:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['payment']->election->title }}
                                </span>
                            </div>
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Position Aspired:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['payment']->electionPosition->position }}
                                </span>
                            </div>
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Academic Session:</span>
                                <span class="text-black text-capitalize">
                                    {{ $data['payment']->election->academic_session }}/{{ $data['payment']->election->academic_session + 1 }}
                                </span>
                            </div>
                        @endif
                        {{-- election application end --}}

                        {{-- election voting start --}}
                        @if ($purposeOfPayment == 'electionVoting')
                            <div class="col-md-6 p-1">
                                <span style="font-weight: bold">Voting:</span>
                                <span class="text-black text-capitalize">
                                    @if ($data['payment']->monetaryVote)
                                        {{ $data['payment']->monetaryVote->no_of_votes }} votes for
                                        {{ $data['payment']->monetaryVote->thePosition }} —
                                        {{ $data['payment']->election->title }}
                                        ({{ $data['payment']->election->academic_session }}/{{ $data['payment']->election->academic_session + 1 }})
                                    @endif
                                </span>
                            </div>
                        @endif
                        {{-- election voting ends --}}

                        <hr>
                        {{-- transaction start --}}
                        <p style="font-weight: bold; text-decoration: underline; font-size: 14px">
                            Transaction Details</p>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Transaction ID:</span>
                            <span class="text-black text-uppercase">
                                {{ $data['payment']->payment_transaction_id }}
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Payment Mode/Method:</span>
                            <span class="text-black">
                                {{ $data['payment']->payment_mode }} / @if ($data['payment']->payment_mode == 'manual')
                                    @php
                                        $methods = [
                                            'cash' => 'Cash',
                                            'bank_transfer' => 'Bank Transfer/Deposit',
                                            'cheque' => 'Cheque',
                                        ];
                                    @endphp

                                    {{ $methods[$data['payment']->manual_payment_method] ?? ucfirst(str_replace('_', ' ', $data['payment']->manual_payment_method)) }}
                                @endif
                                @if ($data['payment']->payment_mode == 'electronic')
                                    <span class="text-capitalize">
                                        {{ $data['payment']->electronic_payment_method }}
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Date:</span>
                            <span class="text-black text-capitalize">
                                {{ \Carbon\Carbon::parse($data['payment']->paid_at)->format('l, d F, Y h:i A') }}
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Payment Type:</span>
                            <span class="text-black text-capitalize">
                                {{ $data['payment']->payment_type }}
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Amount Paid:</span>
                            <span class="text-black text-capitalize">
                                ₦{{ number_format($data['payment']->amount_payable, 2) }}
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Status:</span>
                            <span class="text-black text-capitalize">
                                {{ $data['payment']->payment_status }}
                                /
                                {{ $data['payment']->approval_status }}
                            </span>
                        </div>

                        
                        <br>
                        <div class="d-flex justify-content-center my-5">
                            <a href="{{ route('homepage') }}
                             " class="btn btn-success mx-3">
                                Home
                                <i class="fas fa-home"></i>
                            </a>
                        </div>

                    </div>


                </div>

            </div>

        </div>
    </div>
@endsection

