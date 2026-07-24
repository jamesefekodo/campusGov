@extends('layouts.userLayout')
@section('title', 'Academic Info')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <div class="mb-4">
                <div class="individual-review-card-container">
                    <div class="col-md-12 individual-review-card">
                        <div class="my_form_container row col-md-12 pb-5">
                            <h4 class="block text-center py-2 pt-3 text-uppercase type-b-page-header">
                                Academic Info
                            </h4>
                            <div class="col-md-6">
                                <p class="m-1">
                                    Academic Status: <span class="text-black">
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
                                </p>
                                <p class="m-1">
                                    Matric Number: <span class="text-black">
                                        {{ $data['member']->memberAcademicInfo->matric_number }}
                                    </span>
                                </p>
                                <p class="m-1">
                                    Level: <span class="text-black">
                                        <span style="font-weight: bold">
                                            @if ($data['member']->memberAcademicInfo->academic_status == 'student')
                                                {{-- Current: --}}
                                            @elseif($data['member']->memberAcademicInfo->academic_status == 'alumni')
                                                Graduated:
                                            @else
                                                -
                                            @endif
                                        </span>

                                        @php
                                            $academic = $data['member']->memberAcademicInfo;
                                        @endphp


                                        @if ($academic?->academic_status == 'student')
                                            {{ currentLevel(
                                                \App\Models\AcademicConfig\Program::whereSlug($academic->program)->first(),
                                                now()->year - (int) explode('/', $academic->admission_session)[0] + 1,
                                            ) }}
                                        @elseif($academic?->academic_status == 'alumni')
                                            @if ($academic?->graduation_session)
                                                ({{ $academic->graduation_session }}/{{ $academic->graduation_session + 1 }})
                                            @endif
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $academic?->academic_status ?? '-')) }}

                                            @if ($academic?->graduation_session)
                                                ({{ $academic->graduation_session }}/{{ $academic->graduation_session + 1 }})
                                            @endif
                                        @endif
                                    </span>
                                </p>
                                <p class="m-1">
                                    Department: <span class="text-black">
                                        {{ $data['member']->memberAcademicInfo->theDepartment->name }}
                                    </span>
                                </p>
                                @if ($data['member']->memberAcademicInfo->graduation_session)
                                    <p class="m-1">
                                        Graduation Session: <span class="text-black">
                                            {{ $data['member']->memberAcademicInfo->graduation_session }}/{{ $data['member']->memberAcademicInfo->graduation_session + 1 }}
                                        </span>
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p class="m-1">
                                    Program: <span class="text-black">
                                        {{ $data['member']->memberAcademicInfo->theProgram->name }}
                                        @if ($data['member']->memberAcademicInfo->theProgram->short_name)
                                            ({{ $data['member']->memberAcademicInfo->theProgram->short_name }})
                                        @endif
                                        - {{ $data['member']->memberAcademicInfo->programType->type_name }}
                                    </span>
                                </p>
                                <p class="m-1">
                                    Program Duration: <span class="text-black">
                                        {{ getProgramDuration($data['member']->memberAcademicInfo->theProgram) }}
                                        years
                                    </span>
                                </p>
                                <p class="m-1">
                                    Faculty: <span class="text-black">
                                        {{ $data['member']->memberAcademicInfo->theFaculty->name }}
                                        @if ($data['member']->memberAcademicInfo->theFaculty->acronym)
                                            ({{ $data['member']->memberAcademicInfo->theFaculty->acronym }})
                                        @endif
                                    </span>
                                </p>
                                <p class="m-1">
                                    Admission Session: <span class="text-black">
                                        {{ $data['member']->memberAcademicInfo->admission_session }}/{{ $data['member']->memberAcademicInfo->admission_session + 1 }}
                                    </span>
                                </p>

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
