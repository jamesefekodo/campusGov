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

                                <span class="ms-5 badge bg-warning text-black" data-bs-toggle="modal"
                                    data-bs-target="#sessionSelector">
                                    {{ $data['academicSession'] }}/{{ $data['academicSession'] + 1 }}
                                    <i class="fa fa-filter"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    @include('users.admin.leadership.studentReps.include.filterStudentRepsByYearModal')

                    <div class="col-md-12 m-auto p-4">
                        <div class="my_form_container">
                            <div class="association-leadership-page">

                                {{-- STUDENT LEADERS --}}
                                <div class="leadership-section mb-5">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <div class="leadership-section-icon">
                                            <i class="fa fa-users"></i>
                                        </div>

                                        <div>
                                            <h4 class="mb-1">Student Leadership | @php
                                                    $tenureName = $data['theTenure']?->name;
                                                @endphp

                                                @if (filled($tenureName) && !is_numeric($tenureName))
                                                    ({{ $tenureName }}
                                                @endif Tenure)</h4>
                                            <p class="text-muted mb-0">
                                                Association representatives for
                                                {{ $data['academicSession'] }}/{{ $data['academicSession'] + 1 }} academic
                                                session
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
                                                                @php
                                                                    $profile = $representative?->memberPersonalProfile;

                                                                    $fullName = collect([
                                                                        $profile?->first_name,
                                                                        $profile?->middle_name
                                                                            ? strtoupper(
                                                                                    substr($profile->middle_name, 0, 1),
                                                                                ) . '.'
                                                                            : null,
                                                                        $profile?->last_name,
                                                                    ])
                                                                        ->filter()
                                                                        ->implode(' ');

                                                                    $displayName = filled($fullName)
                                                                        ? $fullName
                                                                        : $representative?->username;
                                                                @endphp

                                                                <h6 class="leader-name">
                                                                    {{ $displayName }}
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
        </div>
    </div>
@endsection
