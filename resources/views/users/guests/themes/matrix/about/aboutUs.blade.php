@extends('users.guests.themes.matrix.layout.matrixLayout')

@section('title', 'About Us')

@section('content')


    @php
        $basicInfo = $data['basicInfo'] ?? null;
        $systemInfo = basicSystemInfo();

        $associationName = $systemInfo->association_name ?? ($systemInfo->association_acronym ?? config('app.name'));

        $associationAcronym = $systemInfo->association_acronym ?? config('app.name');

        $schoolName = $systemInfo->school_name ?? ($systemInfo->school_acronym ?? '');

        $contactEmail = $basicInfo->email ?? null;
        $contactPhone = $basicInfo->phone ?? null;
        $contactAddress = $basicInfo->address ?? null;
        $facebookLink = $basicInfo->facebook_link ?? null;
        $instagramLink = $basicInfo->instagram_link ?? null;
        $twitterLink = $basicInfo->twitter_link ?? null;
        $linkedinLink = $basicInfo->linkedin_link ?? null;

    @endphp
    <div class="custom-page-container">
        <main class="mx-page p-4 p-md-5 pt-5 mt-5">
            {{-- ABOUT --}}
            <div class="container-fluid">

                <section class="mx-section mx-section-white">
                    <div class="row g-4 align-items-stretch">

                        <div class="col-md-12">
                            <div class="mx-copy">
                                {{-- <span class="mx-kicker">Our Story</span> --}}

                                <h2 class="fw-bold">
                                    About <span>{{ $associationName }}</span>
                                </h2>

                                <div class="mx-prose">
                                    @if (!empty($basicInfo?->description))
                                        {!! $basicInfo->description !!}
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-md-5">
                        <div class="mx-feature-panel">
                            <div class="mx-feature-item">
                                <div class="mx-feature-icon">
                                    <i class="fa-solid fa-bullseye"></i>
                                </div>
    
                                <div>
                                    <h3>Our Purpose</h3>
                                    <p>
                                        To represent members, encourage participation, and advance
                                        the objectives of the association through responsible leadership.
                                    </p>
                                </div>
                            </div>
    
                            <div class="mx-feature-item">
                                <div class="mx-feature-icon">
                                    <i class="fa-solid fa-eye"></i>
                                </div>
    
                                <div>
                                    <h3>Our Vision</h3>
                                    <p>
                                        To cultivate a connected, progressive, and engaged community
                                        where members can contribute and grow.
                                    </p>
                                </div>
                            </div>
    
                            <div class="mx-feature-item">
                                <div class="mx-feature-icon">
                                    <i class="fa-solid fa-people-group"></i>
                                </div>
    
                                <div>
                                    <h3>Our Community</h3>
                                    <p>
                                        To encourage unity, mutual support, shared responsibility,
                                        and meaningful involvement in association activities.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    </div>
                </section>

            @section('presidentData')
                @php
                    $president = $data['thePresident'] ?? null;
                    $presidentRep = $president?->theRep;
                    $presidentProfile = $presidentRep?->memberPersonalProfile;

                    $presidentPhoto = asset('system/images/default_profile_pic.webp');

                    if (
                        $presidentRep?->photo &&
                        file_exists(
                            public_path(
                                'assets/tenant_' .
                                    tenant()->id .
                                    '/uploads/users/members/photo/' .
                                    $presidentRep->photo,
                            ),
                        )
                    ) {
                        $presidentPhoto = asset(
                            'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $presidentRep->photo,
                        );
                    }

                    $middleInitial = trim(strtoupper(substr($presidentProfile?->middle_name ?? '', 0, 1)));

                    $presidentName = trim(
                        ($presidentProfile?->first_name ?? '') .
                            ' ' .
                            ($middleInitial ? $middleInitial . '.' : '') .
                            ' ' .
                            ($presidentProfile?->last_name ?? ''),
                    );
                @endphp
            @endsection

            @if (!empty($data['thePresident']))
                @php
                    $presidentRep = $data['thePresident']->theRep ?? null;
                    $presidentProfile = $presidentRep->memberPersonalProfile ?? null;

                    $presidentPhoto = asset('system/images/default_profile_pic.webp');

                    if (
                        $presidentRep?->photo &&
                        file_exists(
                            public_path(
                                'assets/tenant_' .
                                    tenant()->id .
                                    '/uploads/users/members/photo/' .
                                    $presidentRep->photo,
                            ),
                        )
                    ) {
                        $presidentPhoto = asset(
                            'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $presidentRep->photo,
                        );
                    }

                    $middleInitial = trim(strtoupper(substr($presidentProfile?->middle_name ?? '', 0, 1)));

                    $presidentName = trim(
                        ($presidentProfile?->first_name ?? '') .
                            ' ' .
                            ($middleInitial ? $middleInitial . '.' : '') .
                            ' ' .
                            ($presidentProfile?->last_name ?? ''),
                    );
                @endphp

                <section class="mx-section mx-section-dark p-5">
                    <div class="container-fluid">
                        <div class="mx-president-panel">

                            <div class="mx-president-layout">

                                {{-- Portrait --}}
                                <div class="mx-president-portrait">
                                    <div class="mx-president-portrait-frame">
                                        <img src="{{ $presidentPhoto }}"
                                            alt="{{ $presidentName ?: 'Association President' }}">
                                    </div>

                                    <div class="mx-president-portrait-details">
                                        <strong>{{ $presidentName ?: 'Association President' }}</strong>

                                        <span>
                                            President, {{ strtoupper($associationAcronym) }}
                                        </span>

                                        @if (!empty($data['currentAcademicYear']))
                                            <small>
                                                {{ $data['currentAcademicYear'] }}/{{ $data['currentAcademicYear'] + 1 }}
                                                Session
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Opening remark beside the portrait --}}
                                <div class="mx-president-lead">
                                    <div class="mx-quote-icon">
                                        <i class="fa-solid fa-quote-left"></i>
                                    </div>

                                    <span class="mx-kicker">
                                        Message From Our Leadership
                                    </span>

                                    <h2>President’s Remarks</h2>

                                    <div class="mx-quote-body mx-president-lead-text">
                                        @if (!empty($basicInfo?->president_remarks))
                                            {!! $basicInfo->president_remarks !!}
                                        @else
                                            <p>
                                                Welcome to {{ $associationName }}. Our association is built on
                                                representation, cooperation, responsibility, and service to members.
                                            </p>

                                            <p>
                                                Through participation, constructive ideas, and collective effort,
                                                we can strengthen our community and create meaningful opportunities
                                                for development.
                                            </p>
                                        @endif
                                    </div>

                                    <div class="mx-president-signature">
                                        <div class="mx-quote-avatar">
                                            <i class="fa-solid fa-user-tie"></i>
                                        </div>

                                        <div>
                                            <strong>{{ $presidentName ?: 'Association President' }}</strong>
                                            <small>
                                                President, {{ strtoupper($associationAcronym) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </section>
            @endif
            {{-- ############################################################################### --}}

            {{-- ########################################################## --}}
            {{-- LEADERS --}}
            <section class="mx-section mx-section-light">
                <div class="container px-3">

                    <div class="mx-section-heading text-center">
                        <span class="mx-kicker">Meet The Team</span>

                        <h2>Our Association Leaders</h2>

                        <p>
                            Meet the offices responsible for representing members, coordinating
                            activities, and advancing the association’s objectives.
                        </p>
                    </div>

                    <div class="association-leadership-page">

                        {{-- STUDENT LEADERS --}}
                        <div class="leadership-section mb-5">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                                @if (intval($data['academicSession']) !== intval(currentAcademicYear()))
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="leadership-section-icon">
                                            <i class="fa fa-users"></i>
                                        </div>

                                        <div>
                                            <h4 class="mb-1">Previous Leadership</h4>

                                            <p class="text-muted mb-0">
                                                Association representatives for the
                                                {{ $data['academicSession'] }}/{{ $data['academicSession'] + 1 }}
                                                academic session
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#sessionSelector">
                                    <i class="fa-solid fa-calendar-days me-2"></i>
                                    Change Session
                                </button>
                            </div>

                            {{-- ################################################# --}}
                            <div class="modal fade" id="sessionSelector" tabindex="-1"
                                aria-labelledby="sessionSelectorLabel" aria-hidden="true">

                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="sessionSelectorLabel">
                                                <i class="fa-solid fa-calendar-days me-2"></i>
                                                Change Academic Session
                                            </h5>

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close">
                                            </button>
                                        </div>

                                        {{-- Changed to plain GET without CSRF token to keep URL clean --}}
                                        <form method="GET" action="{{ request()->url() }}">

                                            <div class="modal-body">
                                                <p class="text-muted small mb-3">
                                                    Select an academic session to view its leadership tenure.
                                                </p>

                                                <div class="mb-3">
                                                    <label for="academic_session" class="form-label fw-semibold">
                                                        Academic Session <span class="text-danger">*</span>
                                                    </label>

                                                    {{-- Fixed: text-center moved to class attribute --}}
                                                    <select name="academic_session" id="academic_session"
                                                        class="form-select text-center" required>
                                                        <option value="" disabled>
                                                            Select academic session
                                                        </option>

                                                        @php
                                                            $startYear = schoolFoundingYear();
                                                            $endYear = currentAcademicYear();
                                                            $selectedSession = request(
                                                                'academic_session',
                                                                $data['academicSession'] ?? $endYear,
                                                            );
                                                        @endphp

                                                        @for ($year = $endYear; $year >= $startYear; $year--)
                                                            <option value="{{ $year }}"
                                                                {{ $selectedSession == $year ? 'selected' : '' }}>
                                                                {{ $year }}/{{ $year + 1 }}
                                                                {{ $year == $endYear ? ' — Current Session' : '' }}
                                                            </option>
                                                        @endfor
                                                    </select>

                                                    @error('academic_session')
                                                        <div class="text-danger small mt-2">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                {{-- Fixed: Removed 'bg-red' from type attribute --}}
                                                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>

                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa-solid fa-filter me-2"></i>
                                                    Apply Session
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            {{-- ########################################################## --}}

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
                                                                alt="Default profile picture" class="leader-photo-image"
                                                                loading="lazy">
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
                        @if ($data['advisors']->isNotEmpty())
                            <div class="leadership-section advisors-section">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="leadership-section-icon advisor-icon">
                                        <i class="fa fa-user-tie"></i>
                                    </div>

                                    <div>
                                        <h4 class="mb-1">Guidance & support</h4>
                                        <p class="text-muted mb-0">
                                            The advisers and representatives who provide guidance, institutional
                                            support, and
                                            direction to the association.
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

                                            $hasAdvisorPhoto = $advisorPhoto && file_exists(public_path($advisorPhoto));
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
                        @endif
                    </div>

                </div>
            </section>

            {{-- CONTACT --}}
            <section class="mx-section mx-section-light">
                <div class="container">

                    <div class="mx-section-heading text-center">
                        <span class="mx-kicker">Get In Touch</span>

                        <h2>Contact The Association</h2>

                        <p>
                            Have a question, suggestion, or enquiry?
                            Reach out through any of the available contact channels.
                        </p>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="mx-contact-card">
                                <div class="mx-contact-icon">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>

                                <div>
                                    <h3>Email Address</h3>

                                    @if ($contactEmail)
                                        <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                                    @else
                                        <p>Not available</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="mx-contact-card">
                                <div class="mx-contact-icon">
                                    <i class="fa-solid fa-phone"></i>
                                </div>

                                <div>
                                    <h3>Phone Number</h3>

                                    @if ($contactPhone)
                                        <a href="tel:{{ $contactPhone }}">{{ $contactPhone }}</a>
                                    @else
                                        <p>Not available</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="mx-contact-card">
                                <div class="mx-contact-icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>

                                <div>
                                    <h3>Address</h3>

                                    @if ($contactAddress)
                                        <p>{{ $contactAddress }}</p>
                                    @else
                                        <p>Not available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (
                        !empty($basicInfo->facebook_link) ||
                            !empty($basicInfo->instagram_link) ||
                            !empty($basicInfo->twitter_link) ||
                            !empty($basicInfo->linkedin_link))
                        <div class="mx-social-panel">
                            <div class="mx-social-heading">
                                <span class="mx-kicker">Follow Us</span>

                                <h3>Connect With {{ $associationAcronym }}</h3>

                                <p>
                                    Follow the association on social media for updates,
                                    announcements, activities, and community engagement.
                                </p>
                            </div>

                            <div class="mx-social-links">
                                @if (!empty($basicInfo->facebook_link))
                                    <a href="{{ $basicInfo->facebook_link }}" target="_blank"
                                        rel="noopener noreferrer" class="mx-social-link facebook">
                                        <i class="fa-brands fa-facebook-f"></i>
                                        <span>Facebook</span>
                                    </a>
                                @endif

                                @if (!empty($basicInfo->instagram_link))
                                    <a href="{{ $basicInfo->instagram_link }}" target="_blank"
                                        rel="noopener noreferrer" class="mx-social-link instagram">
                                        <i class="fa-brands fa-instagram"></i>
                                        <span>Instagram</span>
                                    </a>
                                @endif

                                @if (!empty($basicInfo->twitter_link))
                                    <a href="{{ $basicInfo->twitter_link }}" target="_blank"
                                        rel="noopener noreferrer" class="mx-social-link twitter">
                                        <i class="fa-brands fa-twitter"></i>
                                        <span>X / Twitter</span>
                                    </a>
                                @endif

                                @if (!empty($basicInfo->linkedin_link))
                                    <a href="{{ $basicInfo->linkedin_link }}" target="_blank"
                                        rel="noopener noreferrer" class="mx-social-link linkedin">
                                        <i class="fa-brands fa-linkedin-in"></i>
                                        <span>LinkedIn</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            </section>
            {{-- CTA --}}
            <section class="mx-section mx-section-white">
                <div class="container">
                    <div class="mx-cta">
                        <span class="mx-kicker">Get Involved</span>

                        <h2>Our Community. Our Future.</h2>

                        <p>
                            Stay informed, participate in association initiatives, and contribute
                            to building a stronger and more connected community.
                        </p>

                        <div class="mx-actions">
                            <a href="{{ route('homepage') }}" class="mx-button">
                                <i class="fa-solid fa-house"></i>
                                Back to Home
                            </a>

                            @auth
                                <a href="{{ route('homepage') }}" class="mx-button mx-button-outline">
                                    <i class="fa-solid fa-arrow-right"></i>
                                    Continue to Portal
                                </a>
                            @else
                                <a href="{{ route('loginPage') }}" class="mx-button mx-button-outline">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                    Member Login
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </main>

</div>

@endsection
