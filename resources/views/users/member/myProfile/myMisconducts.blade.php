@extends('layouts.userLayout')
@section('title', 'Academic History')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <div class="mb-4">
                <div class="individual-review-card-container">
                    <div class="col-md-12 individual-review-card">
                        <div class="d-flex justify-content-end me-2">
                            <div class="m-1">
                                <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                    data-target="#addNewAcademicRecordModal">
                                    <i class="fas fa-plus"></i> New Record</button>
                            </div>
                        </div>
                        @include('users.member.myProfile.include.addNewAcademicRecordModal')
                        <div class="my_form_container row col-md-12 pb-5">
                            <h4 class="block text-center py-2 pt-3 text-uppercase type-b-page-header">
                                Academic History
                            </h4>
                            <div class="row">

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

                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('memberViewSelfProfile') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>


                        </div>
                    </div>


                </div>

            </div><!--//container-fluid-->
        </div><!--//app-content-->
    @endsection
