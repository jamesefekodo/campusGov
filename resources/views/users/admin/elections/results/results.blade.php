@extends('layouts.userLayout')
@section('title', 'Election Results - ' . $data['election']->title)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.elections.include.view_election_inner_nav')
            </div>
            <div class="">
                <div class="row">
                    <div class="individual-review-card-container">
                        {{-- view applicand start --}}

                        <div class="col-md-12 individual-review-card">
                            <div class="my_form_container pb-5">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Results - <span class="text-uppercase">{{ strtolower($data['election']->title) }}
                                        {{ $data['election']->academic_session . '/' . $data['election']->academic_session + 1 }}</span>
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">ID:</span>
                                        <span class="text-black text-uppercase">
                                            {{ $data['election']->election_id }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Status:</span>
                                        <span class="text-black">
                                            @switch($data['election']->status)
                                                @case('draft')
                                                    <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                                @break

                                                @case('awaiting_approval')
                                                    <span class="badge bg-warning-subtle text-warning">Awaiting
                                                        Approval</span>
                                                @break

                                                @case('new')
                                                    <span class="badge bg-primary-subtle text-primary">New</span>
                                                @break

                                                @case('registration')
                                                    <span class="badge bg-primary-subtle text-primary">Registration</span>
                                                @break

                                                @case('screening')
                                                    <span class="badge bg-warning-subtle text-warning">Screening</span>
                                                @break

                                                @case('campaign')
                                                    <span class="badge bg-warning-subtle text-warning">Campaign</span>
                                                @break

                                                @case('voting')
                                                    <span class="badge bg-blue text-white">Ongoing</span>
                                                @break

                                                @case('completed')
                                                    <span class="badge bg-success-subtle text-success">Completed</span>
                                                @break

                                                @case('cancelled')
                                                    <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                                @break

                                                @case('suspended')
                                                    <span class="badge bg-danger-subtle text-danger">Suspended</span>
                                                @break

                                                @case('declined')
                                                    <span class="badge bg-danger-subtle text-danger">Declined</span>
                                                @break

                                                @default
                                                    <span
                                                        class="badge bg-danger-subtle text-danger">{{ $data['election']->status }}</span>
                                            @endswitch
                                            {{-- countdown for sales of form closure start --}}
                                            @if ($data['election']->status == 'registration')
                                                <span
                                                    class="badge rounded-pill bg-danger-subtle text-danger border border-danger px-3 py-2">
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    Registration closes in
                                                    <strong id="registration-countdown" class="ms-1"></strong>
                                                </span>

                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {

                                                        const deadline = new Date(
                                                            "{{ \Carbon\Carbon::parse($data['election']->form_sales_ends)->toIso8601String() }}"
                                                        ).getTime();

                                                        const countdown = document.getElementById('registration-countdown');

                                                        function updateCountdown() {

                                                            const now = new Date().getTime();
                                                            const distance = deadline - now;

                                                            if (distance <= 0) {
                                                                countdown.innerHTML = 'Registration has ended.';
                                                                return;
                                                            }

                                                            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                            const hours = Math.floor(
                                                                (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                                                            );
                                                            const minutes = Math.floor(
                                                                (distance % (1000 * 60 * 60)) / (1000 * 60)
                                                            );
                                                            const seconds = Math.floor(
                                                                (distance % (1000 * 60)) / 1000
                                                            );

                                                            countdown.innerHTML =
                                                                `${days}d ${hours}h ${minutes}m ${seconds}s`;
                                                        }

                                                        updateCountdown();
                                                        setInterval(updateCountdown, 1000);
                                                    });
                                                </script>
                                            @endif
                                            {{-- countdown for sales of form closure end --}}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Title:</span>
                                        <span class="text-black">
                                            {{ $data['election']->title }}
                                            {{ $data['election']->academic_session . '/' . $data['election']->academic_session + 1 }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black">
                                            {{ $data['election']->description }}
                                        </span>
                                    </div>
                                    @if (in_array($data['election']->status, ['voting', 'result_pending', 'completed']))
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Qualified Voters:</span>
                                            <span class="text-black">
                                                {{ $data['election']->qualified_voters }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Form sales starts:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['election']->form_sales_starts)->format('l, d F, Y h:i A') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Form sales ends:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['election']->form_sales_ends)->format('l, d F, Y h:i A') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Screening starts:</span>
                                        <span class="text-black">
                                            @if ($data['election']->screening_starts)
                                                {{ \Carbon\Carbon::parse($data['election']->screening_starts)->format('l, d F, Y h:i A') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Screening ends:</span>
                                        <span class="text-black">
                                            @if ($data['election']->screening_ends)
                                                {{ \Carbon\Carbon::parse($data['election']->screening_ends)->format('l, d F, Y h:i A') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Campaign starts:</span>
                                        <span class="text-black">
                                            @if ($data['election']->campaign_starts)
                                                {{ \Carbon\Carbon::parse($data['election']->campaign_starts)->format('l, d F, Y h:i A') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Campaign ends:</span>
                                        <span class="text-black">
                                            @if ($data['election']->campaign_ends)
                                                {{ \Carbon\Carbon::parse($data['election']->campaign_ends)->format('l, d F, Y h:i A') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Voting starts:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['election']->voting_starts)->format('l, d F, Y h:i A') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Voting ends:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['election']->voting_ends)->format('l, d F, Y h:i A') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Voting Method:</span>
                                        <span class="text-black text-capitalize">
                                            {{ $data['election']->voting_method }}
                                        </span>
                                    </div>
                                    @if ($data['election']->voting_method == 'monetary')
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Cost Per Vote:</span>
                                            <span class="text-black">
                                                ₦{{ number_format($data['election']->cost_per_vote, 2) }}
                                            </span>
                                        </div>
                                    @endif

                                </div>

                            </div>
                            <hr>
                            <div class="table_head m-2">
                                <div class="table_head_item col-md-12">
                                    <div class="table_title text-center">
                                        Click Position to see the results
                                    </div>
                                </div>
                            </div>
                            <div class="row row-cols-1 row-cols-md-3 g-4 summary_cards_container m-2" id="summaryContent">
                                @if ($data['election']->positions->count() > 0)
                                    @foreach ($data['election']->positions as $position)
                                        <a
                                            href="{{ route('viewElectionResultForPosition', ['election' => $data['election']->uuid, 'position' => $position->uuid]) }}">
                                            <div class="col">
                                                <div class="card shadow-sm summary_card">
                                                    <div class="card-body">
                                                        <i class="fas fa-user-tie fa-2x"></i>
                                                        <h4 class="card-title">{{ $position->position }}</h4>


                                                        @if ($data['election']->status == 'completed')
                                                            @php
                                                                $elecResult = $position->highestVoteCandidate();
                                                            @endphp

                                                            @if ($elecResult['is_tie'])
                                                                @foreach ($elecResult['tied_candidates'] as $candidate)
                                                                    <span class="badge bg-yellow text-black fs-7">
                                                                        {{ $candidate->theCandidate->memberPersonalProfile->first_name ?? $candidate->theCandidate->username }}

                                                                        {{ $candidate->theCandidate->memberPersonalProfile->last_name ?? '' }}
                                                                        ->

                                                                        {{ $candidate->voteCount }} votes

                                                                    </span>
                                                                @endforeach
                                                            @elseif ($elecResult['winner'])
                                                                @php
                                                                    $winner = $elecResult['winner'];
                                                                @endphp
                                                                <div class="badge bg-green text-white fs-6">
                                                                    {{ $winner->theCandidate->memberPersonalProfile->first_name ?? $winner->theCandidate->username }}

                                                                    {{ $winner->theCandidate->memberPersonalProfile->last_name ?? '' }}
                                                                    ->

                                                                    {{ $winner->voteCount }} votes
                                                                </div>
                                                            @endif

                                                            {{-- if election is not completed --}}
                                                        @else
                                                            @if ($position->qualifiedCandidates->isNotEmpty())
                                                                <span class="badge bg-primary text-white fs-5">
                                                                    {{ $position->qualifiedCandidates->count() }}
                                                                    Candidate(s)
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger text-white">N/A</span>
                                                            @endif
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @endif

                            </div>
                            <style>
                                .total_card {
                                    background: var(--system-primary) !important;
                                    color: #ffffff !important;
                                }

                                .total_card>.card-body>i {
                                    color: var(--system-primary) !important;
                                }

                                .summary_card {
                                    background: color-mix(in srgb,
                                            var(--system-secondary) 15%,
                                            white 85%);
                                    color: var(--system-primary);
                                    border: none;
                                    display: flex;
                                    flex-direction: column;
                                    justify-content: center;
                                    align-items: center;
                                    min-height: 150px;
                                    text-align: center;
                                    padding-top: 5px;
                                }

                                .summary_card:hover {
                                    background: var(--system-primary);
                                    color: color-mix(in srgb,
                                            var(--system-secondary) 15%,
                                            white 85%);
                                    transition: all 0.3s ease-in-out;
                                }

                                .summary_card i {
                                    margin-bottom: 10px;
                                }

                                .card-title {
                                    font-size: 26px;
                                }
                            </style>
                            {{-- view applicant end --}}
                        </div>

                    </div>

                    @if ($data['election']->status == 'completed')
                        <div class="px-5">
                            <a href="{{ route('printElectionResults', ['uuid' => $data['election']->uuid]) }}"
                                class="btn btn-danger w-100" target="_blank"><i class="fas fa-print me-2"></i> Download
                                Result</a>
                        </div>
                    @endif


                    {{-- charts start --}}
                    @if ($data['election']->voting_method == 'standard')
                        <div class="m-3 row col-md-12 g-3">
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Voter Participation</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="voterParticipationChart"></canvas>
                                    </div>
                                </div>

                                <div class="text-center mb-3">
                                    <h2>{{ number_format($data['participationRate'], 2) }}%</h2>
                                    <small>Voter Participation</small>
                                </div>

                                <script>
                                    new Chart(document.getElementById('voterParticipationChart'), {
                                        type: 'doughnut',
                                        data: {
                                            labels: ['Voted', 'Did Not Vote'],
                                            datasets: [{
                                                data: [
                                                    {{ $data['totalVoters'] }},
                                                    {{ $data['totalNotVoted'] }}
                                                ]
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: {
                                                legend: {
                                                    position: 'bottom'
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>

                            <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Voters by Faculty</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="facultyChart"></canvas>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const allFacultyLabels = @json($data['votersByFaculty']->keys()->values());

                                        const allFacultyCounts = @json($data['votersByFaculty']->values()->values());

                                        /*
                                         * Limit dashboard display to the ten faculties with the
                                         * highest number of unique voters.
                                         */
                                        const maxFaculties = 10;

                                        const facultyLabels = allFacultyLabels.slice(0, maxFaculties);
                                        const facultyCounts = allFacultyCounts.slice(0, maxFaculties);

                                        const facultyPalette = [
                                            '#2563eb', // Blue
                                            '#16a34a', // Green
                                            '#f59e0b', // Amber
                                            '#dc2626', // Red
                                            '#7c3aed', // Purple
                                            '#0891b2', // Cyan
                                            '#db2777', // Pink
                                            '#65a30d', // Lime
                                            '#ea580c', // Orange
                                            '#4f46e5' // Indigo
                                        ];

                                        function getFacultyLabelLimit() {
                                            if (window.innerWidth < 576) {
                                                return 10;
                                            }

                                            if (window.innerWidth < 992) {
                                                return 20;
                                            }

                                            return 36;
                                        }

                                        function shortenFacultyName(name) {
                                            const labelLimit = getFacultyLabelLimit();

                                            /*
                                             * Remove repetitive prefixes only from the visible label.
                                             * Keep the original full name for the tooltip.
                                             */
                                            const cleanName = String(name)
                                                .replace(/^Faculty of\s+/i, '')
                                                .replace(/^Faculty\s+/i, '')
                                                .replace(/\sand\s/gi, ' & ')
                                                .replace(/\s+/g, ' ')
                                                .trim();

                                            if (cleanName.length <= labelLimit) {
                                                return cleanName;
                                            }

                                            return cleanName.substring(0, labelLimit).trim() + '…';
                                        }

                                        const canvas = document.getElementById('facultyChart');

                                        if (!canvas || facultyLabels.length === 0) {
                                            return;
                                        }

                                        /*
                                         * Adjust the height only for this Faculty chart.
                                         */
                                        const chartContainer = canvas.parentElement;

                                        chartContainer.style.height = Math.max(
                                            300,
                                            facultyLabels.length * 38 + 70
                                        ) + 'px';

                                        new Chart(canvas, {
                                            type: 'bar',

                                            data: {
                                                labels: facultyLabels.map(shortenFacultyName),

                                                datasets: [{
                                                    label: 'Unique voters',
                                                    data: facultyCounts,

                                                    backgroundColor: facultyCounts.map(function(_, index) {
                                                        return facultyPalette[index % facultyPalette.length];
                                                    }),

                                                    borderColor: facultyCounts.map(function(_, index) {
                                                        return facultyPalette[index % facultyPalette.length];
                                                    }),

                                                    borderWidth: 1,
                                                    borderRadius: 5,

                                                    hoverBackgroundColor: facultyCounts.map(function(_, index) {
                                                        return facultyPalette[index % facultyPalette.length];
                                                    })
                                                }]
                                            },

                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,

                                                /*
                                                 * Horizontal layout keeps faculty names readable.
                                                 */
                                                indexAxis: 'y',

                                                plugins: {
                                                    legend: {
                                                        display: false
                                                    },

                                                    tooltip: {
                                                        callbacks: {
                                                            title: function(context) {
                                                                return facultyLabels[
                                                                    context[0].dataIndex
                                                                ];
                                                            },

                                                            label: function(context) {
                                                                return 'Unique voters: ' +
                                                                    Number(context.raw).toLocaleString();
                                                            }
                                                        }
                                                    }
                                                },

                                                scales: {
                                                    x: {
                                                        beginAtZero: true,

                                                        ticks: {
                                                            precision: 0
                                                        }
                                                    },

                                                    y: {
                                                        ticks: {
                                                            autoSkip: false,

                                                            font: {
                                                                size: window.innerWidth < 576 ? 10 : 12
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            {{-- <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Voters by Department</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="departmentChart"></canvas>
                                    </div>
                                </div>

                                <script>
                                    new Chart(document.getElementById('departmentChart'), {
                                        type: 'bar',
                                        data: {
                                            labels: @json($data['votersByDepartment']->keys()->values()),
                                            datasets: [{
                                                label: 'Voters',
                                                data: @json($data['votersByDepartment']->values()->values())
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div> --}}
                            <div class="col-md-8">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <strong>Voters by Department</strong>

                                        <small class="text-muted">
                                            Top 10 departments
                                        </small>
                                    </div>

                                    <div class="card-body">
                                        <div style="position: relative; height: 380px;">
                                            <canvas id="departmentChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const allDepartmentLabels = @json($data['votersByDepartment']->keys()->values());

                                    const allDepartmentCounts = @json($data['votersByDepartment']->values()->values());

                                    /*
                                     * Keep the top 10 departments because a long list makes
                                     * a dashboard chart difficult to read.
                                     */
                                    const maxDepartments = 10;

                                    const departmentLabels = allDepartmentLabels.slice(0, maxDepartments);
                                    const departmentCounts = allDepartmentCounts.slice(0, maxDepartments);

                                    /*
                                     * One colour per department bar.
                                     * The colours repeat safely if maxDepartments is ever increased.
                                     */
                                    const departmentPalette = [
                                        '#2563eb', // Blue
                                        '#16a34a', // Green
                                        '#f59e0b', // Amber
                                        '#dc2626', // Red
                                        '#7c3aed', // Purple
                                        '#0891b2', // Cyan
                                        '#db2777', // Pink
                                        '#65a30d', // Lime
                                        '#ea580c', // Orange
                                        '#4f46e5' // Indigo
                                    ];

                                    /*
                                     * Label length is adjusted based on available screen width.
                                     */
                                    function getLabelLimit() {
                                        if (window.innerWidth < 576) {
                                            return 10;
                                        }

                                        if (window.innerWidth < 992) {
                                            return 20;
                                        }

                                        return 36;
                                    }

                                    function shortenDepartmentName(name) {
                                        const labelLimit = getLabelLimit();

                                        /*
                                         * Remove repeated text from chart labels only.
                                         * The complete original department name remains in the tooltip.
                                         */
                                        const cleanName = String(name)
                                            .replace(/^Department of\s+/i, '')
                                            .replace(/^Department\s+/i, '')
                                            .replace(/\sand\s/gi, ' & ')
                                            .replace(/\s+/g, ' ')
                                            .trim();

                                        if (cleanName.length <= labelLimit) {
                                            return cleanName;
                                        }

                                        return cleanName.substring(0, labelLimit).trim() + '…';
                                    }

                                    /*
                                     * Set enough height for each horizontal bar.
                                     * This affects only the Department chart container.
                                     */
                                    const canvas = document.getElementById('departmentChart');

                                    if (!canvas || departmentLabels.length === 0) {
                                        return;
                                    }

                                    const chartContainer = canvas.parentElement;

                                    chartContainer.style.height = Math.max(
                                        380,
                                        departmentLabels.length * 38 + 70
                                    ) + 'px';

                                    new Chart(canvas, {
                                        type: 'bar',

                                        data: {
                                            labels: departmentLabels.map(shortenDepartmentName),

                                            datasets: [{
                                                label: 'Unique voters',
                                                data: departmentCounts,

                                                /*
                                                 * A separate solid colour for every bar.
                                                 */
                                                backgroundColor: departmentCounts.map(function(_, index) {
                                                    return departmentPalette[
                                                        index % departmentPalette.length
                                                    ];
                                                }),

                                                borderColor: departmentCounts.map(function(_, index) {
                                                    return departmentPalette[
                                                        index % departmentPalette.length
                                                    ];
                                                }),

                                                borderWidth: 1,
                                                borderRadius: 5,

                                                /*
                                                 * Slightly darken bars on hover.
                                                 */
                                                hoverBackgroundColor: departmentCounts.map(function(_, index) {
                                                    return departmentPalette[
                                                        index % departmentPalette.length
                                                    ];
                                                })
                                            }]
                                        },

                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,

                                            /*
                                             * Horizontal bars prevent long department names
                                             * from becoming slanted below the chart.
                                             */
                                            indexAxis: 'y',

                                            plugins: {
                                                legend: {
                                                    display: false
                                                },

                                                tooltip: {
                                                    callbacks: {
                                                        /*
                                                         * Full, original department name on hover.
                                                         */
                                                        title: function(context) {
                                                            return departmentLabels[
                                                                context[0].dataIndex
                                                            ];
                                                        },

                                                        label: function(context) {
                                                            return 'Unique voters: ' +
                                                                Number(context.raw).toLocaleString();
                                                        }
                                                    }
                                                }
                                            },

                                            scales: {
                                                x: {
                                                    beginAtZero: true,

                                                    ticks: {
                                                        precision: 0
                                                    }
                                                },

                                                y: {
                                                    ticks: {
                                                        autoSkip: false,

                                                        font: {
                                                            size: window.innerWidth < 576 ? 10 : 12
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>

                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Voters by Programme</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="programChart"></canvas>
                                    </div>
                                </div>

                                <script>
                                    new Chart(document.getElementById('programChart'), {
                                        type: 'bar',
                                        data: {
                                            labels: @json($data['votersByProgram']->keys()->values()),
                                            datasets: [{
                                                label: 'Voters',
                                                data: @json($data['votersByProgram']->values()->values())
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>


                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Voters by Programme Type</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="programTypeChart"></canvas>
                                    </div>
                                </div>

                                <script>
                                    new Chart(document.getElementById('programTypeChart'), {
                                        type: 'doughnut',
                                        data: {
                                            labels: @json($data['votersByProgramType']->keys()->values()),
                                            datasets: [{
                                                data: @json($data['votersByProgramType']->values()->values())
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: {
                                                legend: {
                                                    position: 'bottom'
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>





                            <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Voters by Level</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="levelChart"></canvas>
                                    </div>
                                </div>

                                <script>
                                    new Chart(document.getElementById('levelChart'), {
                                        type: 'bar',
                                        data: {
                                            labels: @json($data['votersByLevel']->keys()->values()),
                                            datasets: [{
                                                label: 'Voters',
                                                data: @json($data['votersByLevel']->values()->values())
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>
                            <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Votes by Position</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="positionChart"></canvas>
                                    </div>
                                </div>

                                <script>
                                    new Chart(document.getElementById('positionChart'), {
                                        type: 'bar',
                                        data: {
                                            labels: @json($data['votesByPosition']->keys()->values()),
                                            datasets: [{
                                                label: 'Votes',
                                                data: @json($data['votesByPosition']->values()->values())
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Voters by Gender</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="genderChart"></canvas>
                                    </div>
                                </div>

                                <script>
                                    new Chart(document.getElementById('genderChart'), {
                                        type: 'bar',
                                        data: {
                                            labels: @json($data['votersByGender']->keys()->values()),
                                            datasets: [{
                                                label: 'Voters',
                                                data: @json($data['votersByGender']->values()->values())
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>


                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Votes by Candidate</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="candidateChart"></canvas>
                                    </div>
                                </div>

                                <script>
                                    new Chart(document.getElementById('candidateChart'), {
                                        type: 'bar',
                                        data: {
                                            labels: @json($data['votesByCandidate']->keys()->values()),
                                            datasets: [{
                                                label: 'Votes',
                                                data: @json($data['votesByCandidate']->values()->values())
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>



                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong>Voting Trend</strong>
                                    </div>

                                    <div class="card-body">
                                        <canvas id="votingTrendChart"></canvas>
                                    </div>
                                </div>

                                <script>
                                    new Chart(document.getElementById('votingTrendChart'), {
                                        type: 'line',
                                        data: {
                                            labels: @json($data['votingTrend']->keys()->values()),
                                            datasets: [{
                                                label: 'Unique Voters',
                                                data: @json($data['votingTrend']->values()->values()),
                                                tension: 0.3,
                                                fill: true
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    @endif
                    {{-- charts end --}}
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary"
                            href="{{ route('viewElection', ['uuid' => $data['election']->uuid]) }}">
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
