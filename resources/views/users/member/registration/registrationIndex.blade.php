@extends('layouts.miscellaneousLayout')
@section('title', 'Account Setup')
@section('content')
    <div class="container">
        <div class="col-md-12 m-auto">
            <div class="user_page_title">Account Setup</div>
            <div class="container">
                <div class="user_page_container mb-5">
                    <div class="col-md-12 row d-flex justify-content-center align-items-center">
                        <div class="col md-5 d-flex justify-content-center align-items-center">
                            @if ($data['member']->photo && file_exists(public_path('assets/uploads/users/members/photo/' . $data['member']->photo)))
                                <img src="{{ asset('assets/uploads/users/members/photo/' . $data['member']->photo) }}"
                                    style="max-width: 200px" alt="{{ $data['member']->username }}">
                            @else
                                <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture"
                                    style="max-width: 200px">
                            @endif
                        </div>
                        <div class="col-md-7">
                            <p>Username: <span style="font-weight: bold">
                                    {{ $data['member']->username }}
                                </span></p>
                            <p>Phone: <span style="font-weight: bold">
                                    {{ $data['member']->phone }}
                                </span></p>
                            <p>Email: <span style="font-weight: bold">
                                    {{ $data['member']->email }}
                                </span></p>
                            <p class="">
                                <span style="font-weight: bold">
                                    @if ($data['member']?->memberAcademicInfo?->academic_status == 'student')
                                        Current Level:
                                    @elseif($data['member']?->memberAcademicInfo?->academic_status == 'alumni')
                                        Graduation Session:
                                    @else
                                        Status:
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
                                    {{ $academic->graduation_session ?? '-' }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $academic?->academic_status ?? '-')) }}

                                    @if ($academic?->graduation_session)
                                        ({{ $academic->graduation_session }})
                                    @endif

                                @endif

                            </p>
                        </div>
                    </div>
                    <div class="col-md-12 main_content_container">
                        <br>

                        <section class="user_dashboard_quicklink_card_container" data-toggle="popover" data-trigger="focus"
                            data-placement="top">
                            {{-- Personal Profile --}}
                            <a href="{{ route('memberCreatePersonalProfile') }}" style="text-decoration: none">
                                <div class="quicklink_card @if ($data['member']->memberPersonalProfile) done @endif">
                                    <div class="quicklink_card_body">
                                        <div class="quicklink_card_icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="quicklink_card_content">
                                            <p>Personal Profile</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            {{-- Academic Info --}}
                            <a href="{{ route('memberCreateAcademicInfo') }}" style="text-decoration: none">
                                <div class="quicklink_card @if (
                                    $data['member']->memberAcademicInfo &&
                                        filled($data['member']->memberAcademicInfo->academic_status) &&
                                        filled($data['member']->memberAcademicInfo->program) &&
                                        filled($data['member']->memberAcademicInfo->program_type) &&
                                        filled($data['member']->memberAcademicInfo->department) &&
                                        filled($data['member']->memberAcademicInfo->faculty) &&
                                        filled($data['member']->memberAcademicInfo->admission_session) &&
                                        filled($data['member']->memberAcademicInfo->matric_number)) done @endif">
                                    <div class="quicklink_card_body">
                                        <div class="quicklink_card_icon">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div class="quicklink_card_content">
                                            <p>Academic Info</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </section>

                        <br>
                        <h4 class="text-red">{{ $errors->first('passport') ?? '' }}</h4>

                        <br>
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('memberSubmitSelfAccountForVerification') }}
                             "
                                class="btn btn-success mx-3">
                                Submit for Verification
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a href="{{ route('logoutRequest') }}" class="btn btn-danger mx-3">
                                Logout
                                <i class="fas fa-times"></i>
                            </a>
                        </div>

                    </div>


                </div>

            </div>

        </div>
    </div>
    {{-- modals start --}}
    {{-- @include('users.students.registration.include.registerPersonalProfileModal')
    @include('users.students.registration.include.registerAcademicInfoModal')
    @include('users.students.registration.include.registerAcademicHistoryModal')
    @include('users.students.registration.include.registerStudentSkillsModal')
    @include('users.students.registration.include.registerStudentEmploymentAndBusinessModal')
    @include('users.students.registration.include.registerStudentLeadershipPositionModal')
    @include('users.students.registration.include.registerStudentAwardsModal') --}}
    {{-- modals end --}}

@endsection
