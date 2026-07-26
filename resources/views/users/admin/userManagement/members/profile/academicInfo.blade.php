@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? '') . ' - Academic Info')
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
                                    Academic Info
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Matric Number:</span>
                                        <span class="text-black text-uppercase">
                                            {{ $data['member']->memberAcademicInfo->matric_number }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Level:</span>
                                        <span class="text-black">
                                            @php
                                                $academic = $data['member']->memberAcademicInfo;
                                            @endphp

                                            <span style="font-weight: bold">
                                                @if ($academic?->academic_status == 'student')
                                                    {{-- Current Level: --}}
                                                @elseif($academic?->academic_status == 'alumni')
                                                    Graduated:
                                                @else
                                                    {{ $academic?->academic_status }}
                                                @endif
                                            </span>

                                            @if ($academic?->academic_status == 'student')

                                                {{ currentLevel($academic->theProgram, studentCurrentLevel($academic)) }}
                                            @elseif($academic?->academic_status == 'alumni')
                                                {{ $academic->graduation_session
                                                    ? $academic->graduation_session . '/' . ($academic->graduation_session + 1)
                                                    : '-' }}
                                            @else
                                                {{ ucfirst(str_replace('_', ' ', $academic?->academic_status ?? '-')) }}

                                                @if ($academic?->graduation_session)
                                                    ({{ $academic->graduation_session }}/{{ $academic->graduation_session + 1 }})
                                                @endif

                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Academic Status:</span>
                                        <span class="text-black">
                                            @switch($data['member']->memberAcademicInfo->academic_status)
                                                @case('student')
                                                    Student
                                                @break

                                                @case('extra_year')
                                                    Extra Year
                                                @break

                                                @case('alumni')
                                                    Alumni
                                                @break

                                                @case('withdrawn')
                                                    Withdrawn
                                                @break

                                                @case('suspended')
                                                    Suspended
                                                @break

                                                @case('expelled')
                                                    Expelled
                                                @break

                                                @default
                                                    Unknown
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Program:</span>
                                        <span class="text-black">
                                            {{ $data['member']->memberAcademicInfo->theProgram->name }}
                                            @if ($data['member']->memberAcademicInfo->theProgram->short_name)
                                                ({{ $data['member']->memberAcademicInfo->theProgram->short_name }})
                                            @endif
                                            - {{ $data['member']->memberAcademicInfo->programType->type_name }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Duration:</span>
                                        <span class="text-black">
                                            @php
                                                $academic = $data['member']->memberAcademicInfo;
                                            @endphp

                                            {{ getProgramDuration($academic->theProgram, $academic->programType, $academic->theDepartment) }}
                                            Years
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Faculty:</span>
                                        <span class="text-black">
                                            {{ $data['member']->memberAcademicInfo->theFaculty->name }}
                                            @if ($data['member']->memberAcademicInfo->theFaculty->acronym)
                                                ({{ $data['member']->memberAcademicInfo->theFaculty->acronym }})
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Department:</span>
                                        <span class="text-black">
                                            {{ $data['member']->memberAcademicInfo->theDepartment->name }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Admission Session:</span>
                                        <span class="text-black">
                                            {{ $data['member']->memberAcademicInfo->admission_session }}/{{ $data['member']->memberAcademicInfo->admission_session + 1 }}
                                        </span>
                                    </div>
                                    @if ($data['member']->memberAcademicInfo->graduation_session)
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Graduation Session:</span>
                                            <span class="text-black">
                                                {{ $data['member']->memberAcademicInfo->graduation_session }}/{{ $data['member']->memberAcademicInfo->graduation_session + 1 }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Verification:</span>
                                        <span class="text-black">
                                            @switch($data['member']->memberAcademicInfo->is_verified)
                                                @case(true)
                                                    <span class="badge bg-success-subtle text-success">Verified</span>
                                                @break

                                                @default
                                                    <span class="badge bg-danger-subtle text-danger">Not-Verified</span>
                                            @endswitch
                                            @if ($data['member']->memberAcademicInfo?->verifiedBy?->memberAcademicInfo)
                                                by
                                                <a href="{{ route('viewMMember', ['uuid' => $data['member']->uuid]) }}">
                                                    {{ trim(
                                                        ($data['member']->memberAcademicInfo?->verifiedBy?->memberAcademicInfo?->first_name ?? '') .
                                                            ' ' .
                                                            ($data['member']->memberAcademicInfo?->verifiedBy?->memberAcademicInfo?->last_name ?? ''),
                                                    ) }}
                                                </a>
                                            @endif |
                                            {{ \Carbon\Carbon::parse($data['member']->memberAcademicInfo->verified_at)->format('l, d F Y \a\t h:i A') }}
                                        </span>
                                    </div>
                                    <hr>

                                    @if (Auth::user()->hasPermission('member_profile.update'))
                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            <a href="{{ route('editMemberAcademicInfo', ['uuid' => $data['member']->uuid]) }}"
                                                class="btn btn-warning m-1 d-flex align-items-center">
                                                <i class="fas fa-edit me-2"></i>
                                                <span>Update</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('viewMMember', ['uuid' => $data['member']->uuid]) }}">
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
