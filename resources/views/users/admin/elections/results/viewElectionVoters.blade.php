@extends('layouts.userLayout')
@section('title', 'Votes - ' . $data['candidate']->theCandidate->memberPersonalProfile->first_name . ' - ' .
    $data['candidate']->position->position)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.elections.include.view_election_inner_nav')
            </div>
            <div class="">
                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @php
                                $data['member'] = $data['candidate']->theCandidate;
                            @endphp
                            @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')
                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container pb-5">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    {{ $data['candidate']->theCandidate->memberPersonalProfile->first_name }} Votes - <span
                                        class="text-uppercase">
                                        {{ strtolower($data['election']->title) }}
                                        {{ $data['election']->academic_session . '/' . $data['election']->academic_session + 1 }}</span>
                                </h4>
                                <div class="card-body p-4 row col-md-12">

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Election:</span>
                                        <span class="text-black">
                                            {{ $data['election']->title }}
                                            {{ $data['election']->academic_session . '/' . $data['election']->academic_session + 1 }}
                                            <a href="{{ route('viewElection', ['uuid' => $data['election']->uuid]) }}"
                                                class="badge bg-secondary text-white" title="View Initiator">
                                                <i class="fas fa-link"></i>
                                            </a>

                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black">
                                            {{ $data['election']->description }}
                                        </span>
                                    </div>
                                    <hr>
                                    <h5 class="text-blue text-decoration-underline fw-bold mt-3">Candidate</h5>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Name:</span>
                                        <span class="text-black">
                                            {{ $data['candidate']->theCandidate->memberPersonalProfile->first_name }}
                                            {{ $data['candidate']->theCandidate->memberPersonalProfile->middle_name }}
                                            {{ $data['candidate']->theCandidate->memberPersonalProfile->last_name }}
                                            <a href="{{ route('viewMember', ['uuid' => $data['candidate']->theCandidate->uuid]) }}"
                                                class="badge bg-secondary text-white" title="View Initiator">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Program:</span>
                                        <span class="text-black">
                                            {{ $data['candidate']->program->name }}
                                            ({{ $data['candidate']->programType->type_name }})
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Faculty:</span>
                                        <span class="text-black">
                                            {{ $data['candidate']->faculty->name }} @if ($data['candidate']->faculty->acronym)
                                                ({{ $data['candidate']->faculty->acronym }})
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Department:</span>
                                        <span class="text-black">
                                            {{ $data['candidate']->department->name }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Level:</span>
                                        <span class="text-black">
                                            {{ $data['candidate']->level_at_application }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Application:</span>
                                        <span class="text-black text-capitalize">
                                            <a href="{{ route('viewElectionCandidateApplication', ['uuid' => $data['candidate']->uuid]) }}"
                                                class="badge bg-secondary text-white" title="View Initiator">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </span>
                                    </div>
                                    <h5 class="text-blue text-decoration-underline fw-bold mt-3">Position</h5>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Position Aspired:</span>
                                        <span class="text-black">
                                            {{ $data['candidate']->position->position }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black">
                                            {{ $data['candidate']->position->description }}
                                        </span>
                                    </div>
                                    {{-- ############################################################################## --}}
                                    @if (Auth::user()->account_type == '90')
                                        <hr>
                                        <form class="form my_form" method="POST"
                                            action="{{ route('manualSyncElectionTemp') }}" enctype="multipart/form-data">
                                            @csrf <div class="form-row mt-5 mb-5">
                                                <div class="col m-1">
                                                    <input type="hidden" class="form-control" name="election_id"
                                                        value="{{ $data['election']->election_id }}">
                                                </div>
                                                <div class="col m-1">
                                                    <input type="hidden" class="form-control" name="position"
                                                        value="{{ $data['candidate']->position->id }}">
                                                </div>
                                                <div class="col m-1">
                                                    <input type="hidden" class="form-control" name="candidate"
                                                        value="{{ $data['candidate']->application_id }}">
                                                </div>
                                                <div class="row col-md-12">
                                                    <div class="col co-md-6 m-1">
                                                        <input type="text" class="form-control" name="voter"
                                                            value="{{ old('voter') }}" placeholder="Voter">
                                                    </div>
                                                    <div class="col co-md-6 m-1">
                                                        <input type="text" class="form-control"
                                                            name="level_at_application"
                                                            value="{{ old('level_at_application') }}" placeholder="Level">
                                                    </div>
                                                    <div class="col co-md-6 m-1">
                                                        <input type="text" class="form-control" name="created_at"
                                                            value="{{ old('created_at') }}" placeholder="created_at">
                                                    </div>
                                                    <div class="col co-md-6 m-1">
                                                        <input type="text" class="form-control" name="updated_at"
                                                            value="{{ old('updated_at') }}" placeholder="updated_at">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="green-button submit-btn">
                                                <span class="btn-text">
                                                    Submit
                                                </span>

                                                <span class="btn-loading" style="display:none;">
                                                    <i class="fas fa-spinner fa-spin"></i> Processing...
                                                </span>
                                            </button>
                                        </form>
                                        <hr>
                                    @endif
                                    {{-- ############################################################################## --}}

                                </div>
                            </div>
                            {{-- standard votes start --}}
                            @if ($data['election']->voting_method == 'standard')
                                <div class="my_form_container mt-4">
                                    <div class=" pb-4">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered p-3">
                                                    <thead class="justify-content-center text-center align-middle">
                                                        <tr>
                                                            <th scope="col" style="width: 10px" class="table_header">
                                                                S/N
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Voter
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Date/Time
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    @if ($data['standardVotes']->isEmpty())
                                                        <tr>
                                                            <td colspan="3" class="text-center">
                                                                <h3 class="p-1 m-1 text-red">
                                                                    No Records Found!
                                                                </h3>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tbody>
                                                            @foreach ($data['standardVotes'] as $voter)
                                                                <tr class=" align-middle text-center">
                                                                    <th>{{ $loop->iteration }}</th>
                                                                    <td>
                                                                        <div class="d-flex align-items-center gap-3">
                                                                            {{-- Avatar --}}
                                                                            <div class="flex-shrink-0">
                                                                                @php
                                                                                    $user = $voter->theVoter;
                                                                                @endphp
                                                                                @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                            </div>

                                                                            {{-- Name & Code --}}
                                                                            <div
                                                                                class="flex-grow-1 min-width-0 text-center">
                                                                                @php
                                                                                    $middleInitial = $voter->theVoter
                                                                                        ->memberPersonalProfile
                                                                                        ->middle_name
                                                                                        ? strtoupper(
                                                                                                substr(
                                                                                                    $voter->theVoter
                                                                                                        ->memberPersonalProfile
                                                                                                        ->middle_name,
                                                                                                    0,
                                                                                                    1,
                                                                                                ),
                                                                                            ) . '. '
                                                                                        : '';
                                                                                @endphp
                                                                                <p
                                                                                    class="fw-bold mb-0 text-truncate text-capitalize">
                                                                                    {{ strtolower($voter->theVoter->memberPersonalProfile->first_name) . ' ' . $middleInitial . strtolower($voter->theVoter->memberPersonalProfile->last_name) }}
                                                                                    | <span
                                                                                        class="badge bg-secondary-subtle text-black">
                                                                                        {{ $voter->level_at_application }}
                                                                                    </span>
                                                                                </p>
                                                                                <p
                                                                                    class="text-muted mb-0 small text-truncate">


                                                                                    <span class="text-capitalize">

                                                                                        {{ strtoupper($voter->theVoter->memberAcademicInfo->matric_number) }}
                                                                                    </span>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        {{ $voter->created_at->format('l, d F Y \a\t h:i A') }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    @endif
                                                </table>
                                                {{-- Pagination --}}
                                                @php
                                                    $paginatedArray = $data['standardVotes'];
                                                @endphp

                                                @include('layouts.include.pagination')
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            @endif
                            {{-- standard votes end --}}
                            {{-- monetary votes start --}}
                            @if ($data['election']->voting_method == 'monetary')
                                <div class="my_form_container mt-4">
                                    <div class=" pb-4">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered p-3">
                                                    <thead class="justify-content-center text-center align-middle">
                                                        <tr>
                                                            <th scope="col" style="width: 10px" class="table_header">
                                                                S/N
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Voter
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                No of Votes
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Amount Paid
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Date/Time
                                                            </th>
                                                            <th scope="col" class="table_header">
                                                                Vote Count
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    @if ($data['monetaryVotes']->isEmpty())
                                                        <tr>
                                                            <td colspan="6" class="text-center">
                                                                <h3 class="p-1 m-1 text-red">
                                                                    No Records Found!
                                                                </h3>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tbody>
                                                            @foreach ($data['monetaryVotes'] as $voter)
                                                                <tr class=" align-middle text-center">
                                                                    <th>{{ $loop->iteration }}</th>
                                                                    <td>
                                                                        @if ($voter->voter)
                                                                            {{ $voter->voter->memberPersonalProfile->first_name }}
                                                                            {{ $voter->voter->memberPersonalProfile->last_name }}
                                                                        @else
                                                                            {{ $voter->voters_name }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        {{ $voter->no_of_votes }}
                                                                    </td>

                                                                    <td>
                                                                        ₦{{ number_format($voter->amount_paid, 2) }}
                                                                    </td>
                                                                    <td>
                                                                        {{ $voter->created_at->format('l, d F Y \a\t h:i A') }}
                                                                    </td>
                                                                    <td>
                                                                        @if ($voter->votes_counted == true)
                                                                            <span
                                                                                class="badge bg-success-subtle text-success">Counted</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-danger-subtle text-danger">Not-Counted</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    @endif
                                                </table>
                                                {{-- Pagination --}}
                                                @php
                                                    $paginatedArray = $data['monetaryVotes'];
                                                @endphp

                                                @include('layouts.include.pagination')
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            @endif
                            {{-- monetary votes end --}}

                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary"
                            href="{{ route('viewElectionResultForPosition', ['election' => $data['election']->uuid, 'position' => $data['candidate']->position->uuid]) }}">
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
