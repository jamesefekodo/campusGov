@extends('layouts.userLayout')
@section('title', 'Election - ' . $data['election']->title)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Election - <span class="text-uppercase">{{ strtolower($data['election']->title) }} {{ $data['election']->academic_session .'/'. $data['election']->academic_session +1 }}</span>
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

                                                @default
                                                    <span
                                                        class="badge bg-danger-subtle text-danger">{{ $data['election']->status }}</span>
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Title:</span>
                                        <span class="text-black">
                                            {{ $data['election']->title }} {{ $data['election']->academic_session .'/'. $data['election']->academic_session +1 }}
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

                                    <div class="row col-md-12 p-4">
                                        <div class="col-md-6">
                                            <h5 class="text-blue text-decoration-underline fw-bold">Qualified Applicant
                                                Group (General)</h5>
                                            @if ($data['election']->generalCandidateGroups->isEmpty())
                                                <h5 class="m-2">
                                                    All members are qualified applicants, regardless of their program,
                                                    faculty, or level. <br> <span class="text-danger"> To change this, add a
                                                        specific set of members using the button
                                                        below.
                                                    </span>
                                                </h5>
                                            @else
                                                show items
                                            @endif
                                            {{-- generalCandidateGroups --}}
                                            @if (Auth::user()->hasPermission('elections.read'))
                                                <div class="d-flex justify-content-center">
                                                    <div class="m-1">
                                                        <button class="btn btn-primary btn-sm table_btn" data-toggle="modal"
                                                            data-target="#addNewQualifiedCandidateGoupModal">
                                                            <i class="fas fa-plus"></i> Add Applicant Group</button>
                                                    </div>
                                                </div>
                                            @endif
                                            @include('users.admin.elections.include.addNewQualifiedCandidateGoupModal')
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="text-blue text-decoration-underline fw-bold">Qualified Voters
                                                Group</h5>
                                        </div>
                                    </div>

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

    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
