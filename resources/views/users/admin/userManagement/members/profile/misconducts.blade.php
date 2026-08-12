@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? '') . ' - Misconducts')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')
                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Misconducts
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    @forelse($data['member']->memberMisconducts as $misconduct)
                                        <div class="col-md-4 mb-3">
                                            <div class="card shadow-sm border-1 h-100">

                                                <div class="card-body p-4">

                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h5 class="mb-1 fw-bold">
                                                                @switch($misconduct->misconduct_type)
                                                                    @case('abuse_of_office')
                                                                        Abuse of Office
                                                                    @break

                                                                    @case('bullying')
                                                                        Bullying
                                                                    @break

                                                                    @case('conflict_of_interest')
                                                                        Conflict of Interest
                                                                    @break

                                                                    @case('cyberbullying')
                                                                        Cyberbullying
                                                                    @break

                                                                    @case('financial_misconduct')
                                                                        Financial Misconduct
                                                                    @break

                                                                    @case('fighting')
                                                                        Fighting
                                                                    @break

                                                                    @case('inciting_unrest')
                                                                        Inciting Unrest
                                                                    @break

                                                                    @case('insubordination')
                                                                        Insubordination
                                                                    @break

                                                                    @case('misinformation')
                                                                        Misinformation
                                                                    @break

                                                                    @case('neglect_of_duties')
                                                                        Neglect of Duties
                                                                    @break

                                                                    @case('substance_abuse')
                                                                        Substance Abuse
                                                                    @break

                                                                    @case('theft')
                                                                        Theft
                                                                    @break

                                                                    @case('unauthorized_meetings')
                                                                        Unauthorized Meetings
                                                                    @break

                                                                    @case('vandalism')
                                                                        Vandalism
                                                                    @break

                                                                    @case('verbal_abuse')
                                                                        Verbal Abuse
                                                                    @break

                                                                    @default
                                                                @endswitch </h5>

                                                            <small class="text-muted text-uppercase">
                                                                {{ $misconduct->case_id }}
                                                            </small>
                                                        </div>

                                                        <i class="fas fa-gavel fs-3 text-secondary"></i>
                                                    </div>

                                                    <hr>

                                                    <p class="mb-2">
                                                        <strong>Type/Title</strong><br>
                                                        @switch($misconduct->misconduct_type)
                                                            @case('abuse_of_office')
                                                                Abuse of Office
                                                            @break

                                                            @case('bullying')
                                                                Bullying
                                                            @break

                                                            @case('conflict_of_interest')
                                                                Conflict of Interest
                                                            @break

                                                            @case('cyberbullying')
                                                                Cyberbullying
                                                            @break

                                                            @case('financial_misconduct')
                                                                Financial Misconduct
                                                            @break

                                                            @case('fighting')
                                                                Fighting
                                                            @break

                                                            @case('inciting_unrest')
                                                                Inciting Unrest
                                                            @break

                                                            @case('insubordination')
                                                                Insubordination
                                                            @break

                                                            @case('misinformation')
                                                                Misinformation
                                                            @break

                                                            @case('neglect_of_duties')
                                                                Neglect of Duties
                                                            @break

                                                            @case('substance_abuse')
                                                                Substance Abuse
                                                            @break

                                                            @case('theft')
                                                                Theft
                                                            @break

                                                            @case('unauthorized_meetings')
                                                                Unauthorized Meetings
                                                            @break

                                                            @case('vandalism')
                                                                Vandalism
                                                            @break

                                                            @case('verbal_abuse')
                                                                Verbal Abuse
                                                            @break

                                                            @default
                                                        @endswitch
                                                    </p>

                                                    <p class="mb-3">
                                                        <strong>Reported On</strong><br>
                                                        {{ \Carbon\Carbon::parse($misconduct->report_date)->format('l, d F, Y') }}
                                                    </p>

                                                    <p class="mb-3">
                                                        <strong>Status</strong><br>
                                                        @switch($misconduct->status)
                                                            @case('pending')
                                                                <span class="badge bg-gray text-white">Pending</span>
                                                            @break

                                                            @case('under_investigation')
                                                                <span class="badge bg-yellow text-black">Under
                                                                    Investigation</span>
                                                            @break

                                                            @case('partially_resolved')
                                                                <span class="badge bg-blue text-white">Partially
                                                                    Resolved</span>
                                                            @break

                                                            @case('resolved')
                                                                <span class="badge bg-green text-white">Resolved</span>
                                                            @break

                                                            @default
                                                        @endswitch
                                                    </p>
                                                    @if (Auth::user()->hasPermission('misconducts.read'))
                                                        <div class="d-flex justify-content-end">
                                                            <a class="btn btn-primary btn-sm"
                                                                href="{{ route('viewMisconductCase', ['uuid' => $misconduct->uuid]) }}">View</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @empty

                                            <div class="col-12">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body text-center py-5">
                                                        <i class="fas fa-gavel fa-3x text-secondary mb-3"></i>
                                                        <h5>No Records Found!</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                        @if (Auth::user()->hasPermission('misconducts.create'))
                                            <div class="d-flex justify-content-end flex-wrap mt-3">
                                                <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                                    data-target="#addNewMisconductModal">
                                                    <i class="fas fa-plus"></i> Create New</button>
                                            </div>


                                            @include('users.admin.misconducts.include.addNewMisconductModal')
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Back Home --}}
                        <div class="w-100 justify-content-center text-center my-4 pt-5">
                            <a class="btn btn-primary" href="{{ route('viewMember', ['uuid' => $data['member']->uuid]) }}">
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
