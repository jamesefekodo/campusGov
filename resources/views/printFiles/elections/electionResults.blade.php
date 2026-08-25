<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}"> --}}
    <link rel="icon" type="image/x-icon" href="{{ base64Image(associationLogo()) }}">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <title>Election Results
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
                        Election Results
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
            <div class="">
                <p
                    style="background: {{ $secondaryColour }}; padding: 10px; margin: 10px; color: white; border: 1px solid white; text-align: center; font-weight: bold;">
                    RESULTS
                </p>
            </div>
            {{-- chart start --}}
            @if ($data['election']->voting_method == 'standard')
                <div style="margin-top: 20px;">

                    <div style="font-size: 16px; font-weight: bold; margin-bottom: 8px;">
                        Voter Participation
                    </div>

                    <div style="font-size: 13px; margin-bottom: 8px;">
                        {{ number_format($data['totalUniqueVoters'] ?? 0) }}
                        of
                        {{ number_format($data['totalQualifiedVoters'] ?? 0) }}
                        qualified voters participated
                    </div>

                    <!-- mPDF-Compatible Progress Bar Using a Table -->
                    <table
                        style="width: 100%; border-collapse: collapse; background-color: #e9ecef; border-radius: 5px; overflow: hidden;"
                        cellpadding="0" cellspacing="0">
                        <tr>
                            <!-- Green Bar for Participated (Dynamic Width) -->
                            <td
                                style="width: {{ $data['participationPercentage'] ?? 0 }}%; height: 25px; background-color: #198754; font-size: 1px; line-height: 1px;">
                                &nbsp;</td>

                            <!-- Gray Bar for Remaining (Auto-fills the rest) -->
                            <td style="height: 25px; background-color: #e9ecef; font-size: 1px; line-height: 1px;">
                                &nbsp;
                            </td>
                        </tr>
                    </table>

                    <!-- Percentage Label -->
                    <div
                        style="
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-top: 6px;
    ">
                        {{ number_format($data['participationPercentage'] ?? 0, 1) }}% Participation
                    </div>

                </div>
            @endif
            {{-- chart ends --}}
            <style>
                .report-title {
                    font-size: 18pt;
                    font-weight: bold;
                    color: {{ $primaryColour }};
                    margin-bottom: 3px;
                }

                .report-subtitle {
                    font-size: 9pt;
                    color: #6b7280;
                    margin-bottom: 14px;
                }

                .position-card {
                    border: 1px solid #d9e2ec;
                    margin-bottom: 16px;
                }

                .position-header {
                    background-color: {{ $primaryColour }};
                    color: #ffffff;
                    padding: 9px 10px;
                    font-size: 12pt;
                    font-weight: bold;
                }

                .position-body {
                    padding: 10px;
                }

                .summary-winner {
                    background-color: #e8f7ee;
                    border: 1px solid #a7dfba;
                    color: #146c3a;
                    padding: 8px;
                    margin-bottom: 10px;
                }

                .summary-tie {
                    background-color: #fff7d6;
                    border: 1px solid #f0d77b;
                    color: #6c5600;
                    padding: 8px;
                    margin-bottom: 10px;
                }

                .summary-empty {
                    background-color: #f3f4f6;
                    border: 1px solid #d1d5db;
                    color: #4b5563;
                    padding: 8px;
                    margin-bottom: 10px;
                }

                .stat-box {
                    border: 1px solid #d9e2ec;
                    background-color: #f8fafc;
                    text-align: center;
                    padding: 7px;
                }

                .stat-label {
                    display: block;
                    color: #64748b;
                    font-size: 8pt;
                }

                .stat-value {
                    display: block;
                    color: #123b68;
                    font-size: 13pt;
                    font-weight: bold;
                    margin-top: 2px;
                }

                .candidate-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 12px;
                }

                .candidate-table th {
                    background-color: #edf2f7;
                    color: #334155;
                    border-bottom: 1px solid #cbd5e1;
                    padding: 7px;
                    text-align: left;
                    font-size: 8pt;
                }

                .candidate-table td {
                    border-bottom: 1px solid #e2e8f0;
                    padding: 7px;
                    vertical-align: middle;
                }

                .leading-row {
                    background-color: #edf9f0;
                }

                .rank-circle {
                    color: #ffffff;
                    background-color: #64748b;
                    text-align: center;
                    font-weight: bold;
                    padding: 4px;
                }

                .rank-leading {
                    background-color: #198754;
                }

                .candidate-photo {
                    width: 38px;
                    height: 38px;
                }

                .candidate-name {
                    font-weight: bold;
                    color: {{ $primaryColour }};
                }

                .candidate-status {
                    font-size: 8pt;
                    color: #ffffff;
                    background-color: #198754;
                    padding: 3px 5px;
                }

                .candidate-tie {
                    font-size: 8pt;
                    color: #3f3100;
                    background-color: #f7d559;
                    padding: 3px 5px;
                }

                .vote-count {
                    text-align: right;
                    font-weight: bold;
                    color: {{ $primaryColour }};
                }

                .vote-percent {
                    text-align: right;
                    color: #64748b;
                    font-size: 8pt;
                }

                .bar-background {
                    background-color: #e5e7eb;
                    height: 7px;
                }

                .bar-leading {
                    background-color: #198754;
                    height: 7px;
                }

                .bar-normal {
                    background-color: #2563eb;
                    height: 7px;
                }

                .no-records {
                    text-align: center;
                    color: #b91c1c;
                    padding: 20px;
                    border: 1px dashed #fca5a5;
                }

                .pdf-candidate-card {
                    width: 100%;
                    border: 1px solid #cbd5e1;
                    background-color: #ffffff;
                    margin-top: 7px;
                    margin-bottom: 7px;
                    page-break-inside: avoid;
                }

                .pdf-candidate-leading {
                    border: 1.5px solid #198754;
                    background-color: #effaf2;
                }

                .pdf-rank {
                    width: 9mm;
                    height: 9mm;
                    line-height: 9mm;
                    text-align: center;
                    color: #ffffff;
                    background-color: #64748b;
                    font-size: 10pt;
                    font-weight: bold;
                }

                .pdf-rank-leading {
                    background-color: #198754;
                }

                .pdf-candidate-label {
                    color: #64748b;
                    font-size: 7.5pt;
                    font-weight: bold;
                }

                .pdf-candidate-name {
                    color: {{ $secondaryColour }};
                    font-size: 12pt;
                    font-weight: bold;
                    line-height: 1.3;
                }

                .pdf-candidate-username {
                    color: #64748b;
                    font-size: 8pt;
                    margin-top: 2px;
                }

                .pdf-winner-badge {
                    display: inline-block;
                    margin-top: 4px;
                    padding: 3px 6px;
                    background-color: #198754;
                    color: #ffffff;
                    font-size: 7.5pt;
                    font-weight: bold;
                }

                .pdf-tie-badge {
                    display: inline-block;
                    margin-top: 4px;
                    padding: 3px 6px;
                    background-color: #f7d559;
                    color: #3f3100;
                    font-size: 7.5pt;
                    font-weight: bold;
                }

                .pdf-vote-label {
                    color: #64748b;
                    font-size: 7.5pt;
                    text-align: right;
                }

                .pdf-vote-count {
                    color: {{ $secondaryColour }};
                    font-size: 14pt;
                    font-weight: bold;
                    text-align: right;
                }

                .pdf-vote-percent {
                    color: #475569;
                    font-size: 9pt;
                    text-align: right;
                }

                .pdf-progress-bg {
                    height: 8px;
                    background-color: #e5e7eb;
                }

                .pdf-progress-normal {
                    height: 8px;
                    background-color: #2878c8;
                }

                .pdf-progress-leading {
                    height: 8px;
                    background-color: #198754;
                }
            </style>



            @foreach ($data['positionResults'] as $result)
                @php
                    $position = $result['position'];
                    $candidateResults = $result['candidateResults'];
                    $totalVotes = $result['totalVotes'];
                    $highestVotes = $result['highestVotes'];
                    $isTie = $result['isTie'];
                    $winner = $result['winner'];
                    $tiedCandidates = $result['tiedCandidates'];
                    $isCompleted = $data['election']->status === 'completed';
                @endphp

                <div class="position-card mt-2">

                    <div class="position-header">
                        Results - {{ $position->position ?? 'Unnamed Position' }}
                    </div>

                    <div class="position-body">

                        {{-- Winner / tie summary --}}
                        @if ($isTie)
                            <div class="summary-tie">
                                <strong>Result: Tied Winner</strong><br>

                                @foreach ($tiedCandidates as $candidate)
                                    @php
                                        $candidateName =
                                            ($candidate->theCandidate?->memberPersonalProfile?->first_name ??
                                                ($candidate->level_at_application ?? 'Unknown Candidate')) .
                                            ' ' .
                                            ($candidate->theCandidate?->memberPersonalProfile?->last_name ?? '');
                                    @endphp

                                    {{ trim($candidateName) }}
                                    — <strong>{{ number_format($candidate->voteCount) }} votes</strong>

                                    @if (!$loop->last)
                                        &nbsp; | &nbsp;
                                    @endif
                                @endforeach
                            </div>
                        @elseif ($winner)
                            @php
                                $winnerName =
                                    ($winner->theCandidate?->memberPersonalProfile?->first_name ??
                                        ($winner->theCandidate?->level_at_application ?? 'Unknown Candidate')) .
                                    ' ' .
                                    ($winner->theCandidate?->memberPersonalProfile?->last_name ?? '');
                            @endphp

                            <div class="summary-winner">
                                <strong>{{ $isCompleted ? 'Winner' : 'Currently Leading' }}:</strong>
                                {{ trim($winnerName) }}
                                — <strong>{{ number_format($winner->voteCount) }} votes</strong>
                            </div>
                        @else
                            <div class="summary-empty">
                                <strong>Result:</strong> No votes have been recorded for this position.
                            </div>
                        @endif

                        {{-- Position statistics --}}
                        <table width="100%" cellpadding="3" cellspacing="0">
                            <tr>
                                <td width="33.33%">
                                    <div class="stat-box">
                                        <span class="stat-label">Total Candidates</span>
                                        <span class="stat-value">{{ $candidateResults->count() }}</span>
                                    </div>
                                </td>

                                <td width="33.33%">
                                    <div class="stat-box">
                                        <span class="stat-label">Total Votes</span>
                                        <span class="stat-value">{{ number_format($totalVotes) }}</span>
                                    </div>
                                </td>

                                <td width="33.33%">
                                    <div class="stat-box">
                                        <span class="stat-label">Highest Votes</span>
                                        <span class="stat-value">{{ number_format($highestVotes) }}</span>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        @if ($candidateResults->isNotEmpty())
                            @foreach ($candidateResults as $candidate)
                                @php
                                    $percent =
                                        $totalVotes > 0 ? round(($candidate->voteCount / $totalVotes) * 100, 1) : 0;

                                    $safePercent = max(0, min(100, $percent));

                                    $isLeading = $candidate->voteCount > 0 && $candidate->voteCount === $highestVotes;

                                    $candidateIsTied = $isLeading && $isTie;

                                    $firstName = $candidate->theCandidate?->memberPersonalProfile?->first_name;
                                    $lastName = $candidate->theCandidate?->memberPersonalProfile?->last_name;
                                    // $username = $candidate->theCandidate?->level_at_application ?? 'Unknown Candidate';
                                    $username = $candidate->level_at_application ?? 'Unknown Candidate';

                                    $candidateName = trim($firstName . ' ' . $lastName);

                                    if (empty($candidateName)) {
                                        $candidateName = $username;
                                    }

                                    $photo = $candidate->theCandidate?->photo;

                                    $photoPath = $photo
                                        ? public_path(
                                            'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $photo,
                                        )
                                        : null;

                                    $imageSource =
                                        $photoPath && is_file($photoPath)
                                            ? base64Image(
                                                asset(
                                                    'assets/tenant_' .
                                                        tenant()->id .
                                                        '/uploads/users/members/photo/' .
                                                        $photo,
                                                ),
                                            )
                                            : base64Image(asset('system/images/default_profile_pic.webp'));
                                @endphp

                                <table width="100%" cellpadding="0" cellspacing="0"
                                    class="pdf-candidate-card {{ $isLeading ? 'pdf-candidate-leading' : '' }}">
                                    <tr>
                                        {{-- Rank --}}
                                        <td width="11%" align="center" valign="middle">
                                            <div class="pdf-rank {{ $isLeading ? 'pdf-rank-leading' : '' }}">
                                                {{ $loop->iteration }}
                                            </div>
                                        </td>

                                        {{-- Candidate photo --}}
                                        <td width="19%" align="center" valign="middle" style="padding: 7px;">
                                            <img src="{{ $imageSource }}" alt="{{ $candidateName }}"
                                                width="22mm" height="22mm" style="border: 1px solid #b8c5d1;">
                                        </td>

                                        {{-- Candidate details --}}
                                        <td width="43%" valign="middle" style="padding: 7px 5px;">
                                            <div class="pdf-candidate-label">
                                                CANDIDATE
                                            </div>

                                            <div class="pdf-candidate-name">
                                                {{ $candidateName }}
                                            </div>

                                            @if ($username !== $candidateName)
                                                <div class="pdf-candidate-username">
                                                    {{-- Username: {{ $username }} --}}
                                                    Level: {{ $username }}

                                                </div>
                                            @endif

                                            @if ($isLeading && $isCompleted)
                                                @if ($candidateIsTied)
                                                    <span class="pdf-tie-badge">
                                                        TIED WINNER
                                                    </span>
                                                @else
                                                    <span class="pdf-winner-badge">
                                                        WINNER
                                                    </span>
                                                @endif
                                            @elseif ($isLeading)
                                                <span class="pdf-winner-badge">
                                                    CURRENTLY LEADING
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Vote information --}}
                                        <td width="27%" valign="middle" style="padding: 7px 9px 7px 5px;">
                                            <div class="pdf-vote-label">
                                                TOTAL VOTES
                                            </div>

                                            <div class="pdf-vote-count">
                                                {{ number_format($candidate->voteCount) }}
                                            </div>

                                            <div class="pdf-vote-percent">
                                                {{ $percent }}% of votes
                                            </div>

                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                class="pdf-progress-bg" style="margin-top: 6px;">
                                                <tr>
                                                    <td width="{{ $safePercent }}%"
                                                        class="{{ $isLeading ? 'pdf-progress-leading' : 'pdf-progress-normal' }}">
                                                    </td>

                                                    <td width="{{ 100 - $safePercent }}%"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endforeach
                        @else
                            <div class="no-records">
                                No candidates or vote records were found for this position.
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach

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
