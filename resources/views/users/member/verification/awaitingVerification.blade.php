@extends('layouts.miscellaneousLayout')
@section('title', 'Awaiting Verification')
@section('content')
    <div class="container">
        <div class="col-md-12 m-auto">
            <div class="user_page_title">Awaiting Verification</div>
            <div class="container">
                <div class="user_page_container mb-5">
                    <div class="col-md-12 row d-flex justify-content-center align-items-center">
                        <div class="col md-5 d-flex justify-content-center align-items-center">
                            @if (
                                $data['member']->photo &&
                                    file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['member']->photo)))
                                <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['member']->photo) }}"
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
                                    Level:
                                </span>

                                @php
                                    $data['member'] = $data['member']->memberAcademicInfo;
                                @endphp

                                <span style="font-weight: bold">
                                    @if ($data['member']?->academic_status == 'student')
                                        {{-- Current Level: --}}
                                    @elseif($data['member']?->academic_status == 'alumni')
                                        Graduated -
                                    @else
                                        Status:
                                    @endif
                                </span>

                                @if ($data['member']?->academic_status == 'student')

                                    {{ currentLevel($data['member']->theProgram, studentCurrentLevel($data['member'])) }}
                                @elseif($data['member']?->academic_status == 'alumni')
                                    {{ $data['member']->graduation_session
                                        ? $data['member']->graduation_session . '/' . ($data['member']->graduation_session + 1)
                                        : '-' }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $data['member']?->academic_status ?? '-')) }}

                                    @if ($data['member']?->graduation_session)
                                        ({{ $data['member']->graduation_session }}/{{ $data['member']->graduation_session + 1 }})
                                    @endif

                                @endif

                            </p>
                        </div>
                    </div>
                    <div class="col-md-12 main_content_container">
                        <br>
                        <h3 class="text-center text-red">
                            Awaiting Membership Verification<br>
                        </h3>
                        <h5>Your registration has been successfully submitted and is currently under review.</h5>
                        <h5>We may contact you, your class representative, department leaders, relevant offices, and perform
                            additional checks to verify your membership request.
                        </h5>
                        <h5>Please check back later. After the verification process, your account will either be approved or
                            declined. You will be notified of the final decision.</h5>


                        <br>
                        <br>
                        <div class="d-flex justify-content-center">
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
@endsection
