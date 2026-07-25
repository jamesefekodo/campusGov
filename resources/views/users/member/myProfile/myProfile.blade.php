@extends('layouts.userLayout')
@section('title', 'My Profile')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <div class="mb-4">
                <h1 class="app-page-title mb-0">My Profile</h1>
                <div class="review-info-card-container">
                    <a href="{{ route('myPersonalProfile') }}">
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
                    <a href="{{ route('myAcademicInfo') }}">
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
                    <a href="{{ route('myAcademicHistory') }}">
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

                    <a href="{{ route('myAcademicSkills') }}">
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
                    <a href="{{ route('myBusiness') }}">
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
                    <a href="{{ route('myLeadershipPosition') }}">
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
                    <a href="{{ route('myAwards') }}">
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
                    <a
                        href="
                                {{ route('mySocialMediaHandles') }}
                                 ">
                        <div class="review-info-card">
                            <div class="review-info-icon">
                                <i class="fa-solid fa-globe"></i>
                            </div>
                            <div class="review-info-content">
                                <p class="review-card-info-title">
                                    Social Media
                                </p>
                            </div>
                        </div>
                    </a>
                    <a
                        href="
                                    {{-- {{ route('studentMyProfileMisconducts') }} --}}
                                     ">
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


            {{-- modals starts here --}}
            {{-- modals ends here --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
