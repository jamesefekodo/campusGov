@extends('layouts.miscellaneousLayout')
@section('title', 'Account Setup')
@section('content')
    <div class="container">
        <div class="col-md-12 m-auto">
            <div class="user_page_title">Notice of Association Dues Payment Waiver :: Verification</div>
            <div class="container">
                <div class="user_page_container mb-5">
                    <div class="col-md-12 row d-flex justify-content-center align-items-center">
                        <div class="col md-5 d-flex justify-content-center align-items-center">
                            @if (
                                $data['waiver']->payer->photo &&
                                    file_exists(public_path(
                                            'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['waiver']->payer->photo)))
                                <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['waiver']->payer->photo) }}"
                                    style="max-width: 200px" alt="{{ $data['waiver']->payer->username }}">
                            @else
                                <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture"
                                    style="max-width: 200px">
                            @endif
                        </div>
                        <div class="col-md-7">
                            @php
                                $academic = $data['waiver']->payer?->memberAcademicInfo;

                                $programLevel = studentLevelAtDue($academic, $data['waiver']->due);

                                $program = $programLevel['program'];
                                $level = $programLevel['level'];

                                $ctx = resolveAcademicContextForDue(
                                    $data['waiver']->memberAcademicInfo,
                                    $data['waiver']->due,
                                );
                            @endphp
                            <p class="m-0 pt-1">Name: <span style="font-weight: bold">
                                    @php
                                        $personalProfile = $data['waiver']->payer->memberPersonalProfile;
                                        $academicInfo = $data['waiver']->payer->memberAcademicInfo;
                                    @endphp
                                    {{ $personalProfile->first_name }} {{ $personalProfile->middle_name }}
                                    {{ $personalProfile->last_name }}
                                </span></p>
                            <p class="m-0 pt-1">Matric No.: <span style="font-weight: bold">
                                    <span class="text-uppercase">
                                        {{ $ctx['matric_number'] ?? 'N/A' }}
                                    </span>
                                </span></p>
                            <p class="m-0 pt-1">Level: <span style="font-weight: bold">
                                    @if ($program)
                                        {{ currentLevel($program, $level) }}
                                    @else
                                        <span class="text-danger">
                                            Previous programme is missing
                                        </span>
                                    @endif
                                </span></p>
                            <p class="m-0 pt-1">Program: <span style="font-weight: bold">
                                    {{ $ctx['program'] ?? 'N/A' }}
                                    -
                                    {{ $ctx['program_type'] ?? 'N/A' }}
                                </span>
                            </p>
                            <p class="m-0 pt-1">Faculty: <span style="font-weight: bold">
                                    {{ $ctx['faculty'] ?? 'N/A' }}
                                </span>
                            </p>
                            <p class="m-0 pt-1">Department: <span style="font-weight: bold">
                                    {{ $ctx['department'] ?? 'N/A' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12 m-2 row main_content_container">
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Due:</span>
                            <span class="text-black text-capitalize">
                                {{ $data['waiver']->due->title }} -

                                @if ($program)
                                    {{ currentLevel($program, $level) }}
                                @else
                                    <span class="text-danger">
                                        Previous programme is missing
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Academic Session:</span>
                            <span class="text-black text-capitalize">
                                {{ $data['waiver']->due->academic_year }}/{{ $data['waiver']->due->academic_year + 1 }}
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Description:</span>
                            <span class="text-black text-capitalize">
                                {{ $data['waiver']->due->description }}
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Reason for waiver:</span>
                            <span class="text-black text-capitalize">
                                {{ $data['waiver']->reason }}
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Amount:</span>
                            <span class="text-black text-capitalize">
                                ₦{{ number_format($data['due']->payable_amount, 2) }}
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Approval Status:</span>
                            <span class="text-black text-capitalize">
                                @switch($data['waiver']->approval_status)
                                    @case('approved')
                                        Approved
                                    @break

                                    @case('declined')
                                        Declined
                                    @break

                                    @case('pending')
                                        Pending
                                    @break

                                    @default
                                        {{ $data['waiver']->approval_status }}
                                @endswitch
                            </span>
                        </div>
                        <div class="col-md-6 p-1">
                            <span style="font-weight: bold">Date:</span>
                            <span class="text-black text-capitalize">
                                {{ \Carbon\Carbon::parse($data['waiver']->approved_at)->format('D, d M, Y') }}
                            </span>
                        </div>
                        <div class="col-md-12 my-4">
                            <p class="fs-2">
                                Notice:
                                @if ($data['waiver']->approval_status == 'approved')
                                    <span style="color: #198754;">
                                        This is to inform you that the member whose name and photo are on this notice is
                                        a
                                        member of the association and has been granted a waiver from
                                        {{ $data['due']->title }}
                                        {{ $data['waiver']->due->academic_year }}/{{ $data['waiver']->due->academic_year + 1 }}
                                        :: @if ($program)
                                            {{ currentLevel($program, $level) }}
                                        @endif. This notice should be treated as equivalent to a
                                        receipt
                                        or proof of payment, as this member is not expected to make payment upon being
                                        granted a waiver.
                                    </span>
                                @else
                                    <span style="color: #dc3545;">
                                        Kindly disregard this waiver notice, as it is not approved or is yet to be
                                        approved.
                                    </span>
                                @endif
                            </p>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center my-5">
                            <a href="{{ route('homepage') }}
                             " class="btn btn-success mx-3">
                                Home
                                <i class="fas fa-home"></i>
                            </a>
                        </div>

                    </div>


                </div>

            </div>

        </div>
    </div>
@endsection
