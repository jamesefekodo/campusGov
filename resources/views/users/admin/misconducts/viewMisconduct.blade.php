@extends('layouts.userLayout')
@section('title', 'View Misconduct :: ' . $data['misconduct']->case_id)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        View Role ::
                                        {{ $data['misconduct']->case_id }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="col-md-12 row">
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Case ID:</span>
                                                <span class="text-black text-uppercase">
                                                    {{ $data['misconduct']->case_id }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Member:</span>
                                                <span class="text-black">
                                                    @if ($data['misconduct']->studentPersonalProfile)
                                                        {{ $data['misconduct']->studentPersonalProfile->first_name }}
                                                        {{ $data['misconduct']->studentPersonalProfile->middle_name }}
                                                        {{ $data['misconduct']->studentPersonalProfile->last_name }}
                                                    @else
                                                        {{ $data['misconduct']->username }}
                                                    @endif
                                                    <a href="{{ route('viewMMember', ['uuid' => $data['misconduct']->userProfile->uuid]) }}"
                                                        class="badge bg-blue text-white" target=__blank><i
                                                            class="fas fa-user"></i></a>
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Type:</span>
                                                <span class="text-black">
                                                    @switch($data['misconduct']->misconduct_type)
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
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Status:</span>
                                                <span class="text-black">
                                                    @switch($data['misconduct']->status)
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
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Incident Date:</span>
                                                <span class="text-black">
                                                    {{ \Carbon\Carbon::parse($data['misconduct']->incident_date)->format('l, d F, Y') }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Reported On:</span>
                                                <span class="text-black">
                                                    {{ \Carbon\Carbon::parse($data['misconduct']->report_date)->format('l, d F, Y') }}
                                                    by {{ $data['misconduct']->reported_by }}
                                                    @if ($data['misconduct']->reportedBy)
                                                        <a href="{{ route('viewMMember', ['uuid' => $data['misconduct']->reportedBy->uuid]) }}"
                                                            class="badge bg-blue text-white" target=__blank><i
                                                                class="fas fa-user"></i></a>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-md-12 p-1">
                                                <span style="font-weight: bold">Description:</span>
                                                <span class="text-black">
                                                    {!! $data['misconduct']->description !!}
                                                </span>
                                            </div>
                                            <div class="col-md-12 p-1">
                                                <span style="font-weight: bold">Witness Details & Report:</span>
                                                <span class="text-black">
                                                    {!! $data['misconduct']->witness_report !!}
                                                </span>
                                            </div>
                                            <div class="col-md-12 p-1">
                                                <span style="font-weight: bold">Disciplinary Action:</span>
                                                <span class="text-black">
                                                    {!! $data['misconduct']->disciplinary_action !!}
                                                </span>
                                            </div>


                                            <hr>
                                            <div class="col-md-12">
                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <p>Attachment 1</p>

                                                        @if ($data['misconduct']->attachment_1)
                                                            <div class="image-container position-relative">

                                                                <img src="{{ asset('assets/uploads/misconductCases/attachments/' . $data['misconduct']->attachment_1) }}"
                                                                    class="img-fluid image-equal">

                                                                <button type="button"
                                                                    class="btn btn-dark btn-sm position-absolute"
                                                                    style="top:10px; right:10px;" data-toggle="modal"
                                                                    data-target="#imageModal"
                                                                    onclick="showImage('{{ asset('assets/uploads/misconductCases/attachments/' . $data['misconduct']->attachment_1) }}')">
                                                                    <i class="fas fa-search-plus"></i>
                                                                </button>

                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6">
                                                        <p>Attachment 2</p>

                                                        @if ($data['misconduct']->attachment_2)
                                                            <div class="image-container position-relative">

                                                                <img src="{{ asset('assets/uploads/misconductCases/attachments/' . $data['misconduct']->attachment_2) }}"
                                                                    class="img-fluid image-equal">

                                                                <button type="button"
                                                                    class="btn btn-dark btn-sm position-absolute"
                                                                    style="top:10px; right:10px;" data-toggle="modal"
                                                                    data-target="#imageModal"
                                                                    onclick="showImage('{{ asset('assets/uploads/misconductCases/attachments/' . $data['misconduct']->attachment_2) }}')">
                                                                    <i class="fas fa-search-plus"></i>
                                                                </button>

                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>

                                            <style>
                                                .image-container {
                                                    height: 300px;
                                                    border: 1px solid #dee2e6;
                                                    border-radius: .25rem;
                                                    overflow: hidden;
                                                    background: #f8f9fa;
                                                }

                                                .image-equal {
                                                    width: 100%;
                                                    height: 100%;
                                                    object-fit: contain;
                                                    /* Show the whole image */
                                                }
                                            </style>

                                            <!-- Bootstrap Modal -->
                                            <div class="modal fade" id="imageModal" tabindex="-1">
                                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Image Preview</h5>

                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body text-center">
                                                            <img id="previewImage" src="" class="img-fluid">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <script>
                                                function showImage(src) {
                                                    document.getElementById('previewImage').src = src;
                                                }
                                            </script>
                                        </div>
                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            @if (Auth::user()->hasPermission('misconducts.update'))
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#editMisconductModal">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                            @endif
                                        </div>


                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                @php
                                    $backBtn = route('activeMisconductCases');
                                    if ($data['misconduct']->status == 'resolved') {
                                        $backBtn = route('resolvedMisconductCases');
                                    }
                                @endphp

                                <a class="btn btn-primary" href="{{ $backBtn }}"> <i class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- modals start --}}
            @include('users.admin.misconducts.include.editMisconductModal')
            {{-- modals end --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
