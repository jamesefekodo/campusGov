@extends('layouts.miscellaneousLayout')
@section('title', 'Account Setup')
@section('content')
    <div class="container">
        <div class="col-md-12 m-auto">
            <div class="user_page_title">Account Setup</div>
            <div class="container">
                <div class="container user_page_container mb-5">
                    <div class="col-md-12 row d-flex justify-content-center align-items-center">
                        <div class="col md-5 d-flex justify-content-center align-items-center">
                            @if (
                                $data['member']->passport &&
                                    file_exists(public_path('assets/uploads/users/students/passport/' . $data['member']->passport)))
                                <img src="{{ asset('assets/uploads/users/students/passport/' . $data['member']->passport) }}"
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
                        </div>
                    </div>
                    <div class="col-md-12 main_content_container">
                        <br>

                        {{-- <div class="m-0 p-0" style="overflow: hidden; position: relative;">
                            <h5 class="notice_message m-0 p-0 text-red">Please note that only the 'Personal Profile' and
                                'Academic Info' sections are compulsory.</h5>
                        </div> --}}

                        <section class="user_dashboard_quicklink_card_container" data-toggle="popover" data-trigger="focus"
                            data-placement="top">
                            {{-- Personal Profile --}}
                            <a href="#" type="button" data-toggle="modal" data-target="#personalProfileModal"
                                style="text-decoration: none" data-toggle="popover" data-trigger="focus"
                                data-placement="top" title="Personal Profile">
                                <div class="quicklink_card @if ($data['member']->studentPersonalProfile) done @endif">
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
                            <a href="#" type="button" data-toggle="modal" data-target="#academicInfoModal"
                                style="text-decoration: none" data-toggle="popover" data-trigger="focus"
                                data-placement="top" title="Academic Info">
                                <div class="quicklink_card @if (
                                    $data['member']->studentAcademicInfo &&
                                        isset($data['member']->studentAcademicInfo->affiliation_status) &&
                                        (isset($data['member']->studentAcademicInfo->nd_admission_year) ||
                                            isset($data['member']->studentAcademicInfo->hnd_admission_year)) &&
                                        (isset($data['member']->studentAcademicInfo->current_program) ||
                                            isset($data['member']->studentAcademicInfo->past_program) ||
                                            isset($data['member']->studentAcademicInfo->past_program_2))) done @endif">
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

                            {{-- <a href="#" type="button" data-toggle="modal" data-target="#academicHistoryModal"
                                style="text-decoration: none" data-toggle="popover" data-trigger="focus"
                                data-placement="top" title="Academic History">

                                <div
                                    class="quicklink_card
                                @if (!$data['member']->studentAcademicHistory->isEmpty()) done @endif
                                ">
                                    <div class="quicklink_card_body">
                                        <div class="quicklink_card_icon">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div class="quicklink_card_content">
                                            <p>Academic History</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="#" type="button" data-toggle="modal" data-target="#nextStudentSkillsModal"
                                style="text-decoration: none" data-toggle="popover" data-trigger="focus"
                                data-placement="top" title="Skills">
                                <div
                                    class="quicklink_card
                                @if (!$data['member']->studentSkills->isEmpty()) done @endif
                                ">
                                    <div class="quicklink_card_body">
                                        <div class="quicklink_card_icon">
                                            <i class="fa-solid fa-brain"></i>
                                        </div>
                                        <div class="quicklink_card_content">
                                            <p>Skills</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="#" type="button" data-toggle="modal" data-target="#employmentAndBusinessModal"
                                style="text-decoration: none" data-toggle="popover" data-trigger="focus"
                                data-placement="top" title="Employment(s)">
                                <div
                                    class="quicklink_card
                                @if (!$data['member']->studentEmploymentAndBusiness->isEmpty()) done @endif
                                ">
                                    <div class="quicklink_card_body">
                                        <div class="quicklink_card_icon">
                                            <i class="fa-solid fa-briefcase"></i>
                                        </div>
                                        <div class="quicklink_card_content">
                                            <p>Business(es) / Employment(s)</p>
                                        </div>
                                    </div>
                                </div>
                            </a>


                            <a href="#" type="button" data-toggle="modal" data-target="#studentLeadershipModal"
                                style="text-decoration: none" data-toggle="popover" data-trigger="focus"
                                data-placement="top" title="Leadership Position(s)">
                                <div
                                    class="quicklink_card
                                @if (!$data['member']->studentLeadershipPosition->isEmpty()) done @endif
                                ">
                                    <div class="quicklink_card_body">
                                        <div class="quicklink_card_icon">
                                            <i class="fa-solid fa-crown"></i>
                                        </div>
                                        <div class="quicklink_card_content">
                                            <p>Leadership Position(s)</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="#" type="button" data-toggle="modal" data-target="#nextStudentAwardModal"
                                style="text-decoration: none" data-toggle="popover" data-trigger="focus"
                                data-placement="top" title="Awards/Achievements">
                                <div
                                    class="quicklink_card
                                @if (!$data['member']->StudentAward->isEmpty()) done @endif
                                ">
                                    <div class="quicklink_card_body">
                                        <div class="quicklink_card_icon">
                                            <i class="fa-solid fa-trophy"></i>
                                        </div>
                                        <div class="quicklink_card_content">
                                            <p>Awards / Achievements</p>
                                        </div>
                                    </div>
                                </div>
                            </a> --}}

                        </section>

                        <br>
                        <h4 class="text-red">{{ $errors->first('passport') ?? '' }}</h4>

                        <br>
                        <div class="d-flex justify-content-center">
                            <a href="
                            {{-- {{ route('studentSubmitAccountForVerification', ['username' => $data['member']->username]) }} --}}
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
