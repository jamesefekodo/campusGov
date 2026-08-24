@extends('layouts.userLayout')
@section('title', 'Voting - ' . $data['election']->title)
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
                                    <div class="col-md-12 mt-2 fs-4">

                                        @if ($data['election']->status == 'voting')
                                            @if (canUserVoteInElection($data['election'], auth()->user()))
                                                <div class="alert alert-success d-flex align-items-center" role="alert">

                                                    <div class="text-center w-100">
                                                        <i class="fas fa-check-circle fa-lg me-3"></i>
                                                        <strong>You are eligible to vote in this election.</strong>
                                                        <br>
                                                        Please review the available positions and cast your vote for each
                                                        position.
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning d-flex align-items-center" role="alert">

                                                    <div class="text-center w-100">
                                                        <i class="fas fa-info-circle fa-lg me-3"></i>
                                                        <strong>You are not eligible to vote in this election.</strong>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <hr>

                            @if (Route::is('memberElectionVoting'))
                                {{-- voting index start --}}
                                <div class="table_head m-2">
                                    <div class="table_head_item col-md-12">
                                        <div class="table_title text-center">
                                            Select a position to vote
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cols-1 row-cols-md-3 g-4 summary_cards_container m-2"
                                    id="summaryContent">
                                    @if ($data['election']->positions->count() > 0)
                                        @foreach ($data['election']->positions as $position)
                                            <a
                                                href="{{ route('memberViewElectionPositionCandidatesForVote', ['uuid' => $position->uuid]) }}">
                                                <div class="col">
                                                    <div class="card shadow-sm summary_card">
                                                        <div class="card-body">
                                                            <i class="fas fa-user-tie fa-2x"></i>
                                                            <h4 class="card-title">{{ $position->position }}</h4>
                                                            @if ($position->qualifiedCandidates->isNotEmpty())
                                                                <span class="badge bg-primary text-white fs-5">
                                                                    {{ $position->qualifiedCandidates->count() }}
                                                                    Candidate(s)
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger text-white">N/A</span>
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
                                {{-- voting index end --}}
                            @endif

                            @if (Route::is('memberViewElectionPositionCandidatesForVote'))
                                {{-- voting - position start --}}
                                <div class="table_head m-2">
                                    <div class="table_head_item col-md-12">
                                        <div class="table_title text-center">
                                            Vote:: {{ $data['position']->position }}
                                        </div>
                                    </div>
                                </div>

                                <section class="mini_card_container">
                                    @foreach ($data['position']->qualifiedCandidates as $candidate)
                                        @if (canUserVoteInElection($data['election'], auth()->user()) && !$data['hasAlreadyVoted'])
                                            <a href="#" data-toggle="modal"
                                                data-target="#standardVoteNowModal_{{ $candidate->id }}">
                                            @else
                                                <a href="#">
                                        @endif
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
                                                    {{-- {{ $candidate->theCandidate->memberPersonalProfile->last_name }} --}}
                                                </div>

                                                <div class="mini-card-content">
                                                    <div class="mini_card_info">
                                                        <p class="mini_card_info_list">
                                                            @if ($candidate->theCandidate->memberPersonalProfile)
                                                                {{ $candidate->theCandidate->memberPersonalProfile->first_name }}
                                                                {{ $candidate->theCandidate->memberPersonalProfile->middle_name }}
                                                                {{ $candidate->theCandidate->memberPersonalProfile->last_name }}
                                                            @endif
                                                            {{-- {{ $candidate->program->short_name }}
                                                                ({{ $candidate->programType->type_name }}) --}}
                                                        </p>
                                                        @if (canUserVoteInElection($data['election'], auth()->user()) && !$data['hasAlreadyVoted'])
                                                            <span class="badge bg-red text-white fs-6 mt-2"
                                                                data-toggle="modal"
                                                                data-target="#standardVoteNowModal_{{ $candidate->id }}"
                                                                style="cursor: pointer;">
                                                                <i class="fas fa-thumbs-up me-1"></i> Vote
                                                            </span>
                                                        @else
                                                            <span
                                                                class="badge bg-secondary text-black text-white fs-6 mt-2"
                                                                style="cursor: pointer;">
                                                                <i class="fas fa-thumbs-up me-1"></i> Vote
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </a>
                                    @endforeach

                                    @foreach ($data['position']->qualifiedCandidates as $candidate)
                                        @include('users.member.elections.include.standardVoteNowModal')
                                    @endforeach
                                </section>
                                {{-- voting - position end --}}
                            @endif

                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary"
                            href="{{ route('memberElectionVoting', ['uuid' => $data['election']->uuid]) }}">
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
