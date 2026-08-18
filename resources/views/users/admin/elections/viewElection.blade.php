@extends('layouts.userLayout')
@section('title', 'Election - ' . $data['election']->title)
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
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Initiated:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['election']->initiated_at)
                                                    {{ \Carbon\Carbon::parse($data['election']->initiated_at)->format('l, d F, Y h:i A') }}
                                                @endif
                                                -
                                                @if ($data['election']->initiated_by)

                                                    @if ($data['election']->initiatedBy?->memberPersonalProfile)
                                                        {{ trim(
                                                            $data['election']->initiatedBy->memberPersonalProfile->first_name .
                                                                ' ' .
                                                                ($data['election']->initiatedBy->memberPersonalProfile->middle_name
                                                                    ? $data['election']->initiatedBy->memberPersonalProfile->middle_name . ' '
                                                                    : '') .
                                                                $data['election']->initiatedBy->memberPersonalProfile->last_name,
                                                        ) }}
                                                    @else
                                                        {{ $data['election']->initiated_by }}
                                                    @endif

                                                    @if ($data['election']->initiatedBy)
                                                        <a href="{{ route('viewMember', ['uuid' => $data['election']->initiatedBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Initiator">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif

                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Processed:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['election']->approved_at)
                                                    {{ \Carbon\Carbon::parse($data['election']->approved_at)->format('l, d F, Y h:i A') }}
                                                @endif
                                                -
                                                @if ($data['election']->processed_by)

                                                    @if ($data['election']->processedBy?->memberPersonalProfile)
                                                        {{ trim(
                                                            $data['election']->processedBy->memberPersonalProfile->first_name .
                                                                ' ' .
                                                                ($data['election']->processedBy->memberPersonalProfile->middle_name
                                                                    ? $data['election']->processedBy->memberPersonalProfile->middle_name . ' '
                                                                    : '') .
                                                                $data['election']->processedBy->memberPersonalProfile->last_name,
                                                        ) }}
                                                    @else
                                                        {{ $data['election']->processed_by }}
                                                    @endif

                                                    @if ($data['election']->processedBy)
                                                        <a href="{{ route('viewMember', ['uuid' => $data['election']->processedBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Approver">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif

                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Approval:</span>
                                        <span class="text-black">
                                            @switch($data['election']->approval_status)
                                                @case('pending')
                                                    <span class="badge bg-primary-subtle text-primary">Pending</span>
                                                @break

                                                @case('approved')
                                                    <span class="badge bg-success-subtle text-success">Approved</span>
                                                @break

                                                @case('cancelled')
                                                    <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                                    @breaku
                                                @case('declined')
                                                    <span class="badge bg-danger-subtle text-danger">Declined</span>
                                                @break

                                                @default
                                                    <span
                                                        class="badge bg-danger-subtle text-danger">{{ $data['election']->approval_status }}</span>
                                            @endswitch
                                        </span>
                                    </div>

                                    <hr>
                                    @php
                                        $showCompletePage = true;
                                        $showAwaitingVerificationPage = false;
                                        $showVerificationProcessingMenu = false;

                                        if ($data['election']->status == 'awaiting_approval') {
                                            if (Auth::user()->hasPermission('elections.approve')) {
                                                $showVerificationProcessingMenu = true;
                                            } else {
                                                $showCompletePage = false;
                                                $showAwaitingVerificationPage = true;
                                            }
                                        }
                                    @endphp

                                    @if ($showAwaitingVerificationPage == true)
                                        <div class="alert alert-info m-3" role="alert">
                                            <h4 class="alert-heading">Pending Approval</h4>
                                            <p class="my-1">This election is pending approval. Kindly download the
                                                consent
                                                and
                                                awareness form, take it to the relevant stakeholders in the school to obtain
                                                their agreement, and afterwards contact an administrator with permission to
                                                approve elections for approval on the portal.</p>
                                            <p class="my-1">
                                                If there is a need to make any change before approval, please contact an
                                                administrator with permission to approve elections for assistance.
                                            </p>
                                        </div>
                                    @endif

                                    @if ($showVerificationProcessingMenu == true)
                                        <div class="alert alert-info m-3" role="alert">
                                            <h4 class="alert-heading">Pending Approval</h4>
                                            <p class="my-1">This election is pending approval. Ensure that all necessary
                                                stakeholders have signed the consent and awareness forms and that all
                                                applicable school compliance requirements have been met. Once completed,
                                                approve or decline the election from the menu below.</p>


                                            <button class="btn btn-block btn-primary mt-5 w-100 text-uppercase"
                                                data-toggle="modal" data-target="#electionApprovalModal"> <i
                                                    class="fas fa-gavel me-2"></i>
                                                Click here to approve/decline election request</button>

                                            @include('users.admin.elections.include.electionApprovalModal')
                                        </div>
                                    @endif

                                    @if ($showCompletePage == true)
                                        @unless (in_array($data['election']->status, ['completed', 'cancelled', 'suspended', 'declined']))
                                            <div class="d-flex justify-content-end flex-wrap mt-3">
                                                @if (Auth::user()->hasPermission('elections.update'))
                                                    <button class="btn btn-warning m-1 d-flex align-items-center"
                                                        data-toggle="modal" data-target="#editElectionModal">
                                                        <i class="fas fa-edit me-2"></i>
                                                        <span>Update</span>
                                                    </button>
                                                @endif
                                            </div>

                                            @include('users.admin.elections.include.editElectionModal')
                                        @endunless
                                        <div class="row col-md-12">
                                            <div class="col-md-12 mt-4">
                                                <h5 class="text-blue text-decoration-underline fw-bold">Qualified Voters
                                                    Group</h5>
                                                @if ($data['election']->voterGroups->isEmpty())
                                                    <h5 class="m-2 text-center">
                                                        All members are qualified voters, regardless of their program,
                                                        faculty, or level. <br> <span class="text-danger"> To change this,
                                                            add
                                                            a specific set of members using the button below.
                                                    </h5>
                                                @else
                                                    <div class="px-3 pt-3 table-responsive">
                                                        <table class="table">
                                                            @foreach ($data['election']->voterGroups as $group)
                                                                <tbody>
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
                                                                                Year {{ $group->start_level }}
                                                                                @if ($group->end_level)
                                                                                    - Year {{ $group->end_level }}
                                                                                @endif
                                                                            @else
                                                                                All Level
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @unless (in_array($data['election']->status, ['completed', 'cancelled', 'suspended', 'declined']))
                                                                                @if (Auth::user()->hasPermission('elections.update'))
                                                                                    <span
                                                                                        class="badge bg-yellow text-black pe-2 ps-3 py-2 m-1 rounded-3"
                                                                                        data-toggle="modal"
                                                                                        data-target="#editQualifiedVoterGoupModal_{{ $group->id }}"
                                                                                        style="cursor: pointer;">
                                                                                        <i class="fas fa-edit me-1"></i>Update
                                                                                    </span>
                                                                                @endif
                                                                                @if (Auth::user()->hasPermission('elections.delete'))
                                                                                    <a href="javascript:void(0)"
                                                                                        class="badge bg-red text-white pe-2 ps-3 py-2 rounded-3 delete-btn m-1"
                                                                                        data-toggle="modal"
                                                                                        data-target="#deleteDurationOverride"
                                                                                        data-url="{{ route('removeQualifiedVoterGroup', ['uuid' => $group->uuid]) }}"
                                                                                        style="cursor: pointer;">
                                                                                        <i class="fas fa-trash me-1"></i>Remove
                                                                                    </a>
                                                                                @endif
                                                                            @endunless
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            @endforeach
                                                        </table>
                                                    </div>
                                                @endif


                                                @if (Auth::user()->hasPermission('elections.create'))
                                                    @unless (in_array($data['election']->status, ['completed', 'cancelled', 'suspended', 'declined']))
                                                        <div class="d-flex justify-content-center">
                                                            <div class="m-1">
                                                                <button class="btn btn-primary btn-sm table_btn"
                                                                    data-toggle="modal"
                                                                    data-target="#addNewQualifiedVotersGoupModal">
                                                                    <i class="fas fa-plus"></i> Add Voter Group</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @include('users.admin.elections.include.addNewQualifiedVotersGoupModal')
                                                @endunless

                                            </div>
                                            <hr>

                                            <div class="col-md-12 mt-4">
                                                <h5 class="text-blue text-decoration-underline fw-bold">Positions</h5>
                                                @if ($data['election']->positions->isEmpty())
                                                    <h5 class="m-2 text-center">
                                                        There are currently no positions. Click the button below to add a
                                                        new
                                                        position.
                                                    </h5>
                                                @else
                                                    <div class="col-md-12">
                                                        @foreach ($data['election']->positions as $position)
                                                            <h4> <span
                                                                    class="badge bg-secondary-subtle text-secondary fs-6">{{ $loop->iteration }}</span>
                                                                {{ $position->position }}</h4>
                                                            <div class="row text-black text-capitalize mb-5">
                                                                @include('users.admin.elections.include.addPositionQualifiedCandidateModal')
                                                                <div class="">
                                                                    <span class="fw-bold text-muted">Description:</span>
                                                                    {{ $position->description }}
                                                                </div>
                                                                <div class="">
                                                                    <span class="fw-bold text-muted">Reserved Cand.
                                                                        Groups:</span>
                                                                    @if ($position->candidateGroups->isEmpty())
                                                                        <h5 class="m-2 text-center">
                                                                            All members can apply for this position <br>
                                                                            <span class="text-danger">
                                                                                To specify requirements, click the buttons
                                                                                below.
                                                                        </h5>
                                                                    @else
                                                                        <div class="px-3 pt-3 table-responsive">
                                                                            <table class="table">
                                                                                @foreach ($position->candidateGroups as $group)
                                                                                    <tbody>
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
                                                                                            <td>
                                                                                                @unless (in_array($data['election']->status, ['completed', 'cancelled', 'suspended', 'declined']))
                                                                                                    @if (Auth::user()->hasPermission('elections.update'))
                                                                                                        <span
                                                                                                            class="badge bg-yellow text-black pe-2 ps-3 py-2 m-1 rounded-3"
                                                                                                            data-toggle="modal"
                                                                                                            data-target="#editQualifiedCandidateGoupModal_{{ $group->id }}"
                                                                                                            style="cursor: pointer;">
                                                                                                            <i
                                                                                                                class="fas fa-edit me-1"></i>Update
                                                                                                        </span>
                                                                                                    @endif
                                                                                                    @if (Auth::user()->hasPermission('elections.delete'))
                                                                                                        <a href="javascript:void(0)"
                                                                                                            class="badge bg-red text-white pe-2 ps-3 py-2 rounded-3 delete-btn m-1"
                                                                                                            data-toggle="modal"
                                                                                                            data-target="#deleteDurationOverride"
                                                                                                            data-url="{{ route('removeQualifiedCandidateGroup', ['uuid' => $group->uuid]) }}"
                                                                                                            style="cursor: pointer;">
                                                                                                            <i
                                                                                                                class="fas fa-trash me-1"></i>Remove
                                                                                                        </a>
                                                                                                    @endif
                                                                                                @endunless
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                @endforeach
                                                                            </table>
                                                                        </div>
                                                                        @foreach ($position->candidateGroups as $group)
                                                                            @include('users.admin.elections.include.editPositionQualifiedCandidateGoupModal')
                                                                        @endforeach
                                                                    @endif
                                                                    <div class="d-flex justify-content-end">
                                                                        <span
                                                                            class="badge bg-green text-white p-1 fs-6 mx-2 px-2"
                                                                            data-toggle="modal"
                                                                            data-target="#addPositionQualifiedCandidateModal_{{ $position->id }}"
                                                                            style="cursor: pointer;">
                                                                            <i class="fas fa-plus me-2"></i>Add New
                                                                            Requirement
                                                                            (qualified candidate group)
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif


                                                @unless (in_array($data['election']->status, ['completed', 'cancelled', 'suspended', 'declined']))
                                                    @if (Auth::user()->hasPermission('elections.create'))
                                                        <div class="d-flex justify-content-center">
                                                            <div class="m-1">
                                                                <button class="btn btn-primary btn-sm table_btn"
                                                                    data-toggle="modal" data-target="#addPositionModal">
                                                                    <i class="fas fa-plus"></i> Add New Position</button>
                                                            </div>
                                                        </div>

                                                        @include('users.admin.elections.include.addPositionModal')
                                                    @endif
                                                @endunless

                                            </div>
                                        </div>

                                    @endif
                                    @if ($data['election']->status == 'draft')
                                        <a href="{{ route('submitElectionForApproval', ['uuid' => $data['election']->uuid]) }}"
                                            class="btn btn-block btn-primary mt-5"> <i
                                                class="fas fa-paper-plane me-2"></i>
                                            Submit For Approval</a>
                                    @endif
                                    @if ($data['election']->status == 'awaiting_approval')
                                        <a href="{{ route('printElectionConsentAwarenessForm', ['uuid' => $data['election']->uuid]) }}"
                                            class="btn btn-block btn-danger mt-5"> <i class="fas fa-print me-2"></i>
                                            Print Consent/Awareness Form</a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('allElections') }}">
                            <i class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>

                </div>

            </div>
        </div>

        @if ($data['election']->voterGroups->isNotEmpty())
            @foreach ($data['election']->voterGroups as $group)
                @include('users.admin.elections.include.editQualifiedVoterGoupModal')
            @endforeach
        @endif
        {{-- script start --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function() {

                        const url = this.dataset.url;

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This policy will be permanently deleted.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = url;
                            }
                        });

                    });
                });
            });
        </script>
        {{-- script end --}}

    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
