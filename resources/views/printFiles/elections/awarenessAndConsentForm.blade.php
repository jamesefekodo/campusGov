<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}"> --}}
    <link rel="icon" type="image/x-icon" href="{{ base64Image(associationLogo()) }}">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <title>Election Awareness/Consent Form
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
                        Election Awareness/Consent Form
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
                            BASIC ELECTION INFO
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border: 0px; border-bottom:none; padding:10px">
                        <td>
                            <span style="font-weight: bold">Election: </span>
                            <span class="">{{ $data['election']->title }}</span>
                            <br>
                            <span style="font-weight: bold">Description: </span>
                            <span class="">{{ $data['election']->description }}</span>
                            <br>
                            <span style="font-weight: bold">Public Vis: </span>
                            <span class="">
                                @switch($data['election']->public_visibility)
                                    @case(true)
                                        Enabled
                                        (Guests can see election details)
                                    @break

                                    @default
                                        Disabled
                                        (Only signed-in members can see election details)
                                    @break
                                @endswitch
                            </span>
                            <br>
                            <span style="font-weight: bold">One candidate can apply for multiple positions?: </span>
                            <span class="">

                                @switch($data['election']->one_candidate_multiple_positions)
                                    @case(true)
                                        Yes
                                    @break

                                    @default
                                        No
                                    @break
                                @endswitch
                            </span>
                            <br>
                            <span style="font-weight: bold">Form Sales Starts: </span>
                            <span class="">
                                {{ \Carbon\Carbon::parse($data['election']->form_sales_starts)->format('D, d M, Y h:i A') }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Screening Starts: </span>
                            <span class="">
                                @if ($data['election']->screening_starts)
                                    {{ \Carbon\Carbon::parse($data['election']->screening_starts)->format('D, d M, Y h:i A') }}
                                @else
                                    -
                                @endif
                            </span>
                            <br>
                            <span style="font-weight: bold">Campaign Starts: </span>
                            <span class="">
                                @if ($data['election']->campaign_starts)
                                    {{ \Carbon\Carbon::parse($data['election']->campaign_starts)->format('D, d M, Y h:i A') }}
                                @else
                                    -
                                @endif
                            </span>
                            <br>
                            <span style="font-weight: bold">Voting Starts: </span>
                            <span class="">
                                {{ \Carbon\Carbon::parse($data['election']->voting_starts)->format('D, d M, Y h:i A') }}
                            </span>
                            <br>
                        </td>
                        <td>
                            <span style="font-weight: bold">Academic Session: </span>
                            <span
                                class="">{{ $data['election']->academic_session }}/{{ $data['election']->academic_session + 1 }}</span>
                            <br>
                            <span style="font-weight: bold">Voting Method: </span>
                            <span class="text-capitalize">
                                {{ $data['election']->voting_method }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Status:</span>
                            <span class="text-capitalize">
                                @switch($data['election']->status)
                                    @case('draft')
                                        Draft
                                    @break

                                    @case('awaiting_approval')
                                        Awaiting
                                        Approval
                                    @break

                                    @case('new')
                                        New
                                    @break

                                    @case('registration')
                                        Registration
                                    @break

                                    @case('screening')
                                        Screening
                                    @break

                                    @case('campaign')
                                        Campaign
                                    @break

                                    @case('completed')
                                        Completed
                                    @break

                                    @case('cancelled')
                                        Cancelled
                                    @break

                                    @case('suspended')
                                        Suspended
                                    @break

                                    @default
                                        {{ $data['election']->status }}</span>
                            @endswitch
                            </span>
                            <br>
                            <span style="font-weight: bold">Form Sales Ends: </span>
                            <span class="">
                                {{ \Carbon\Carbon::parse($data['election']->form_sales_ends)->format('D, d M, Y h:i A') }}
                            </span>
                            <br>
                            <span style="font-weight: bold">Screening Ends: </span>
                            <span class="">
                                @if ($data['election']->screening_ends)
                                    {{ \Carbon\Carbon::parse($data['election']->screening_ends)->format('D, d M, Y h:i A') }}
                                @else
                                    -
                                @endif
                            </span>
                            <br>
                            <span style="font-weight: bold">Campaign Ends: </span>
                            <span class="">
                                @if ($data['election']->campaign_ends)
                                    {{ \Carbon\Carbon::parse($data['election']->campaign_ends)->format('D, d M, Y h:i A') }}
                                @else
                                    -
                                @endif
                            </span>
                            <br>
                            <span style="font-weight: bold">Voting Ends: </span>
                            <span class="">
                                {{ \Carbon\Carbon::parse($data['election']->voting_ends)->format('D, d M, Y h:i A') }}
                            </span>
                            <br>

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span style="font-weight: bold">Qualified Voters: </span>
                            <span class="">
                                @if ($data['election']->voterGroups->isEmpty())
                                    All members are qualified voters.
                                @else
                                    <ul class="list-group list-group-numbered">
                                        @foreach ($data['election']->voterGroups as $group)
                                            <li class="list-group-item">
                                                @if ($group->theProgram)
                                                    {{ $group->theProgram->name }}
                                                    ({{ $group->theProgram->short_name }})

                                                    @if ($group->programType)
                                                        {{ $group->programType->type_name }}
                                                    @endforelse
                                                @else
                                                    All programs
                                                @endif -
                                                @if ($group->theFaculty)
                                                    {{ $group->theFaculty->name }}
                                                    ({{ $group->theFaculty->acronym }})
                                                @else
                                                    All Faculties
                                                @endif -
                                                @if ($group->theDepartment)
                                                    {{ $group->theDepartment->name }}
                                                @else
                                                    All Departments
                                                @endif -
                                                @if ($group->start_level)
                                                    Year {{ $group->start_level }}
                                                    @if ($group->end_level)
                                                        - Year {{ $group->end_level }}
                                                    @endif
                                                @else
                                                    All Level
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                            </span>
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
                            STAKEHOLDER'S AWARENESS/CONSENT
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <br>
                    @switch(basicSystemInfo()->operational_level)
                        @case('department')
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
                                        <span style="font-weight: normal; font-style:italic;">For:</span>
                                        {{ $data['onlyDepartment']->administrative_head_title }},
                                        {{ $data['onlyDepartment']->name }}
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
                                        <span style="font-weight: normal; font-style:italic;">For:</span>
                                        Student Affairs
                                    </span>
                                </td>

                            </tr>
                        @break

                        @case('faculty')
                            {{-- department's consent --}}
                            @foreach ($data['departments']->chunk(2) as $departments)
                                <tr style="border: 0; padding: 10px;">

                                    @foreach ($departments as $department)
                                        <td style="text-align: center; width: 50%;">
                                            <br>
                                            <br>
                                            <br>

                                            <span style="color: grey;">
                                                Remarks, name, position, stamp, signature & date
                                            </span>

                                            <br>

                                            _______________________________________________

                                            <br>

                                            <span style="font-weight: bold">
                                                <span style="font-weight: normal; font-style:italic;">For:</span>
                                                {{ $department->name }}
                                            </span>
                                        </td>
                                    @endforeach

                                    {{-- Add an empty cell if the number of departments is odd --}}
                                    @if ($departments->count() < 2)
                                        <td style="width: 50%;"></td>
                                    @endif

                                </tr>
                            @endforeach

                            {{-- faculty head --}}
                            <tr style="border: 0; padding: 10px;">
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
                                        <span style="font-weight: normal; font-style:italic;">For:</span>
                                        {{ $data['onlyFaculty']->administrative_head_title }},
                                        {{ $data['onlyFaculty']->name }} ({{ $data['onlyFaculty']->acronym }})
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
                                        <span style="font-weight: normal; font-style:italic;">For:</span>
                                        Student Affairs
                                    </span>
                                </td>
                            </tr>
                        @break

                        @case('institute')
                            {{-- all faculties --}}
                            @foreach ($data['faculties']->chunk(2) as $faculties)
                                <tr style="border: 0; padding: 10px;">

                                    @foreach ($faculties as $faculty)
                                        <td style="text-align: center; width: 50%;">
                                            <br>
                                            <br>
                                            <br>

                                            <span style="color: grey;">
                                                Remarks, name, position, stamp, signature & date
                                            </span>

                                            <br>

                                            _______________________________________________

                                            <br>

                                            <span style="font-weight: bold">
                                                <span style="font-weight: normal; font-style:italic;">For:</span>
                                                {{ $faculty->name }}
                                            </span>
                                        </td>
                                    @endforeach

                                    {{-- Add an empty cell if the number of departments is odd --}}
                                    @if ($faculties->count() < 2)
                                        <td style="width: 50%;"></td>
                                    @endif

                                </tr>
                                <tr style="border: 0; padding: 10px;">
                                    <td style="text-align: center;" colspan="2">
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
                                            <span style="font-weight: normal; font-style:italic;">For:</span>
                                            Student Affairs
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @break

                        @default
                    @endswitch

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
                            Electoral Committee
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
