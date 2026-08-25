@extends('layouts.userLayout')
@section('title', 'Election Candidate Application - ' .
    $data['application']->theCandidate->memberPersonalProfile->first_name . ' - ' .
    $data['application']->position->position)
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
                                $data['member'] = $data['application']->theCandidate;
                            @endphp
                            @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')
                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container pb-5">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Election - <span class="text-uppercase">Candidate Application |
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
                                    <h5 class="text-blue text-decoration-underline fw-bold mt-3">Applicant</h5>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Name:</span>
                                        <span class="text-black">
                                            {{ $data['application']->theCandidate->memberPersonalProfile->first_name }}
                                            {{ $data['application']->theCandidate->memberPersonalProfile->middle_name }}
                                            {{ $data['application']->theCandidate->memberPersonalProfile->last_name }}
                                            <a href="{{ route('viewMember', ['uuid' => $data['application']->theCandidate->uuid]) }}"
                                                class="badge bg-secondary text-white" title="View Initiator">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Program:</span>
                                        <span class="text-black">
                                            {{ $data['application']->program->name }}
                                            ({{ $data['application']->programType->type_name }})
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Faculty:</span>
                                        <span class="text-black">
                                            {{ $data['application']->faculty->name }} @if ($data['application']->faculty->acronym)
                                                ({{ $data['application']->faculty->acronym }})
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Department:</span>
                                        <span class="text-black">
                                            {{ $data['application']->department->name }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Level:</span>
                                        <span class="text-black">
                                            {{ $data['application']->level_at_application }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Application Type:</span>
                                        <span class="text-black text-capitalize">
                                            {{ $data['application']->application_type }}
                                        </span>
                                    </div>
                                    <h5 class="text-blue text-decoration-underline fw-bold mt-3">Position</h5>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Position Aspired:</span>
                                        <span class="text-black">
                                            {{ $data['application']->position->position }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Application Fee:</span>
                                        <span class="text-black">
                                            ₦{{ number_format($data['application']->position->application_fee, 2) }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black">
                                            {{ $data['application']->position->description }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Initiated By:</span>
                                        <span class="text-black text-capitalize">
                                            {{ $data['application']->initiatedBy->memberPersonalProfile->first_name }}
                                            {{ $data['application']->initiatedBy->memberPersonalProfile->middle_name }}
                                            {{ $data['application']->initiatedBy->memberPersonalProfile->last_name }}

                                            <a href="{{ route('viewMember', ['uuid' => $data['application']->initiatedBy->uuid]) }}"
                                                class="badge bg-secondary text-white" title="View Initiator">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Payment:</span>
                                        <span class="text-black text-capitalize">
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
                                            @endif

                                            <a href="{{ route('viewPaymentTransaction', ['payment_transaction_id' => $data['application']->successfulPaymentTransaction?->payment_transaction_id]) }}"
                                                class="badge bg-secondary text-white" title="View Initiator">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Application Status:</span>
                                        <span class="text-black text-capitalize">
                                            @switch($data['application']->status)
                                                @case('draft')
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        Draft
                                                    </span>
                                                @break

                                                @case('pending_approval')
                                                    <span class="badge bg-warning-subtle text-warning">
                                                        Pending Approval
                                                    </span>
                                                @break

                                                @case('approved')
                                                    <span class="badge bg-success-subtle text-success">
                                                        Approved
                                                    </span>
                                                @break

                                                @case('declined')
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        Declined
                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        {{ $data['application']->status }}
                                                    </span>
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="col-md-12 p-1">
                                        <span style="font-weight: bold">Bio:</span>
                                        <span class="text-black text-capitalize">
                                            {!! $data['application']->bio !!}
                                        </span>
                                    </div>
                                    <div class="col-md-12 p-1">
                                        <span style="font-weight: bold">Manifesto:</span>
                                        <span class="text-black text-capitalize">
                                            {!! $data['application']->manifesto !!}
                                        </span>
                                    </div>
                                    <div class="col-md-12 p-1">
                                        <span style="font-weight: bold">Stakeholders Remarks:</span>
                                        <span class="text-black text-capitalize">
                                            @if ($data['application']->stakeholders_remarks)
                                                {!! $data['application']->stakeholders_remarks !!}
                                            @endif
                                        </span>
                                    </div>

                                    @if (
                                        $data['application']->application_form &&
                                            file_exists(public_path(
                                                    'assets/tenant_' .
                                                        tenant()->id .
                                                        '/uploads/elections/applicationForm/' .
                                                        $data['application']->application_form)))
                                        @php
                                            $applicationId = 'formImageModal_' . $data['application']->id;
                                        @endphp

                                        <div class="col-md-12 p-1">
                                            <span style="font-weight: bold">Application/Endorsement Form:</span>

                                            <span class="text-black">
                                                <span class="text-capitalize">

                                                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/elections/applicationForm/' . $data['application']->application_form) }}"
                                                        alt="Proof of Payment" class="img-thumbnail ms-2"
                                                        style="max-width:150px; cursor: zoom-in;" data-bs-toggle="modal"
                                                        data-bs-target="#{{ $applicationId }}">

                                                </span>
                                            </span>
                                        </div>

                                        <!-- Zoom Modal -->
                                        <div class="modal fade" id="{{ $applicationId }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content border-0 bg-transparent">

                                                    <div class="text-end mb-2">
                                                        <button type="button" class="btn btn-light rounded-circle"
                                                            data-bs-dismiss="modal" aria-label="Close">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>

                                                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/elections/applicationForm/' . $data['application']->application_form) }}"
                                                        class="img-fluid rounded shadow" alt="Application form">

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Processed:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['application']->approved_at)
                                                    {{ \Carbon\Carbon::parse($data['application']->approved_at)->format('l, d F, Y h:i A') }}
                                                @endif
                                                -
                                                @if ($data['application']->processed_by)

                                                    @if ($data['application']->processedBy?->memberPersonalProfile)
                                                        {{ trim(
                                                            $data['application']->processedBy->memberPersonalProfile->first_name .
                                                                ' ' .
                                                                ($data['application']->processedBy->memberPersonalProfile->middle_name
                                                                    ? $data['application']->processedBy->memberPersonalProfile->middle_name . ' '
                                                                    : '') .
                                                                $data['application']->processedBy->memberPersonalProfile->last_name,
                                                        ) }}
                                                    @else
                                                        {{ $data['application']->processed_by }}
                                                    @endif

                                                    @if ($data['application']->processedBy)
                                                        <a href="{{ route('viewMember', ['uuid' => $data['application']->processedBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Approver">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif

                                                @endif
                                            </span>
                                        </span>
                                    </div>





                                </div>
                                <div class="d-flex justify-content-end flex-wrap mt-3 px-3">
                                    @if (Auth::user()->hasPermission('elections.approve') && $data['application']->status == 'pending_approval')
                                        <button class="btn btn-warning m-1 d-flex align-items-center" data-toggle="modal"
                                            data-target="#processElectionCandidateRequestModal">
                                            <i class="fas fa-edit me-2"></i>
                                            <span>Process Request</span>
                                        </button>
                                    @endif

                                </div>
                                @include('users.admin.elections.include.processElectionCandidateRequestModal')
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        @php
                            $backRoute = route('electionOperationRequests');
                            if ($data['application']->status !== 'pending_approval') {
                                $backRoute = route('processedElectionOperationRequests');
                            }
                        @endphp
                        <a class="btn btn-primary" href="{{ $backRoute }}">
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
