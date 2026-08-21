<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}"> --}}
    <link rel="icon" type="image/x-icon" href="{{ base64Image(associationLogo()) }}">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <title>Application/Endorsement Form
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
                    <p style="text-transform: uppercase; font-weight: bold; color: red; margin: 10px; font-size: 18px;">
                        Application/Endorsement Form - {{ $data['election']->title }}
                        {{ $data['election']->academic_session }}/{{ $data['election']->academic_session + 1 }}
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
                            APPLICANT
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
                                        $data['application']->theCandidate->photo,
                                );
                            @endphp

                            @if ($data['application']->theCandidate->photo && file_exists($photoPath))
                                <img src="{{ $photoPath }}" alt="Candidate Photo" width="140px">
                            @else
                                <img src="{{ public_path('system/images/default_profile_pic.webp') }}"
                                    alt="Default Picture" width="140px">
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: bold">Name: </span>
                            <span class="">
                                @php
                                    $personalProfile = $data['application']->theCandidate->memberPersonalProfile;
                                    $academicInfo = $data['application']->theCandidate->memberAcademicInfo;
                                @endphp
                                {{ $personalProfile->first_name }} {{ $personalProfile->middle_name }}
                                {{ $personalProfile->last_name }}</span>
                            <br>
                            <span style="font-weight: bold">Matric No.: </span>
                            <span class="text-uppercase">
                                {{ $academicInfo->matric_number }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Level : </span>
                            <span class="">
                                @php
                                    $academic = $academicInfo;
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

                                    {{ currentLevel($academic?->theProgram, studentCurrentLevel($academic)) }}
                                @elseif($academic?->academic_status == 'alumni')
                                    {{ $academic?->graduation_session
                                        ? $academic?->graduation_session . '/' . ($academic?->graduation_session + 1)
                                        : '-' }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $academic?->academic_status ?? '-')) }}

                                    @if ($academic?->graduation_session)
                                        ({{ $academic?->graduation_session }}/{{ $academic?->graduation_session + 1 }})
                                    @endif

                                @endif
                            </span>
                            <br>
                            <span style="font-weight: bold">Program: </span>
                            <span class="text-capitalize">
                                {{ $academicInfo?->theProgram?->name }} @if ($academicInfo?->theProgram?->short_name)
                                    ({{ $academicInfo?->theProgram?->short_name }}) -
                                    {{ $academicInfo->programType->type_name }}
                                @endif
                            </span>
                            <br>
                            <span style="font-weight: bold">Faculty : </span>
                            <span class="text-capitalize">
                                {{ $academicInfo?->theFaculty?->name }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Department : </span>
                            <span class="text-capitalize">
                                {{ $academicInfo?->theDepartment?->name }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Phone : </span>
                            <span class="text-capitalize">
                                {{ $personalProfile->phone1 }} @if ($personalProfile->phone2)
                                    /{{ $personalProfile->phone2 }}
                                @endif
                            </span>
                            <br>
                            <span style="font-weight: bold">Email : </span>
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

            <table style="margin: 5px; width: 100%; margin-bottom: 10px">
                <thead class="justify-content-center text-center align-middle">
                    <tr style="border: 1px solid white; justify-content: center; text-align: center;">
                        <th scope="col" colspan="2"
                            style="background: {{ $primaryColour }}; padding: 10px; margin: 10px; color: white; border: 1px solid white; text-align: center;">
                            ELECTION/POSITION
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border: 0px; border-bottom:none; padding:10px">
                        <td>
                            <span style="font-weight: bold">Election:</span>
                            <span class="">{{ $data['election']->title }}</span>
                            <br>
                            <span style="font-weight: bold">Description:</span>
                            <span class="">{{ $data['election']->description }}</span>
                            <br>
                            <span style="font-weight: bold">Position Aspired:</span>
                            <span class="">{{ $data['application']->position->position }}</span>
                            <br>

                        </td>
                        <td>
                            <span style="font-weight: bold">Academic Session:</span>
                            <span
                                class="">{{ $data['election']->academic_session }}/{{ $data['election']->academic_session + 1 }}</span>
                            <br>
                            <span style="font-weight: bold">Payment:</span>
                            <span class="">
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
                                    /

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
                                @else
                                    No successful payment record was found.
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
                            style="background: {{ $secondaryColour }}; padding: 10px; margin: 10px; color: white; border: 1px solid white; text-align: center;">
                            ENDORSEMENT
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <br>
                    {{-- hod start --}}
                    <tr style="border: 0; padding: 10px;">
                        <td style="text-align: center;" colspan="2">
                            <br>
                            <br>
                            <br>

                            <span style="color: grey;">
                                Remarks, name, stamp, signature & date
                            </span>

                            <br>

                            _________________________________________________________

                            <br>

                            <span style="font-weight: bold">
                                <span style="font-weight: normal; font-style:italic;">For:</span>
                                Applicant's HOD
                            </span>
                        </td>
                    </tr>
                    {{-- hod end --}}
                    @switch(basicSystemInfo()->operational_level)
                        @case('faculty')
                        @case('institute')
                            {{-- dean start --}}
                            <tr style="border: 0; padding: 10px;">
                                <td style="text-align: center;" colspan="2">
                                    <br>
                                    <br>
                                    <br>

                                    <span style="color: grey;">
                                        Remarks, name, stamp, signature & date
                                    </span>

                                    <br>

                                    _________________________________________________________

                                    <br>

                                    <span style="font-weight: bold">
                                        <span style="font-weight: normal; font-style:italic;">For:</span>
                                        Applicant's Dean
                                    </span>
                                </td>
                            </tr>
                            {{-- dean end --}}
                        @break

                        @default
                    @endswitch
                    {{-- others start --}}
                    <tr style="border: 0; padding: 10px;">
                        <td style="text-align: center; width: 50%;">
                            <br>
                            <br>
                            <br>

                            <span style="color: grey;">
                                Remarks, name, stamp, signature & date
                            </span>

                            <br>

                            _________________________________________________________

                            <br>

                            <span style="font-weight: bold">
                                Others 1
                            </span>
                        </td>
                        <td style="text-align: center; width: 50%;">
                            <br>
                            <br>
                            <br>

                            <span style="color: grey;">
                                Remarks, name, stamp, signature & date
                            </span>

                            <br>

                            _______________________________________________

                            <br>

                            <span style="font-weight: bold">
                                Others 2
                            </span>
                        </td>
                    </tr>
                    {{-- others end --}}
                    {{-- association exco 1 --}}
                    {{-- <tr style="border: 0; padding: 10px;">
                        <td style="text-align: center; width: 50%;">
                            <br>
                            <br>
                            <br>

                            <span style="color: grey;">
                                Remarks, name, stamp, signature & date
                            </span>

                            <br>

                            _________________________________________________________

                            <br>

                            <span style="font-weight: bold">
                                Association Exco 1
                            </span>
                        </td>
                        <td style="text-align: center; width: 50%;">
                            <br>
                            <br>
                            <br>

                            <span style="color: grey;">
                                Remarks, name, stamp, signature & date
                            </span>

                            <br>

                            _______________________________________________

                            <br>

                            <span style="font-weight: bold">
                                Association Exco 2
                            </span>
                        </td>
                    </tr> --}}
                    {{-- association exco end --}}

                    <tr>
                        <td colspan="2" class="m-4"></td>
                    </tr>

                </tbody>

            </table>
            {{-- Electoral Committee --}}
            <table style="margin-top: 5px; width: 100%">
                <thead class="justify-content-center text-center align-middle">
                    <tr style="border: 1px solid white; justify-content: center; text-align: center;">
                        <th scope="col" colspan="2"
                            style="background: {{ $secondaryColour }}; padding: 10px; margin: 10px; color: white; border: 1px solid white; text-align: center;">
                            Electoral Committee's Review/Approval
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <br>
                    <tr style="border: 0px; border-bottom:none; padding:10px">
                        <td style="justify-content: center; text-align: center;">
                            <br>
                            <br>
                            <br>
                            <br>
                            <span style="color: grey;">Remarks, name, stamp, signature & date</span>
                            <br>
                            _______________________________________________
                            <br>
                            <span style="font-weight: bold">For
                                Electoral Committee (1)
                            </span>
                        </td>
                        <td style="justify-content: center; text-align: center;">
                            <br>
                            <br>
                            <br>
                            <br>
                            <span style="color: grey;">Remarks, name, stamp, signature & date</span>
                            <br>
                            _______________________________________
                            <br>
                            <span style="font-weight: bold">For ->
                                Electoral Committee (2)
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
