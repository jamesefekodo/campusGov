@extends('layouts.userLayout')
@section('title', 'Election - ' . $data['election']->title)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.member.elections.include.view_election_inner_nav')
            </div>
            <div class="">
                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="my_form_container pb-5">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Election - <span class="text-uppercase">{{ strtolower($data['election']->title) }}
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
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {

                                                        const deadline = new Date(
                                                            "{{ \Carbon\Carbon::parse($data['election']->form_sales_ends)->toIso8601String() }}"
                                                        ).getTime();

                                                        const countdown = document.getElementById('registration-countdown-large');

                                                        if (!countdown) {
                                                            return;
                                                        }

                                                        function updateCountdown() {

                                                            const now = new Date().getTime();
                                                            const distance = deadline - now;

                                                            if (distance <= 0) {
                                                                countdown.innerHTML = 'Registration has ended.';
                                                                return;
                                                            }

                                                            const days = Math.floor(
                                                                distance / (1000 * 60 * 60 * 24)
                                                            );

                                                            const hours = Math.floor(
                                                                (distance % (1000 * 60 * 60 * 24)) /
                                                                (1000 * 60 * 60)
                                                            );

                                                            const minutes = Math.floor(
                                                                (distance % (1000 * 60 * 60)) /
                                                                (1000 * 60)
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
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">One candidate can apply for multiple
                                            positions?:</span>
                                        <span class="text-black text-capitalize">
                                            @switch($data['election']->one_candidate_multiple_positions)
                                                @case(true)
                                                    Yes
                                                @break

                                                @default
                                                    No
                                                @break
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Public Visibility:</span>
                                        <span class="text-black text-capitalize">
                                            @switch($data['election']->public_visibility)
                                                @case(true)
                                                    <span class="badge bg-primary-subtle text-primary">Enabled</span>
                                                    Guests can see election details.
                                                @break

                                                @default
                                                    <span class="badge bg-secondary-subtle text-secondary">Disabled</span>
                                                    Only signed-in members can see election details.
                                                @break
                                            @endswitch
                                        </span>
                                    </div>
                                    @if ($data['election']->status == 'registration')
                                        <hr class="mt-3">
                                        <div class="alert alert-danger text-center py-4 px-4 shadow-sm" role="alert">

                                            <div class="mb-2">
                                                <i class="fas fa-hourglass-half fa-2x me-2"></i>
                                                <strong class="fs-4">Registration Closes In</strong>
                                            </div>

                                            <div id="registration-countdown-large" class="fw-bold display-5 mb-3">
                                            </div>

                                            <hr>

                                            <p class="fs-5 mb-0">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Check your desired position, review its requirements carefully,
                                                and submit your application before registration closes.
                                            </p>

                                        </div>
                                        <hr class="mb-3">
                                    @endif


                                    {{-- position starts --}}
                                    <div class="col-md-12 mt-4">
                                        <h5 class="text-blue text-decoration-underline fw-bold">Positions</h5>
                                        @if ($data['election']->positions->isNotEmpty())
                                            <div class="col-md-12">
                                                @foreach ($data['election']->positions as $position)
                                                    <h4> <span
                                                            class="badge bg-secondary-subtle text-secondary fs-6">{{ $loop->iteration }}</span>
                                                        {{ $position->position }}</h4>
                                                    <div class="text-black text-capitalize mb-5">
                                                        <div class="">
                                                            <span class="fw-bold text-muted">Description:</span>
                                                            {{ $position->description }}
                                                        </div>
                                                        <div class="">
                                                            <span class="fw-bold text-muted">Application
                                                                Fee:</span>
                                                            ₦{{ number_format($position->application_fee, 2) }}
                                                        </div>
                                                        <div class="">
                                                            @php
                                                                $qualification = canApplyForPosition(
                                                                    Auth::user(),
                                                                    $position,
                                                                );
                                                            @endphp

                                                            @if ($data['election']->status == 'registration')
                                                                <button class="btn btn-primary rounded-3 m-1"
                                                                    {{ !$qualification['qualified'] ? 'disabled' : '' }}
                                                                    data-toggle="modal"
                                                                    data-target="#memberSelfApplyForPositionModal_{{ $position->id }}"
                                                                    style="cursor: pointer;">
                                                                    <i class="fas fa-solid fa-file-pen me-1"></i>Apply for
                                                                    this
                                                                    position
                                                                </button>
                                                            @endif
                                                            {{-- Nomination is disabled --}}
                                                            {{-- <button class="btn btn-warning rounded-3 m-1"
                                                                data-toggle="modal"
                                                                data-target="#editQualifiedCandidateGoupModal_{{ $group->id }}"
                                                                style="cursor: pointer;">
                                                                <i class="fas fa-hand-holding-heart me-1"></i>Nominate
                                                                Someone for this positino
                                                            </button> --}}

                                                            @include('users.member.elections.include.memberSelfApplyForPositionModal')
                                                            {{-- ############################################# --}}


                                                            {{-- @if ($qualification['qualified'])
                                                                                            <span class="badge bg-success">
                                                                                                Qualified
                                                                                            </span>
                                                                                        @else
                                                                                            <span class="badge bg-danger">
                                                                                                Not Qualified
                                                                                            </span>
                                                                                            <br>
                                                                                            <small class="text-danger">
                                                                                                {{ $qualification['message'] }}
                                                                                            </small>
                                                                                        @endif --}}
                                                            {{-- ################################################### --}}
                                                        </div>
                                                        <div class="">
                                                            <span class="fw-bold text-muted">Requirements:</span>
                                                            @if ($position->candidateGroups->isEmpty())
                                                                <div class="px-3 pt-3 table-responsive">
                                                                    <table class="table">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    *
                                                                                </td>
                                                                                <td colspan="3">
                                                                                    All student members can apply
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="px-3 pt-3 table-responsive">
                                                                    <table class="table">
                                                                        <tbody>
                                                                            @foreach ($position->candidateGroups as $group)
                                                                                <tr class="text-black">
                                                                                    <td>
                                                                                        {{ $loop->iteration }}
                                                                                    </td>
                                                                                    <td class="text-start">
                                                                                        @if ($group->theProgram)
                                                                                            {{ $group->theProgram->name }}
                                                                                            ({{ $group->theProgram->short_name }})

                                                                                            @if ($group->programType)
                                                                                                {{ $group->programType->type_name }}
                                                                                            @endforelse
                                                                                        @else
                                                                                            All programs
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        @if ($group->theFaculty)
                                                                                            {{ $group->theFaculty->name }}
                                                                                            ({{ $group->theFaculty->acronym }})
                                                                                        @else
                                                                                            All Faculties
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        @if ($group->theDepartment)
                                                                                            {{ $group->theDepartment->name }}
                                                                                        @else
                                                                                            All Departments
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        @if ($group->start_level)
                                                                                            Year
                                                                                            {{ $group->start_level }}
                                                                                            @if ($group->end_level)
                                                                                                - Year
                                                                                                {{ $group->end_level }}
                                                                                            @endif
                                                                                        @else
                                                                                            All Level
                                                                                        @endif
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                    </div>
                                    {{-- position ends --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('memberNewElections') }}">
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
