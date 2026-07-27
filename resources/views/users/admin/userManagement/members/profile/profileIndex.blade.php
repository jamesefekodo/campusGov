@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? ''))
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

                            <h4 class="block text-white text-center p-1 type-b-page-header text-capitalize">
                                @php
                                    $personal = $data['member']->memberPersonalProfile;
                                    $middleInitial = $personal?->middle_name
                                        ? strtoupper(substr($personal->middle_name, 0, 1)) . '. '
                                        : '';
                                @endphp

                                {{ ($personal?->first_name ?? $data['member']->username) . ' ' . $middleInitial . ($personal?->last_name ?? '') }}
                            </h4>
                            <div class="review-info-card-container">
                                <a href="#" type="button" data-toggle="modal" data-target="#basicAccountInfoModal"
                                    style="text-decoration: none" data-toggle="popover" data-trigger="focus"
                                    data-placement="top" title="Basic Account Info">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fa fa-user-circle"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Basic Acc. Info</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="{{ route('viewMemberPersonalProfile', ['uuid' => $data['member']->uuid]) }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Personal Profile
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('viewMemberAcademicInfo', ['uuid' => $data['member']->uuid]) }}">

                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Academic Info
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('viewMemberAcademicHistory', ['uuid' => $data['member']->uuid]) }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Academic History
                                            </p>
                                        </div>
                                    </div>
                                </a>

                                <a href="{{ route('viewMemberSkills', ['uuid' => $data['member']->uuid]) }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fa-solid fa-brain"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Skills
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('viewMemberBusiness', ['uuid' => $data['member']->uuid]) }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fa-solid fa-briefcase"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Business(es) / Employment(s)
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('viewMemberLeadershipPosition', ['uuid' => $data['member']->uuid]) }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fa-solid fa-crown"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Leadership Position(s)
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('viewMemberAward', ['uuid' => $data['member']->uuid]) }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fa-solid fa-trophy"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Awards / Achievements
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('viewMemberSocialMediaHandles', ['uuid' => $data['member']->uuid]) }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Social Media
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fas fa-receipt"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Payment Transactions
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('viewMemberMisconducts', ['uuid' => $data['member']->uuid]) }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fa-solid fa-gavel "></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Misconducts
                                            </p>
                                        </div>
                                    </div>
                                </a>



                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('memberAwaitingVerification') }}">
                            <i class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>

                </div>

            </div>
        </div>

        {{-- modals start --}}
        @include('users.admin.userManagement.members.verification.include.updateBasicAccountInfoModal');
        {{-- modals end --}}
    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
