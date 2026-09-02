<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}"> --}}
    <link rel="icon" type="image/x-icon" href="{{ base64Image(associationLogo()) }}">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <title>Waivers and Successful Payments for {{ $data['due']->title }} -
        {{ $data['due']->academic_year }}/{{ $data['due']->academic_year + 1 }}</title>
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
                    <p style="text-transform: uppercase; font-weight: bold; color: red; margin: 10px; font-size: 18px;">
                        Waivers and Successful Payments for <br> {{ $data['due']->title }} -
                        {{ $data['due']->academic_year }}/{{ $data['due']->academic_year + 1 }}
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
                            DUE INFO
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border: 0px; border-bottom:none; padding:10px">
                        <td>
                            <span style="font-weight: bold">Due: </span>
                            <span class="">{{ $data['due']->title }}</span>
                            <br>
                            <span style="font-weight: bold">Description: </span>
                            <span class="">{{ $data['due']->description }}</span>
                            <br>
                            <br>
                            <span style="font-weight: bold">Status: </span>
                            <span class="">{{ $data['due']->is_active ? 'Active' : 'Inactive' }}</span>
                            <br>
                            <span style="font-weight: bold">Expected Payers: </span>
                            <span class="">{{ $data['due']->expectedPayerCount() }}</span>
                            <br>
                            <span style="font-weight: bold">Waivers: </span>
                            <span class="">{{ $data['due']->approvedWaiverCount() }}
                            </span>
                            <br>
                        </td>
                        <td>
                            <span style="font-weight: bold">Academic Session: </span>
                            <span
                                class="">{{ $data['due']->academic_year }}/{{ $data['due']->academic_year + 1 }}</span>
                            <br>
                            <span style="font-weight: bold">Amount: </span>
                            <span class="">₦{{ number_format($data['due']->amount, 2) }}</span>
                            <br>
                            <span style="font-weight: bold">Variations: </span>
                            <span class="">{{ $data['due']->variations()->count() }}</span>
                            <br>
                            <span style="font-weight: bold">Paid: </span>
                            <span class="">{{ $data['due']->successfulPaymentCount() }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Outstanding: </span>
                            <span class="">
                                {{-- {{ $data['due']->membersUnpaidAndUnwaived()->count() }} --}}
                                {{ $data['due']->expectedPayerCount() - $data['due']->membersPaidOrWaived()->count() }}
                            </span>
                            <br>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="m-4"></td>
                    </tr>
                </tbody>
            </table>
            {{-- ################################################################# --}}
            {{-- waivers start --}}
            <table style="margin-top: 5px; width: 100%">
                <thead class="justify-content-center text-center align-middle">
                    <tr style="border: 1px solid white; justify-content: center; text-align: center;">
                        <th scope="col" colspan="4"
                            style="background: {{ $secondaryColour }}; padding: 10px; margin: 10px; color: white; border: 1px solid white; text-align: center;">
                            Waivers
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($data['waiversGranted']->isNotEmpty())
                        @foreach ($data['waiversGranted'] as $waiver)
                            @php $ctx = $waiver->academicContext; @endphp

                            <tr style="border: 1px solid #000; padding:10px">
                                <td style="width:10px;">
                                    {{ $loop->iteration }}
                                </td>
                                <td>

                                    @php
                                        $photoPath = public_path(
                                            'assets/tenant_' .
                                                $data['tenant']->id .
                                                '/uploads/users/members/photo/' .
                                                $waiver->payer->photo,
                                        );
                                    @endphp

                                    @if ($waiver->payer->photo && file_exists($photoPath))
                                        <img src="{{ $photoPath }}" alt="Candidate Photo" width="90px">
                                    @else
                                        <img src="{{ public_path('system/images/default_profile_pic.webp') }}"
                                            alt="Default Picture" width="90px">
                                    @endif
                                </td>
                                <td>
                                    <span style="font-weight: bold">Name: </span>
                                    <span class="">{{ $waiver->personalProfile->first_name }}
                                        {{ $waiver->personalProfile->middle_name }}
                                        {{ $waiver->personalProfile->last_name }}</span>
                                    <br>
                                    <span style="font-weight: bold">Level: </span>
                                    <span class="">
                                        {{-- @php
                                            if ($ctx['source'] == 'readmission') {
                                                # code... reAdmission
                                                $academic = $ctx['reAdmission'];
                                                $level = studentLevelAtDue($waiver->memberAcademicInfo, $waiver->due);
                                            } else {
                                                $academic = $waiver->payer->memberAcademicInfo;
                                                $level = studentLevelAtDue($waiver->memberAcademicInfo, $waiver->due);
                                            }
                                        @endphp
                                        {{ currentLevel($academic->theProgram, $level) }}</span> --}}

                                        @php
                                            $academic = $waiver->registeredPayer?->memberAcademicInfo;

                                            $programLevel = studentLevelAtDue($academic, $waiver->due);

                                            $program = $programLevel['program'];
                                            $level = $programLevel['level'];
                                        @endphp

                                        @if ($program)
                                            {{ currentLevel($program, $level) }}
                                        @else
                                            <span class="text-danger">
                                                Previous programme is missing
                                            </span>
                                        @endif
                                        <br>
                                        <span style="font-weight: bold">Matric Number: </span>
                                        <span class="">{{ $ctx['matric_number'] }}</span>
                                        <br>
                                        <span style="font-weight: bold">Department: </span>
                                        <span class="">{{ $ctx['department'] }}</span>
                                        <br>
                                        <span style="font-weight: bold">Program: </span>
                                        <span class="">{{ $ctx['program'] }} - {{ $ctx['program_type'] }}</span>
                                        <br>
                                        {{-- {{ $ctx['admission_session'] }}
                                {{ $ctx['graduation_session'] }} --}}
                                </td>
                                <td>
                                    <span style="font-weight: bold">Waiver ID: </span>
                                    <span class="text-uppercase"
                                        style="overflow-wrap: anywhere;">{{ $waiver->waiver_id }}</span>
                                    <br>
                                    <span style="font-weight: bold">Reason: </span>
                                    <span class="">{{ $waiver->reason }}</span>
                                    <br>
                                    <span style="font-weight: bold">Date: </span>
                                    <span class="">
                                        @if ($waiver->initiated_at)
                                            {{ \Carbon\Carbon::parse($waiver->initiated_at)->format('l, d F, Y h:i A') }}
                                        @endif
                                    </span>
                                    <br>
                                    <span style="font-weight: bold">Status: </span>
                                    <span class="">
                                        @switch($waiver->approval_status)
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
                                                <span class="badge bg-yellow text-black">{{ $waiver->approval_status }}</span>
                                        @endswitch
                                    </span>
                                    <br>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="width: 100%; color: red; text-align:center; font-size:1.5rem">
                                No waiver records
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="4" class="m-4"></td>
                    </tr>

                </tbody>

            </table>
            {{-- waivers end --}}

            {{-- payment start --}}
            <table style="margin-top: 5px; width: 100%">
                <thead class="justify-content-center text-center align-middle">
                    <tr style="border: 1px solid white; justify-content: center; text-align: center;">
                        <th scope="col" colspan="4"
                            style="background: {{ $secondaryColour }}; padding: 10px; margin: 10px; color: white; border: 1px solid white; text-align: center;">
                            Payments
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($data['successfulPayment']->isNotEmpty())
                        @foreach ($data['successfulPayment'] as $payment)
                            @php $ctxp = $payment->academicContext; @endphp

                            <tr style="border: 1px solid #000; padding:10px">

                                <td style="width:10px;">
                                    {{ $loop->iteration }}
                                </td>
                                <td>

                                    @php
                                        $photoPath = public_path(
                                            'assets/tenant_' .
                                                $data['tenant']->id .
                                                '/uploads/users/members/photo/' .
                                                $payment->registeredPayer->photo,
                                        );
                                    @endphp

                                    @if ($payment->registeredPayer->photo && file_exists($photoPath))
                                        <img src="{{ $photoPath }}" alt="Candidate Photo" width="90px">
                                    @else
                                        <img src="{{ public_path('system/images/default_profile_pic.webp') }}"
                                            alt="Default Picture" width="90px">
                                    @endif
                                </td>
                                <td>
                                    <span style="font-weight: bold">Name: </span>
                                    <span class="">{{ $payment->payerPersonalProfile->first_name }}
                                        {{ $payment->payerPersonalProfile->middle_name }}
                                        {{ $payment->payerPersonalProfile->last_name }}</span>
                                    <br>
                                    <span style="font-weight: bold">Level: </span>
                                    <span class="">
                                        {{-- @php
                                            $academic = $payment->registeredPayer->memberAcademicInfo;
                                            $level = studentLevelAtDue($payment->memberAcademicInfo, $payment->due);
                                        @endphp --}}

                                        {{-- @php
                                            if ($ctxp['source'] == 'readmission') {
                                                $academic = $ctxp['reAdmission'];
                                                $level = studentLevelAtDue($academic, $payment->due);
                                            } else {
                                                $academic = $payment->payer->memberAcademicInfo;
                                                $level = studentLevelAtDue($payment->memberAcademicInfo, $payment->due);
                                            }
                                        @endphp --}}

                                        @php
                                            $academic = $payment->registeredPayer?->memberAcademicInfo;

                                            $programLevel = studentLevelAtDue($academic, $payment->due);

                                            $program = $programLevel['program'];
                                            $level = $programLevel['level'];
                                        @endphp

                                        @if ($program)
                                            {{ currentLevel($program, $level) }}
                                        @else
                                            <span class="text-danger">
                                                Previous programme is missing
                                            </span>
                                        @endif

                                        {{-- {{ currentLevel($academic->theProgram, $level) }} --}}
                                    </span>
                                    <br>
                                    <span style="font-weight: bold">Matric Number: </span>
                                    <span class="">{{ $ctxp['matric_number'] }}</span>
                                    <br>
                                    <span style="font-weight: bold">Department: </span>
                                    <span class="">{{ $ctxp['department'] }}</span>
                                    <br>
                                    <span style="font-weight: bold">Program: </span>
                                    <span class="">{{ $ctxp['program'] }} - {{ $ctxp['program_type'] }}</span>
                                    <br>
                                </td>
                                <td>
                                    <span style="font-weight: bold">Transaction ID: </span>
                                    <span class="text-uppercase"
                                        style="overflow-wrap: anywhere;">{{ $payment->payment_transaction_id }}</span>
                                    <br>
                                    <span style="font-weight: bold">Type: </span>
                                    <span class="text-capitalize">{{ $payment->payment_type }}</span>
                                    <br>
                                    <span style="font-weight: bold">Mode: </span>
                                    <span class="text-capitalize">{{ $payment->payment_mode }}</span>
                                    <br>
                                    <span style="font-weight: bold">Amount: </span>
                                    <span
                                        class="text-capitalize">₦{{ number_format($payment->amount_payable, 2) }}</span>
                                    <br>
                                    <span style="font-weight: bold">Date: </span>
                                    <span
                                        class="text-capitalize">{{ \Carbon\Carbon::parse($payment->paid_at)->format('l, d F, Y h:i A') }}</span>
                                    <br>
                                    <span style="font-weight: bold">Status: </span>
                                    <span class="text-capitalize">
                                        {{ $payment->payment_status }}/{{ $payment->approval_status }}
                                    </span>
                                    <br>
                                    {{-- 
                                    
                                    <span style="font-weight: bold">Date: </span>
                                    <span class="">
                                        @if ($waiver->initiated_at)
                                            {{ \Carbon\Carbon::parse($waiver->initiated_at)->format('l, d F, Y h:i A') }}
                                        @endif
                                    </span>
                                    <br>
                                    <span style="font-weight: bold">Status: </span>
                                    <span class="">
                                        @switch($waiver->approval_status)
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
                                                <span class="badge bg-yellow text-black">{{ $waiver->approval_status }}</span>
                                        @endswitch
                                    </span>
                                    <br> --}}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="width: 100%; color: red; text-align:center; font-size:1.5rem">
                                No payment records
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="4" class="m-4"></td>
                    </tr>

                </tbody>

            </table>
            {{-- payment end --}}

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
