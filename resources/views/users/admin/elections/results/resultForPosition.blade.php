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
                                    <hr>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Position:</span>
                                        <span class="text-black text-capitalize">
                                            {{ $data['position']->position }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black text-capitalize">
                                            {{ $data['position']->description }}
                                        </span>
                                    </div>


                                </div>

                            </div>
                            <hr>
                            {{-- view applicand start --}}
                            <div class="table_head m-2">
                                <div class="table_head_item col-md-12">
                                    <div class="table_title text-center">
                                        Candidates for the position of:: {{ $data['position']->position }}
                                    </div>
                                </div>
                            </div>

                            <section class="mini_card_container">
                                @foreach ($data['position']->qualifiedCandidates as $candidate)
                                    <a href="#" data-toggle="modal" data-target="#">
                                        <div class="mini_card">
                                            <div class="mini_card_body">
                                                <div class="mini-card-content">
                                                    @if (
                                                        $candidate->theCandidate->photo &&
                                                            file_exists(public_path(
                                                                    'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo)))
                                                        <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo) }}"
                                                            alt="{{ $candidate->theCandidate->username }}">
                                                    @else
                                                        <img src="{{ asset('system/images/default_profile_pic.webp') }}"
                                                            alt="Default Picture">
                                                    @endif
                                                </div>
                                                <div class="mini_card_header">
                                                    {{ $candidate->theCandidate->memberPersonalProfile->first_name }}
                                                    {{ $candidate->theCandidate->memberPersonalProfile->last_name }}
                                                </div>

                                                <div class="mini-card-content">
                                                    <div class="mini_card_info">
                                                        <p class="mini_card_info_list">
                                                            {{-- @if ($candidate->theCandidate->memberPersonalProfile)
                                                                {{ $candidate->theCandidate->memberPersonalProfile->first_name }}
                                                                {{ $candidate->theCandidate->memberPersonalProfile->middle_name }}
                                                                {{ $candidate->theCandidate->memberPersonalProfile->last_name }}
                                                            @endif --}}
                                                            {{ $candidate->program->short_name }}
                                                            ({{ $candidate->programType->type_name }})
                                                        </p>

                                                        <span class="badge bg-red text-white fs-6 mt-1">
                                                            @if ($data['election']->voting_method == 'monetary')
                                                                {{ $candidate->totalMonetaryVote() }}
                                                            @else
                                                                {{ $candidate->standardVotes->count() }}
                                                            @endif
                                                            <i class="fas fa-thumbs-up me-1"></i>
                                                        </span>

                                                        @if (Auth::user()->hasPermission('voters.read'))
                                                            <span class="badge bg-blue text-white fs-6 mt-1" role="button"
                                                                style="cursor: pointer;"
                                                                onclick="window.location.href='{{ route('viewElectionVoters', ['uuid' => $candidate->uuid]) }}'">
                                                                <i class="fas fa-eye me-1"></i> View
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach

                            </section>
                            @foreach ($data['position']->qualifiedCandidates as $candidate)
                                @include('users.admin.elections.candidates.include.viewElectionCandidate')
                            @endforeach

                            {{-- view applicant end --}}

                            {{-- result chart start --}}
                            <div class="card shadow-sm m-4">
                                <div class="card-header">
                                    <strong>Results - {{ $data['position']->position ?? '' }}</strong>
                                </div>

                                <div class="card-body">
                                    @foreach ($data['candidateResults'] as $candidate)
                                        @php
                                            $percent =
                                                $data['totalVotes'] > 0
                                                    ? round(($candidate->voteCount / $data['totalVotes']) * 100, 1)
                                                    : 0;

                                            $isLeading =
                                                $candidate->voteCount > 0 &&
                                                $candidate->voteCount === $data['highestVotes'];

                                            // How many candidates share the highest vote count (for tie detection)
                                            $tiedCount = $data['candidateResults']
                                                ->where('voteCount', $data['highestVotes'])
                                                ->count();
                                            $isTie = $isLeading && $tiedCount > 1;

                                            $isCompleted = $data['election']->status === 'completed';
                                        @endphp

                                        <div
                                            class="d-flex align-items-center mb-4 p-2 rounded {{ $isLeading ? 'border border-success bg-success bg-opacity-10' : '' }}">
                                            <div class="position-relative me-3">
                                                @if (
                                                    $candidate->theCandidate->photo &&
                                                        file_exists(public_path(
                                                                'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo)))
                                                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo) }}"
                                                        alt="{{ $candidate->theCandidate->username }}"
                                                        class="rounded-circle"
                                                        style="width: 64px; height: 64px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('system/images/default_profile_pic.webp') }}"
                                                        alt="Default Picture" class="rounded-circle"
                                                        style="width: 64px; height: 64px; object-fit: cover;">
                                                @endif

                                                @if ($isLeading)
                                                    @if ($isCompleted)
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success text-white"
                                                            title="{{ $isTie ? 'Tied winner' : 'Winner' }}">
                                                            <i class="fas fa-crown"></i>
                                                        </span>
                                                    @else
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary text-white"
                                                            title="Currently leading">
                                                            <i class="fas fa-arrow-up"></i>
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong>
                                                        {{ $candidate->theCandidate->memberPersonalProfile->first_name ?? $candidate->theCandidate->username }}
                                                        {{ $candidate->theCandidate->memberPersonalProfile->last_name ?? '' }}

                                                        @if ($isCompleted && $isLeading)
                                                            <span class="badge bg-success ms-2">
                                                                {{ $isTie ? 'Tied Winner' : 'Winner' }}
                                                            </span>
                                                        @endif
                                                    </strong>
                                                    <span>{{ $candidate->voteCount }} votes
                                                        ({{ $percent }}%)</span>
                                                </div>

                                                <div class="progress mt-1" style="height: 10px;">
                                                    <div class="progress-bar {{ $isLeading ? 'bg-success' : 'bg-primary' }}"
                                                        role="progressbar" style="width: {{ $percent }}%;"
                                                        aria-valuenow="{{ $percent }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($data['candidateResults']->isEmpty())
                                        <p class="text-danger text-center fs-2 my-5">No records found!</p>
                                    @endif
                                </div>
                            </div>
                            {{-- result chart end --}}
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary"
                            href="{{ route('electionResults', ['uuid' => $data['election']->uuid]) }}">
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
