@extends('layouts.userLayout')

@section('title', 'Association Leaders')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.activeAccount.payments.include.inner_nav') --}}
            </div>

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                Association Leaders

                                <span class="ms-5 badge bg-warning text-black" data-toggle="modal"
                                    data-target="#sessionSelector">
                                    {{ $data['academicSession'] }}/{{ $data['academicSession'] + 1 }}
                                    <i class="fa fa-filter"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    @include('users.admin.leadership.studentReps.include.filterStudentRepsByYearModal')

                    <div class="col-md-12">
                        <div class="my_form_container p-4">
                            <div class="association-leadership-page">

                                {{-- STUDENT LEADERS --}}
                                <div class="leadership-section mb-5">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <div class="leadership-section-icon">
                                            <i class="fa fa-users"></i>
                                        </div>

                                        <div>
                                            <h4 class="mb-1">Student Leadership</h4>
                                            <p class="text-muted mb-0">
                                                Association representatives for
                                                {{ $data['academicSession'] }}/{{ $data['academicSession'] + 1 }} academic session
                                            </p>
                                        </div>
                                    </div>

                                    @php
                                        $leadersByArm = $data['studentLeaders']->groupBy(
                                            fn($leader) => $leader->position?->arm?->slug ?? 'other',
                                        );
                                    @endphp

                                    @forelse ($leadersByArm as $armLeaders)
                                        @php
                                            $arm = $armLeaders->first()->position?->arm;
                                        @endphp

                                        <div class="leadership-arm mb-4">
                                            <div class="leadership-arm-heading">
                                                <span class="leadership-arm-line"></span>

                                                <h5 class="mb-0">
                                                    {{ $arm?->name ?? 'Other Representatives' }}
                                                </h5>

                                                <span class="leadership-arm-line"></span>
                                            </div>

                                            @if ($arm?->description)
                                                <p class="text-muted text-center small mb-4">
                                                    {{ $arm->description }}
                                                </p>
                                            @endif

                                            <div class="row g-3">
                                                @foreach ($armLeaders as $leader)
                                                    @php
                                                        $representative = $leader->theRep;

                                                        $socialLinks = $representative
                                                            ? $representative->memberSocialMediaHandle->filter(
                                                                fn($handle) => filled($handle->link),
                                                            )
                                                            : collect();
                                                    @endphp

                                                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                                        <div class="leader-profile-card h-100">
                                                            <div class="leader-photo">
                                                                @if (
                                                                    $representative?->photo &&
                                                                        file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $representative->photo)))
                                                                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $representative->photo) }}"
                                                                        alt="{{ $representative->name ?? $leader->username }}"
                                                                        class="leader-photo-image" loading="lazy">
                                                                @else
                                                                    <img src="{{ asset('system/images/default_profile_pic.webp') }}"
                                                                        alt="Default profile picture"
                                                                        class="leader-photo-image" loading="lazy">
                                                                @endif
                                                            </div>

                                                            <div class="leader-profile-details">
                                                                <h6 class="leader-name">
                                                                    {{-- {{ $representative?->name ?? $leader->username }} --}}
                                                                    {{ $representative?->memberPersonalProfile?->first_zz }}
                                                                </h6>

                                                                <div class="leader-position">
                                                                    {{ $leader->position?->name ?? 'Student Representative' }}
                                                                </div>

                                                                @if ($leader->remarks)
                                                                    <p class="leader-remarks">
                                                                        {{ $leader->remarks }}
                                                                    </p>
                                                                @endif

                                                                @if ($socialLinks->isNotEmpty())
                                                                    <div class="leader-social-links">
                                                                        @foreach ($socialLinks as $social)
                                                                            @php
                                                                                $platform = $social->thePlatform;
                                                                            @endphp

                                                                            <a href="{{ $social->link }}" target="_blank"
                                                                                rel="noopener noreferrer"
                                                                                class="leader-social-link"
                                                                                title="{{ $platform?->name ?? $social->platform }}"
                                                                                aria-label="{{ $platform?->name ?? $social->platform }}">

                                                                                @if (
                                                                                    $platform?->logo &&
                                                                                        file_exists(public_path('assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo)))
                                                                                    <img src="{{ asset('assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo) }}"
                                                                                        alt="{{ $platform->name }}"
                                                                                        loading="lazy">
                                                                                @else
                                                                                    <i class="fa fa-globe"
                                                                                        aria-hidden="true"></i>
                                                                                @endif
                                                                            </a>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @empty
                                        <div class="leadership-empty-state">
                                            <i class="fa fa-users fa-2x mb-3"></i>

                                            <h6>No Student Leaders Found</h6>

                                            <p class="mb-0">
                                                There are no student representatives for this academic session.
                                            </p>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- ASSOCIATION ADVISORS --}}
                                <div class="leadership-section advisors-section">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <div class="leadership-section-icon advisor-icon">
                                            <i class="fa fa-user-tie"></i>
                                        </div>

                                        <div>
                                            <h4 class="mb-1">Association Advisors</h4>
                                            <p class="text-muted mb-0">
                                                Guidance and support for the association
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        @forelse ($data['advisors'] as $advisor)
                                            @php
                                                $advisorPhoto = $advisor->photo
                                                    ? 'assets/tenant_' .
                                                        tenant()->id .
                                                        '/uploads/leadership/advisor/' .
                                                        $advisor->photo
                                                    : null;

                                                $hasAdvisorPhoto =
                                                    $advisorPhoto && file_exists(public_path($advisorPhoto));
                                            @endphp

                                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                                <div class="advisor-profile-card h-100">
                                                    <div class="advisor-photo">
                                                        @if ($hasAdvisorPhoto)
                                                            <img src="{{ asset($advisorPhoto) }}"
                                                                alt="{{ $advisor->name }}" class="advisor-photo-image"
                                                                loading="lazy">
                                                        @else
                                                            <img src="{{ asset('system/images/default_profile_pic.webp') }}"
                                                                alt="{{ $advisor->name }}" class="advisor-photo-image"
                                                                loading="lazy">
                                                        @endif
                                                    </div>

                                                    <div class="advisor-details">
                                                        <h6>{{ $advisor->name }}</h6>

                                                        @if ($advisor->position)
                                                            <div class="advisor-position">
                                                                {{ $advisor->position }}
                                                            </div>
                                                        @endif

                                                        @if ($advisor->remarks)
                                                            <p class="advisor-remarks">
                                                                {{ $advisor->remarks }}
                                                            </p>
                                                        @endif

                                                        <div class="advisor-service-period">
                                                            <i class="fa fa-calendar-alt me-1"></i>
                                                            {{ $advisor->start_date?->format('F Y') ?? 'N/A' }}
                                                            –
                                                            {{ $advisor->end_date?->format('F Y') ?? 'Present' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="leadership-empty-state">
                                                    <i class="fa fa-user-tie fa-2x mb-3"></i>

                                                    <h6>No Advisors Found</h6>

                                                    <p class="mb-0">
                                                        No advisors were found for this academic session.
                                                    </p>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .association-leadership-page {
                    --leadership-green: #006a4e;
                    --leadership-navy: #002147;
                    --leadership-blue: #1696e7;
                    --leadership-muted: #687780;
                    --leadership-border: #e5ece8;
                    color: #25352f;
                }

                .leadership-section {
                    margin-bottom: 48px;
                }

                .leadership-section>.d-flex {
                    padding-bottom: 16px;
                    border-bottom: 1px solid var(--leadership-border);
                }

                .leadership-section h4 {
                    color: var(--leadership-navy);
                    font-size: 1.25rem;
                    font-weight: 750;
                    letter-spacing: -0.02em;
                }

                .leadership-section-icon {
                    width: 46px;
                    height: 46px;
                    flex: 0 0 46px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #e8f4ef;
                    color: var(--leadership-green);
                    border-radius: 14px;
                    font-size: 19px;
                }

                .advisor-icon {
                    background: #eaf0f8;
                    color: var(--leadership-navy);
                }

                .leadership-arm {
                    margin-top: 24px;
                }

                .leadership-arm-heading {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 14px;
                    margin: 24px 0 18px;
                    text-align: center;
                }

                .leadership-arm-heading h5 {
                    color: var(--leadership-green);
                    font-weight: 750;
                    font-size: 1.02rem;
                }

                .leadership-arm-line {
                    height: 1px;
                    background: #dce7e1;
                    flex: 1;
                    max-width: 150px;
                }

                .leader-profile-card,
                .advisor-profile-card {
                    height: 100%;
                    min-width: 0;
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                    border: 1px solid var(--leadership-border);
                    border-radius: 18px;
                    background: #fff;
                    box-shadow: 0 4px 16px rgba(20, 45, 35, 0.05);
                    transition: transform 0.2s ease,
                        box-shadow 0.2s ease,
                        border-color 0.2s ease;
                }

                .leader-profile-card:hover,
                .advisor-profile-card:hover {
                    transform: translateY(-4px);
                    border-color: #cbded4;
                    box-shadow: 0 14px 30px rgba(20, 45, 35, 0.10);
                }

                .leader-photo,
                .advisor-photo {
                    width: 100%;
                    aspect-ratio: 1 / 1;
                    overflow: hidden;
                    background: #f4f7f5;
                    border-bottom: 1px solid #edf1ee;
                }

                .leader-photo img,
                .advisor-photo img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                }

                .leader-profile-details,
                .advisor-details {
                    display: flex;
                    flex: 1;
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                    padding: 16px 16px 15px;
                }

                .leader-name,
                .advisor-details h6 {
                    color: #243a31;
                    font-size: 0.98rem;
                    line-height: 1.35;
                    font-weight: 750;
                    margin: 0 0 6px;
                    overflow-wrap: anywhere;
                }

                .leader-position,
                .advisor-position {
                    display: inline-block;
                    max-width: 100%;
                    padding: 5px 10px;
                    margin-bottom: 10px;
                    border-radius: 999px;
                    background: #eaf5ef;
                    color: var(--leadership-green);
                    font-size: 0.74rem;
                    line-height: 1.3;
                    font-weight: 700;
                    overflow-wrap: anywhere;
                }

                .advisor-position {
                    background: #edf2f9;
                    color: var(--leadership-navy);
                }

                .leader-remarks,
                .advisor-remarks {
                    width: 100%;
                    color: var(--leadership-muted);
                    font-size: 0.83rem;
                    line-height: 1.55;
                    margin: 0;
                    overflow-wrap: anywhere;
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }

                .leader-social-links {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 8px;
                    margin-top: auto;
                    padding-top: 14px;
                }

                .leader-social-link {
                    width: 34px;
                    height: 34px;
                    flex: 0 0 34px;
                    border: 1px solid #e2e9e5;
                    border-radius: 10px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--leadership-green);
                    text-decoration: none;
                    background: #fff;
                    transition: background 0.2s ease,
                        border-color 0.2s ease,
                        color 0.2s ease;
                }

                .leader-social-link:hover {
                    background: var(--leadership-green);
                    border-color: var(--leadership-green);
                    color: #fff;
                }

                .leader-social-link img {
                    width: 18px;
                    height: 18px;
                    object-fit: contain;
                }

                .advisor-service-period {
                    width: 100%;
                    margin-top: auto;
                    padding-top: 12px;
                    border-top: 1px solid #edf1ee;
                    color: var(--leadership-muted);
                    font-size: 0.78rem;
                }

                .leadership-empty-state {
                    padding: 34px 20px;
                    text-align: center;
                    border: 1px dashed #cddbd3;
                    border-radius: 16px;
                    color: #78857e;
                    background: #fafcfb;
                }

                .leadership-empty-state h6 {
                    color: #33483e;
                    font-weight: 700;
                }

                @media (max-width: 575.98px) {
                    .leadership-section {
                        margin-bottom: 34px;
                    }

                    .leadership-section-icon {
                        width: 40px;
                        height: 40px;
                        flex-basis: 40px;
                        border-radius: 12px;
                    }

                    .leadership-section h4 {
                        font-size: 1.08rem;
                    }

                    .leader-profile-details,
                    .advisor-details {
                        padding: 14px 12px;
                    }
                }

                @media (prefers-reduced-motion: reduce) {

                    .leader-profile-card,
                    .advisor-profile-card,
                    .leader-social-link {
                        transition: none;
                    }
                }

                /* Smaller cards on large screens */
                @media (min-width: 992px) {

                    .leader-profile-card,
                    .advisor-profile-card {
                        border-radius: 15px;
                    }

                    .leader-photo,
                    .advisor-photo {
                        aspect-ratio: 4 / 5;
                    }

                    .leader-profile-details,
                    .advisor-details {
                        padding: 13px 12px 12px;
                    }

                    .leader-name,
                    .advisor-details h6 {
                        font-size: 0.9rem;
                        margin-bottom: 5px;
                    }

                    .leader-position,
                    .advisor-position {
                        padding: 4px 8px;
                        font-size: 0.7rem;
                        margin-bottom: 8px;
                    }

                    .leader-remarks,
                    .advisor-remarks {
                        font-size: 0.78rem;
                        line-height: 1.45;
                    }

                    .leader-social-link {
                        width: 30px;
                        height: 30px;
                        flex: 0 0 30px;
                        border-radius: 9px;
                    }

                    .leader-social-link img {
                        width: 16px;
                        height: 16px;
                    }

                    .advisor-service-period {
                        font-size: 0.72rem;
                        padding-top: 10px;
                    }
                }

                /* Even smaller on extra-large screens */
                @media (min-width: 1200px) {

                    .leader-photo,
                    .advisor-photo {
                        aspect-ratio: 1 / 1;
                    }

                    .leader-profile-details,
                    .advisor-details {
                        padding: 12px 11px 11px;
                    }

                    .leader-name,
                    .advisor-details h6 {
                        font-size: 0.87rem;
                    }

                    .leader-remarks,
                    .advisor-remarks {
                        font-size: 0.75rem;
                    }
                }
            </style>
        </div>
    </div>
@endsection
