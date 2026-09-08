<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}"> --}}
    <link rel="icon" type="image/x-icon" href="{{ base64Image(associationLogo()) }}">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @if ($data['payment']->due)
        @php
            $academic = $data['payment']->registeredPayer?->memberAcademicInfo;

            $programLevel = studentLevelAtDue($academic, $data['payment']->due);

            $program = $programLevel['program'];
            $level = $programLevel['level'];
        @endphp
    @endif

    <title>Payment Transaction Receipt
    </title>
</head>
@php
    $primaryColour = $data['basicSystemInfo']->primary_colour ?? '#3b5edd';
    $secondaryColour = $data['basicSystemInfo']->secondary_colour ?? '#7a97ff';
    $tertiaryColour = $data['basicSystemInfo']->tertiary_colour ?? basicSystemInfo()['secondary_colour'];

@endphp


<body>
    <div class="background-container">
        <div class="content">
            <div class="header">
                <div class="container">
                    <div class="d-flex justify-content-center align-items-center mb-1">
                        <table style="">
                            <tbody>
                                <tr>
                                    <td style="width: 60px">
                                        <img src="{{ base64Image(associationLogo()) }}" alt="" width="60px">
                                    </td>
                                    <td
                                        style="text-align: center; display: flex; justify-content: center; text-transform:uppercase; font-weight: bold; color: {{ $primaryColour }}; font-size:1.3rem;">
                                        {{ basicSystemInfo()->association_name }} @if (basicSystemInfo()->association_acronym)
                                            {{ basicSystemInfo()->association_acronym }}
                                            @endif - @if (basicSystemInfo()->school_acronym)
                                                {{ basicSystemInfo()->school_acronym }} Chapter
                                            @endif
                                    </td>

                                    <td style="width: 50px">
                                        <img src="{{ base64Image(schoolLogo()) }}" alt="" width="50px">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- ######################### --}}
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
                    {{-- ##################### --}}
                    <p style="text-transform: uppercase; font-weight: bold; color: red; margin: 10px; font-size: 16px;">
                        Payment Transaction Receipt <br>
                        {{-- association dues --}}
                        @if ($purposeOfPayment == 'associationDue')
                            <span style="color: black; font-size: 18px">
                                {{ $data['payment']->due->title }}
                                {{ $data['payment']->due->academic_year }}/{{ $data['payment']->due->academic_year + 1 }}
                                :: @if ($program)
                                    {{ currentLevel($program, $level) }}
                                @endif
                            </span>
                        @endif
                        {{-- donation --}}
                        @if ($purposeOfPayment == 'donation')
                            <span style="color: black; font-size: 18px">
                                {{ $data['payment']->donation->title }}
                            </span>
                        @endif
                        {{-- invoice --}}
                        @if ($purposeOfPayment == 'invoice')
                            <span style="color: black; font-size: 18px">
                                {{ Str::words($data['payment']->invoice->description, 5, '...') }}
                            </span>
                        @endif
                        {{-- election_form --}}
                        @if ($purposeOfPayment == 'electionApplicationForm')
                            <span style="color: black; font-size: 18px">
                                {{ $data['payment']->election->title }} :: Application Form
                            </span>
                        @endif
                        {{-- voting --}}
                        @if ($purposeOfPayment == 'electionVoting')
                            <span style="color: black; font-size: 18px">
                                {{ $data['payment']->election->title }} :: Monetary Votes
                            </span>
                        @endif

                    </p>
                </div>
            </div>
            <p class="m-0 p-0"><span style="font-weight: bold">Date Printed:</span>
                {{ now('Africa/Lagos')->format('l, d F, Y h:i:s A') }}</p>

            <table style="margin: 5px; width: 100%; margin-bottom: 10px">
                <thead class="justify-content-center text-center align-middle">
                    <tr style="border: 1px solid white; justify-content: center; text-align: center;">
                        <th scope="col" colspan="2"
                            style="background: {{ $primaryColour }}; padding: 10px; margin: 10px; color: white; border: 1px solid white; text-align: center;">
                            PAYER INFO
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border: 0px; border-bottom:none; padding:10px">
                        <td>
                            @php
                                $photoPath = public_path(
                                    'assets/tenant_' .
                                        $data['tenant']->id .
                                        '/uploads/users/members/photo/' .
                                        $data['payment']->registeredPayer->photo,
                                );
                            @endphp

                            @if ($data['payment']->registeredPayer->photo && file_exists($photoPath))
                                <img src="{{ $photoPath }}" alt="Candidate Photo" width="120px">
                            @else
                                <img src="{{ public_path('system/images/default_profile_pic.webp') }}"
                                    alt="Default Picture" width="120px">
                            @endif
                        </td>
                        <td>
                            @if ($data['payment']->registeredPayer)
                                <span style="font-weight: bold">Name: </span>
                                <span class="">
                                    @php
                                        $personalProfile = $data['payment']->registeredPayer->memberPersonalProfile;
                                        $academicInfo = $data['payment']->registeredPayer->memberAcademicInfo;
                                    @endphp
                                    {{ $personalProfile->first_name }} {{ $personalProfile->middle_name }}
                                    {{ $personalProfile->last_name }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Phone: </span>
                                <span class="text-capitalize">
                                    {{ $personalProfile->phone1 }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Email: </span>
                                <span class="">
                                    {{ $personalProfile->email1 }}
                                </span>
                                <br>
                            @else
                                <span style="font-weight: bold">Name: </span>
                                <span class="">
                                    {{ $data['non_registered_payer'] }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Phone: </span>
                                <span class="text-capitalize">
                                    {{ $data['phone'] }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Email: </span>
                                <span class="">
                                    {{ $data['email'] }}
                                </span>
                                <br>
                            @endif

                            {{-- association due --}}
                            @if ($purposeOfPayment == 'associationDue')
                                @php
                                    $ctx = resolveAcademicContextForDue(
                                        $data['payment']->payerAcademicInfo,
                                        $data['payment']->due,
                                    );
                                @endphp

                                <span style="font-weight: bold">Matric No.: </span>
                                <span class="text-uppercase">
                                    {{ $ctx['matric_number'] ?? 'N/A' }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Level: </span>
                                <span class="">
                                    @if ($program)
                                        {{ currentLevel($program, $level) }}
                                    @else
                                        <span class="text-danger">
                                            Previous programme is missing
                                        </span>
                                    @endif
                                </span>
                                <br>
                                <span style="font-weight: bold">Program: </span>
                                <span class="text-capitalize">
                                    {{ $ctx['program'] ?? 'N/A' }}
                                    -
                                    {{ $ctx['program_type'] ?? 'N/A' }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Faculty: </span>
                                <span class="text-capitalize">
                                    {{ $ctx['faculty'] ?? 'N/A' }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Department: </span>
                                <span class="text-capitalize">
                                    {{ $ctx['department'] ?? 'N/A' }}
                                </span>
                                <br>
                                {{-- election application --}}
                            @elseif ($purposeOfPayment == 'electionApplicationForm')
                                <span style="font-weight: bold">Matric No.: </span>
                                <span class="text-uppercase">
                                    {{ $data['payment']->electionCandidateApplication->current_matric_no ?? $data['payment']->registeredPayer->memberAcademicInfo->matric_number }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Level: </span>
                                <span class="">
                                    {{ $data['payment']->electionCandidateApplication->level_at_application }}
                                    <br>
                                    <span style="font-weight: bold">Program: </span>
                                    <span class="">
                                        {{ $data['payment']->electionCandidateApplication->program->name }} -
                                        {{ $data['payment']->electionCandidateApplication->programType->type_name }}
                                    </span>
                                    <br>
                                    <span style="font-weight: bold">Faculty: </span>
                                    <span class="text-capitalize">
                                        {{ $data['payment']->electionCandidateApplication->faculty->name }}
                                    </span>
                                    <br>

                                    <span style="font-weight: bold">Department: </span>
                                    <span class="text-capitalize">
                                        {{ $data['payment']->electionCandidateApplication->department->name }}
                                    </span>
                                    <br>
                                </span>

                                {{-- donation + invoice + others + registered player exists ... --}}
                            @else
                                @php
                                    $personalProfile = $data['payment']->registeredPayer;
                                    $academicInfo = $data['payment']->payerAcademicInfo;
                                @endphp
                                <span style="font-weight: bold">Matric No.: </span>
                                <span class="text-uppercase">
                                    {{ $academicInfo->matric_number }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Level: </span>
                                <span class="">
                                    <span style="font-weight: bold">
                                        @if ($academicInfo?->academic_status == 'student')
                                            {{-- Current Level: --}}
                                        @elseif($academicInfo?->academic_status == 'alumni')
                                            Graduated:
                                        @else
                                            {{ $academicInfo?->academic_status }}
                                        @endif
                                    </span>

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
                                </span>
                                <br>
                                <span style="font-weight: bold">Program: </span>
                                <span class="text-capitalize">
                                    {{ $academicInfo->theProgram->name }}
                                    -
                                    {{ $academicInfo->programType->type_name }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Faculty: </span>
                                <span class="text-capitalize">
                                    {{ $academicInfo->theFaculty->name }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Department: </span>
                                <span class="text-capitalize">
                                    {{ $academicInfo->theDepartment->name }}
                                </span>
                                <br>
                            @endif


                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="m-4"></td>
                    </tr>

                </tbody>
            </table>
            {{-- ################################################################# --}}
            <table style="margin-top: 5px; width: 100%">
                <thead class="justify-content-center text-center align-middle">
                    <tr style="border: 1px solid white; justify-content: center; text-align: center;">
                        <th scope="col" colspan="2"
                            style="background: {{ $primaryColour }}; padding: 10px; margin: 10px; color: white; border: 1px solid white; text-align: center;">
                            {{-- TRANSACTION DETAILS --}}
                            PAYMENT TRANSACTION
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <br>
                    <tr>
                        <td colspan="2" style="width:100%">
                            <p
                                style="color: {{ $secondaryColour }}; font-weight: bold; text-decoration: underline; font-size: 14px">
                                Purpose</p>
                        </td>
                    </tr>
                    {{-- association due start --}}
                    @if ($data['payment']->payment_purpose == 'association_due' && $data['payment']->due)
                        <tr style="border: 0px; border-bottom:none; padding:10px">
                            <td>
                                <span style="font-weight: bold">Due: </span>
                                <span class="">
                                    {{ $data['payment']->due->title }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Academic Session: </span>
                                <span class="">
                                    {{ $data['payment']->due->academic_year }}/{{ $data['payment']->due->academic_year + 1 }}
                                </span>
                                <br>
                            </td>
                            <td>
                                <span style="font-weight: bold">Description: </span>
                                <span class="">
                                    {{ $data['payment']->due->description }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Level: </span>
                                <span class="">
                                    @if ($program)
                                        {{ currentLevel($program, $level) }}
                                    @else
                                        <span class="text-danger">
                                            Previous programme is missing
                                        </span>
                                    @endif
                                </span>
                                <br>
                            </td>
                        </tr>
                    @endif
                    {{-- association due end --}}
                    {{-- donation start --}}
                    @if ($purposeOfPayment == 'donation')
                        <tr style="border: 0px; border-bottom:none; padding:10px">
                            <td>
                                <span style="font-weight: bold">Donation: </span>
                                <span class="">
                                    {{ $data['payment']->donation->title }}
                                </span>
                                <br>
                                <span style="font-weight: bold">No of Contributions: </span>
                                <span class="">

                                    {{ $data['numberOfContributions'] }}
                                </span>
                                <br>
                            </td>
                            <td>
                                <span style="font-weight: bold">Description: </span>
                                <span class="">
                                    {{ $data['payment']->donation->description }}
                                </span>
                                <br>
                                <span style="font-weight: bold">Total Amount Contributed: </span>
                                <span class="">

                                    ₦{{ number_format($data['amountHeContributed'], 2) }}
                                </span>
                                <br>
                            </td>
                        </tr>
                    @endif
                    {{-- donation end --}}

                    {{-- invoice start --}}
                    @if ($data['payment']->payment_purpose == 'invoice' && $data['payment']->invoice)
                        <tr style="border: 0px; border-bottom:none; padding:10px">
                            <td>
                                <span style="font-weight: bold;">Invoice Number: </span>
                                <span style=" text-transform: uppercase">
                                    {{ $data['payment']->invoice->invoice_number }}
                                </span>
                                <br>
                            </td>
                            <td>
                                <span style="font-weight: bold">Description: </span>
                                <span class="">
                                    {{ $data['payment']->invoice->description }}
                                </span>
                                <br>
                            </td>
                        </tr>
                    @endif
                    {{-- invoice end --}}

                    {{-- election candidate application start --}}
                    @if (
                        $data['payment']->payment_purpose == 'election_form' &&
                            $data['payment']->election &&
                            $data['payment']->electionPosition &&
                            $data['payment']->electionCandidateApplication)
                        <tr style="border: 0px; border-bottom:none; padding:10px">
                            <td>
                                <span style="font-weight: bold;">Election: </span>
                                <span class="">
                                    {{ $data['payment']->election->title }}
                                </span>
                                <br>
                                <span style="font-weight: bold;">Position Aspired: </span>
                                <span class="">
                                    {{ $data['payment']->electionPosition->position }}
                                </span>
                                <br>
                            </td>
                            <td>
                                <span style="font-weight: bold">Academic Session: </span>
                                <span class="">
                                    {{ $data['payment']->election->academic_session }}/{{ $data['payment']->election->academic_session + 1 }}
                                </span>
                                <br>
                            </td>
                        </tr>
                    @endif
                    {{-- election candidate application end --}}

                    {{-- election voting start --}}
                    @if ($purposeOfPayment == 'electionVoting')
                        <tr style="border: 0px; border-bottom:none; padding:10px">
                            <td>
                                <span style="font-weight: bold">Voting: </span>
                                <span class="">
                                    @if ($data['payment']->monetaryVote)
                                        {{ $data['payment']->monetaryVote->no_of_votes }} votes for
                                        {{ $data['payment']->monetaryVote->thePosition }} —
                                        {{ $data['payment']->election->title }}
                                        ({{ $data['payment']->election->academic_session }}/{{ $data['payment']->election->academic_session + 1 }})
                                    @endif
                                </span>
                                <br>
                            </td>
                        </tr>

                    @endif
                    {{-- election voting ends --}}

                    {{-- transaction start --}}
                    <tr>
                        <td colspan="2" style="width:100%">
                            <p
                                style="color: {{ $secondaryColour }}; font-weight: bold; text-decoration: underline; font-size: 14px">
                                Transaction Details</p>
                        </td>
                    </tr>
                    <tr style="border: 0px; border-bottom:none; padding:10px">
                        <td style="width: 50%">
                            <span style="font-weight: bold">Transaction ID: </span>
                            <span class="" style="text-transform:uppercase">
                                {{ $data['payment']->payment_transaction_id }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Payment Mode/Method: </span>
                            <span class="" style="text-transform: capitalize">
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
                            <br>
                            <span style="font-weight: bold">Date: </span>
                            <span class="" style="text-transform: capitalize">
                                {{ \Carbon\Carbon::parse($data['payment']->paid_at)->format('l, d F, Y h:i A') }}
                            </span>
                            <br>
                        </td>
                        <td style="width: 50%">
                            <span style="font-weight: bold">Payment Type: </span>
                            <span class="" style="text-transform: capitalize">
                                {{ $data['payment']->payment_type }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Amount Paid: </span>
                            <span class="" style="text-transform: capitalize">
                                ₦{{ number_format($data['payment']->amount_payable, 2) }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Status: </span>
                            <span class="" style="text-transform: capitalize">
                                {{ $data['payment']->payment_status }}
                                /
                                {{ $data['payment']->approval_status }}
                            </span>
                            <br>
                        </td>
                    </tr>

                    @if ($data['payment']->payment_purpose == 'donation' && $data['payment']->donation)
                        <tr>
                            <td colspan="2" style="font-size: 18px; font-weight: bold; text-align: center;">
                                <br>
                                <span style="color: #198754;">
                                    Thank you so much for your generous support & contribution. We truly appreciate
                                    your kindness and generosity!
                                </span>
                            </td>
                        </tr>
                    @endif
                    {{-- transactions end --}}




                    <tr>
                        <td colspan="2" class="m-4"></td>
                    </tr>

                </tbody>

            </table>
            {{-- Electoral Committee --}}
            <table style="margin-top: 5px; width: 100%">
                <thead class="justify-content-center text-center align-middle">
                    <tr style="border: 1px solid white; justify-content: center; text-align: center;">
                        <th scope="col" colspan="2">
                            {{-- ASSOCIATION DUE / WAIVER --}}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <br>
                    <tr style="border: 0px; border-bottom:none; padding:10px">
                        <td style="text-align: center; vertical-align: middle;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('verifyPaymentReceipt', $data['payment']->payment_transaction_id)) }}"
                                alt="QR Code" width="140" height="140">
                            <br>
                            <span style="font-size: 10px; color: #555;">Scan to Verify</span>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <br><br><br><br>
                            <span style="color: grey;">Remarks, name, stamp, signature & date</span>
                            <br>
                            __________________________________________
                            <br>
                            <span style="font-weight: bold">For:
                                @if (basicSystemInfo()->association_acronym)
                                    {{ basicSystemInfo()->association_acronym }}
                                @else
                                    Association
                                    @endif - @if (basicSystemInfo()->school_acronym)
                                        {{ basicSystemInfo()->school_acronym }} Chapter
                                    @endif
                            </span>
                        </td>
                    </tr>


                    <tr>
                        <td colspan="2" class="m-4"></td>
                    </tr>

                </tbody>

            </table>



        </div>

    </div>


</body>

</html>



<style>
    body {
        margin: 0;
        padding: 0;
    }


    .content {
        padding-left: 30px;
        padding-right: 30px;
        font-size: 14px;
        /* margin-bottom: 50px; */
    }

    .header {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        /* Set an appropriate height for vertical centering */
    }

    .container {
        text-align: center;
    }
</style>
