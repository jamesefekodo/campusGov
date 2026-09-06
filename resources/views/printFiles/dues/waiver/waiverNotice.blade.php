<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}"> --}}
    <link rel="icon" type="image/x-icon" href="{{ base64Image(associationLogo()) }}">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @php
        $academic = $data['waiver']->payer?->memberAcademicInfo;

        $programLevel = studentLevelAtDue($academic, $data['waiver']->due);

        $program = $programLevel['program'];
        $level = $programLevel['level'];
    @endphp

    <title>Notice of Association Dues Payment Waiver
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
                    <p style="text-transform: uppercase; font-weight: bold; color: red; margin: 10px; font-size: 16px;">
                        Notice of Association Dues Payment Waiver :: <br> <span style="color: black; font-size: 18px">
                            {{ $data['waiver']->due->title }}
                            {{ $data['waiver']->due->academic_year }}/{{ $data['waiver']->due->academic_year + 1 }}
                            :: @if ($program)
                                {{ currentLevel($program, $level) }}
                            @endif </span>
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
                            MEMBER INFO
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
                                        $data['waiver']->payer->photo,
                                );
                            @endphp

                            @if ($data['waiver']->payer->photo && file_exists($photoPath))
                                <img src="{{ $photoPath }}" alt="Candidate Photo" width="160px">
                            @else
                                <img src="{{ public_path('system/images/default_profile_pic.webp') }}"
                                    alt="Default Picture" width="160px">
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: bold">Name: </span>
                            <span class="">
                                @php
                                    $personalProfile = $data['waiver']->payer->memberPersonalProfile;
                                    $academicInfo = $data['waiver']->payer->memberAcademicInfo;
                                @endphp
                                {{ $personalProfile->first_name }} {{ $personalProfile->middle_name }}
                                {{ $personalProfile->last_name }}</span>
                            <br>
                            @php
                                $ctx = resolveAcademicContextForDue(
                                    $data['waiver']->memberAcademicInfo,
                                    $data['waiver']->due,
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
                            <span style="font-weight: bold">Phone: </span>
                            <span class="text-capitalize">
                                {{ $personalProfile->phone1 }} @if ($personalProfile->phone2)
                                    /{{ $personalProfile->phone2 }}
                                @endif
                            </span>
                            <br>
                            <span style="font-weight: bold">Email: </span>
                            <span class="">
                                {{ $personalProfile->email1 }} @if ($personalProfile->email2)
                                    /{{ $personalProfile->email2 }}
                                @endif
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
            <table style="margin-top: 5px; width: 100%">
                <thead class="justify-content-center text-center align-middle">
                    <tr style="border: 1px solid white; justify-content: center; text-align: center;">
                        <th scope="col" colspan="2"
                            style="background: {{ $primaryColour }}; padding: 10px; margin: 10px; color: white; border: 1px solid white; text-align: center;">
                            ASSOCIATION DUE / WAIVER
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <br>
                    <tr style="border: 0px; border-bottom:none; padding:10px">
                        <td>
                            <span style="font-weight: bold">Due: </span>
                            <span class="">
                                {{ $data['waiver']->due->title }} -

                                @if ($program)
                                    {{ currentLevel($program, $level) }}
                                @else
                                    <span class="text-danger">
                                        Previous programme is missing
                                    </span>
                                @endif
                            </span>
                            <br>
                            <span style="font-weight: bold">Description: </span>
                            <span class="">
                                {{ $data['waiver']->due->description }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Reason for waiver: </span>
                            <span class="">
                                {{ $data['waiver']->reason }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Approval Status:</span>
                            <span class="text-capitalize">
                                @switch($data['waiver']->approval_status)
                                    @case('approved')
                                        Approved
                                    @break

                                    @case('declined')
                                        Declined
                                    @break

                                    @case('pending')
                                        Pending
                                    @break

                                    @default
                                        {{ $data['waiver']->approval_status }}
                                @endswitch
                            </span>
                            <br>

                        </td>
                        <td>
                            <span style="font-weight: bold">Academic Session: </span>
                            <span class="">
                                {{ $data['waiver']->due->academic_year }}/{{ $data['waiver']->due->academic_year + 1 }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Amount: </span>
                            <span class="text-capitalize">
                                ₦{{ number_format($data['due']->payable_amount, 2) }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Date: </span>
                            <span class="">
                                {{ \Carbon\Carbon::parse($data['waiver']->approved_at)->format('D, d M, Y') }}
                            </span>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 15px; font-weight: bold;">
                            <br>
                            Notice:
                            @if ($data['waiver']->approval_status == 'approved')
                                <span style="color: #198754;">
                                    This is to inform you that the member whose name and photo are on this notice is
                                    a
                                    member of the association and has been granted a waiver from
                                    {{ $data['due']->title }}
                                    {{ $data['waiver']->due->academic_year }}/{{ $data['waiver']->due->academic_year + 1 }}
                                    :: @if ($program)
                                        {{ currentLevel($program, $level) }}
                                    @endif. This notice should be treated as equivalent to a
                                    receipt
                                    or proof of payment, as this member is not expected to make payment upon being
                                    granted a waiver.
                                </span>
                            @else
                                <span style="color: #dc3545;">
                                    Kindly disregard this waiver notice, as it is not approved or is yet to be
                                    approved.
                                </span>
                            @endif
                        </td>
                    </tr>


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
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('verifyAssociationDuePaymentWaiver', $data['waiver']->uuid)) }}"
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
