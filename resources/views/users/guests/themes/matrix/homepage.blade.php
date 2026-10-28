@extends('users.guests.themes.matrix.layout.matrixLayout')
@section('title', 'Home')
@section('content')

    @if ($data['sliders']->isNotEmpty())
        <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="6000">
            <div class="carousel-indicators">
                @foreach ($data['sliders'] as $key => $slider)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $key }}"
                        class="{{ $key === 0 ? 'active' : '' }}"></button>
                @endforeach
            </div>

            <div class="carousel-inner">
                @foreach ($data['sliders'] as $key => $slider)
                    <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                        <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/sliders/' . $slider->background_image) }}"
                            alt="{{ $slider->header }}">

                        <div class="carousel-caption">
                            <div class="glass-card">
                                <h1>{{ $slider->header }}</h1>
                                <h3>{{ $slider->description }}</h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    @endif

    {{-- about us --}}
    <section class="homepage-about">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-stretch">

                <div class="col-lg-7">
                    <div class="homepage-about-copy">
                        <span class="mx-kicker">Who We Are</span>

                        <h2>
                            About <span>{{ $data['basicInfo']->association_name ?? config('app.name') }}</span>
                        </h2>

                        <div class="mx-prose">
                            @php
                                $aboutText =
                                    $data['basicInfo']->description ??
                                    'Our association brings members together around shared interests, representation, participation, and collective development.';
                            @endphp

                            <p style="text-align: justify">
                                {{ \Illuminate\Support\Str::limit(strip_tags(html_entity_decode($aboutText)), 320, '...') }}
                            </p>
                        </div>

                        <div class="mx-actions justify-content-start mt-4">
                            <a href="{{ route('guestAboutUs') }}" class="mx-button">
                                Learn More
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="homepage-about-media">
                        <div class="homepage-about-logo-card">
                            <img src="{{ associationLogo() }}"
                                alt="{{ $data['basicInfo']->association_name ?? config('app.name') }} logo"
                                class="img-fluid homepage-about-logo">
                        </div>

                        <div class="homepage-about-badge">
                            <i class="fa-solid fa-landmark"></i>
                            {{ $data['basicInfo']->association_acronym ?? config('app.name') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fa fa-user-graduate"></i>
                        <h3 class="stat-number" data-target="{{ $data['undergraduates'] }}" data-suffix="+">0</h3>
                        <p>Undergraduates</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fa fa-user-tie"></i>
                        <h3 class="stat-number" data-target="{{ $data['alumni'] }}" data-suffix="+">0</h3>
                        <p>Alumni Networds</p>
                    </div>
                </div>
                @if ($data['operationalLevel'] == 'institute')
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="fa fa-building-columns"></i>
                            <h3 class="stat-number" data-target="{{ $data['faculties']->count() }}" data-suffix="">0
                            </h3>
                            <p>Faculties</p>
                        </div>
                    </div>
                @elseif ($data['operationalLevel'] == 'faculty')
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="fa fa-sitemap"></i>
                            <h3 class="stat-number" data-target="{{ $data['departments']->count() }}" data-suffix="">0
                            </h3>
                            <p>Departments</p>
                        </div>
                    </div>
                @else
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="fa fa-book-open"></i>
                            <h3 class="stat-number" data-target="{{ $data['totalAcademicResources'] }}" data-suffix="">0
                            </h3>
                            <p>Academic Resources</p>
                        </div>

                    </div>
                @endif



            </div>
        </div>
    </section>


    @if ($data['thePresident'])
        <div class="container my-5">
            <div class="row align-items-center g-4">
                <div class="col-md-6">
                    {{-- ###################### --}}
                    @php
                        $presidentPhoto = asset('system/images/default_profile_pic.webp');
                        if (
                            $data['thePresident']->theRep->photo &&
                            file_exists(
                                public_path(
                                    'assets/tenant_' .
                                        tenant()->id .
                                        '/uploads/users/members/photo/' .
                                        $data['thePresident']->theRep->photo,
                                ),
                            )
                        ) {
                            $presidentPhoto = asset(
                                'assets/tenant_' .
                                    tenant()->id .
                                    '/uploads/users/members/photo/' .
                                    $data['thePresident']->theRep->photo,
                            );
                        }

                        $profile = $data['thePresident']->theRep->memberPersonalProfile;

                        $middleInitial = trim(strtoupper(substr($profile->middle_name ?? '', 0, 1)));
                    @endphp
                    {{-- ########################### --}}

                    <div class="position-relative rounded overflow-hidden shadow-sm">
                        <img src="{{ $presidentPhoto }}" class="img-fluid w-100 president-img" alt="President">

                        <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white overlay-glass text-center">

                            <h4 class="mb-1">
                                {{ $profile->first_name }}
                                @if ($middleInitial)
                                    {{ $middleInitial }}.
                                @endif
                                {{ $profile->last_name }}
                            </h4>
                            <p class="mb-0">President, {{ strtoupper($data['basicInfo']->association_acronym) }},
                                {{ $data['currentAcademicYear'] }}/{{ $data['currentAcademicYear'] + 1 }} Session.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-4 rounded shadow-sm overlay-glass">
                        <h3 style="color: var(--theme-primary)">President's Remarks</h3>

                        <p class="text-justify" style="text-align: justify">
                            {!! \Illuminate\Support\Str::words(
                                strip_tags(html_entity_decode($data['basicInfo']->president_remarks)),
                                150,
                                '...',
                            ) !!}
                        </p>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('guestAboutUs') }}" class="btn btn-primary">More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- <section class="services-section">
        <div class="container my-5">
            <h2 class="section-title">Student <span>Associations</span></h2>

            <div class="row g-4">
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="association-card">
                        <img src="https://placehold.co/300x200/0f172a/ffffff?text=NAPES"
                            class="img-fluid association-logo mb-3" alt="NAPES">

                        <h5 class="text-primary mb-2 fw-bold">NAPES</h5>
                        <p class="text-white small mb-3">National Association of Polytechnic Engineering Students.</p>
                        <a href="#" class="btn btn-red btn-sm px-4">View</a>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="association-card">
                        <img src="https://placehold.co/300x200/0f172a/ffffff?text=NAEES"
                            class="img-fluid association-logo mb-3" alt="NAEES">

                        <h5 class="text-primary mb-2 fw-bold">NAEES</h5>
                        <p class="text-white small mb-3">National Association of Electrical Engineering Students.</p>
                        <a href="#" class="btn btn-red btn-sm px-4">View</a>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="association-card">
                        <img src="https://placehold.co/300x200/0f172a/ffffff?text=NACOMES"
                            class="img-fluid association-logo mb-3" alt="NACOMES">

                        <h5 class="text-primary mb-2 fw-bold">NACOMES</h5>
                        <p class="text-white small mb-3">National Association of Computer Engineering Students.</p>
                        <a href="#" class="btn btn-red btn-sm px-4">View</a>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="association-card">
                        <img src="https://placehold.co/300x200/0f172a/ffffff?text=NAMES"
                            class="img-fluid association-logo mb-3" alt="NAMES">

                        <h5 class="text-primary mb-2 fw-bold">NAMES</h5>
                        <p class="text-white small mb-3">National Association of Mechanical Engineering Students.</p>
                        <a href="#" class="btn btn-red btn-sm px-4">View</a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    @php
        $showfaculties = false;
        $showSingleFaculty = false;
        $showDepartment = false;
        if (basicSystemInfo()->operational_level == 'institute' && activeFaculties()->isNotEmpty()) {
            $showfaculties = true;
        } elseif (basicSystemInfo()->operational_level == 'faculty' && activeDepartments()->isNotEmpty()) {
            $showSingleFaculty = true;
            $showDepartment = true;
        }
    @endphp

    {{-- faculties start --}}
    @if ($showfaculties)
        <section class="services-section">
            <div class="container">
                <h2 class="section-title">
                    Our <span>Faculties</span>
                </h2>

                <div class="row g-4">
                    @php
                        $facultyIconKeywords = [
                            'engineering' => 'fas fa-gears',
                            'science' => 'fas fa-flask-vial',
                            'environment' => 'fas fa-leaf',
                            'health' => 'fas fa-heart-pulse',
                            'medical' => 'fas fa-stethoscope',
                            'medicine' => 'fas fa-stethoscope',
                            'nursing' => 'fas fa-user-nurse',
                            'pharmacy' => 'fas fa-pills',
                            'law' => 'fas fa-scale-balanced',
                            'legal' => 'fas fa-gavel',
                            'business' => 'fas fa-briefcase',
                            'management' => 'fas fa-clipboard-list',
                            'administration' => 'fas fa-clipboard-list',
                            'account' => 'fas fa-calculator',
                            'finance' => 'fas fa-money-bill-trend-up',
                            'banking' => 'fas fa-building-columns',
                            'social' => 'fas fa-people-group',
                            'education' => 'fas fa-graduation-cap',
                            'agric' => 'fas fa-tractor',
                            'art' => 'fas fa-palette',
                            'design' => 'fas fa-pen-ruler',
                            'communication' => 'fas fa-comments',
                            'information' => 'fas fa-database',
                            'technology' => 'fas fa-microchip',
                        ];
                    @endphp

                    @foreach ($data['faculties'] as $faculty)
                        @php
                            $facultyName = strtolower($faculty->name ?? '');
                            $iconClass = 'fas fa-building-columns';

                            foreach ($facultyIconKeywords as $keyword => $icon) {
                                if (str_contains($facultyName, $keyword)) {
                                    $iconClass = $icon;
                                    break;
                                }
                            }
                        @endphp

                        <div class="col-sm-6 col-md-4">
                            <div class="service-card">
                                <i class="{{ $iconClass }} w-100 text-center"></i>

                                <h3 class="w-100 text-center">
                                    {{ $faculty->name }}
                                    @if ($faculty->acronym)
                                        ({{ $faculty->acronym }})
                                    @endif
                                </h3>

                                <p>
                                    {!! \Illuminate\Support\Str::words(strip_tags(html_entity_decode($faculty->description ?? '')), 12, '...') !!}
                                </p>

                                <a href="{{ route('guestViewFaculty', ['uuid' => $faculty->uuid]) }}"
                                    class="w-100 text-end d-block">
                                    Learn More
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    {{-- faculties end --}}

    {{-- departments start --}}
    @if ($showDepartment)
        <section class="services-section">
            <div class="container">
                <h2 class="section-title">
                    Our <span>Faculty/Departments</span>
                </h2>

                <div class="row g-4">
                    @php
                        $iconKeywords = [
                            // Engineering
                            'mechanical' => 'fas fa-gears',
                            'electrical' => 'fas fa-bolt',
                            'electronic' => 'fas fa-microchip',
                            'computer' => 'fas fa-laptop-code',
                            'software' => 'fas fa-code',
                            'civil' => 'fas fa-helmet-safety',
                            'chemical' => 'fas fa-flask',
                            'petroleum' => 'fas fa-oil-well',
                            'gas' => 'fas fa-fire-flame-simple',
                            'material' => 'fas fa-layer-group',
                            'production' => 'fas fa-industry',
                            'industrial' => 'fas fa-industry',
                            'mechatronic' => 'fas fa-robot',
                            'robot' => 'fas fa-robot',
                            'agric' => 'fas fa-tractor',
                            'biomedical' => 'fas fa-heart-pulse',
                            'telecom' => 'fas fa-tower-broadcast',
                            'aeronautic' => 'fas fa-jet-fighter-up',
                            'aviation' => 'fas fa-plane',
                            'maritime' => 'fas fa-ship',
                            'survey' => 'fas fa-ruler-combined',
                            'architecture' => 'fas fa-compass-drafting',
                            'weld' => 'fas fa-screwdriver-wrench',

                            // Science
                            'science' => 'fas fa-flask-vial',
                            'biology' => 'fas fa-dna',
                            'microbio' => 'fas fa-bacteria',
                            'chemistry' => 'fas fa-flask',
                            'physics' => 'fas fa-atom',
                            'mathematic' => 'fas fa-square-root-variable',
                            'statistics' => 'fas fa-chart-line',
                            'geology' => 'fas fa-mountain',
                            'geophysics' => 'fas fa-globe',
                            'environment' => 'fas fa-leaf',
                            'forestry' => 'fas fa-tree',
                            'fisheries' => 'fas fa-fish',
                            'nutrition' => 'fas fa-apple-whole',

                            // Health
                            'nursing' => 'fas fa-user-nurse',
                            'medicine' => 'fas fa-stethoscope',
                            'medical' => 'fas fa-stethoscope',
                            'health' => 'fas fa-heart-pulse',
                            'pharmacy' => 'fas fa-pills',
                            'dental' => 'fas fa-tooth',
                            'optometry' => 'fas fa-glasses',
                            'radiography' => 'fas fa-x-ray',
                            'physiotherapy' => 'fas fa-person-walking',

                            // Business / Management
                            'business' => 'fas fa-briefcase',
                            'account' => 'fas fa-calculator',
                            'banking' => 'fas fa-building-columns',
                            'finance' => 'fas fa-money-bill-trend-up',
                            'marketing' => 'fas fa-bullhorn',
                            'entrepreneur' => 'fas fa-lightbulb',
                            'management' => 'fas fa-clipboard-list',
                            'administration' => 'fas fa-clipboard-list',
                            'office' => 'fas fa-file-invoice',
                            'procurement' => 'fas fa-cart-shopping',
                            'logistics' => 'fas fa-truck-fast',
                            'transport' => 'fas fa-truck',
                            'insurance' => 'fas fa-shield-halved',
                            'hospitality' => 'fas fa-utensils',
                            'tourism' => 'fas fa-plane-departure',

                            // Social Sciences / Law
                            'law' => 'fas fa-scale-balanced',
                            'legal' => 'fas fa-gavel',
                            'mass' => 'fas fa-bullhorn',
                            'communication' => 'fas fa-comments',
                            'journalism' => 'fas fa-newspaper',
                            'media' => 'fas fa-photo-film',
                            'sociology' => 'fas fa-people-group',
                            'psychology' => 'fas fa-brain',
                            'political' => 'fas fa-landmark',
                            'public' => 'fas fa-landmark',
                            'economics' => 'fas fa-chart-pie',
                            'criminology' => 'fas fa-user-shield',

                            // Education / Humanities
                            'education' => 'fas fa-graduation-cap',
                            'teaching' => 'fas fa-chalkboard-user',
                            'english' => 'fas fa-book-open',
                            'language' => 'fas fa-language',
                            'linguistic' => 'fas fa-language',
                            'history' => 'fas fa-landmark-dome',
                            'religious' => 'fas fa-place-of-worship',
                            'islamic' => 'fas fa-mosque',
                            'christian' => 'fas fa-church',
                            'music' => 'fas fa-music',
                            'art' => 'fas fa-palette',
                            'design' => 'fas fa-pen-ruler',
                            'fashion' => 'fas fa-shirt',

                            // faculty general 'engineering' => 'fas fa-gears',
                            'science' => 'fas fa-flask-vial',
                            'environment' => 'fas fa-leaf',
                            'health' => 'fas fa-heart-pulse',
                            'medical' => 'fas fa-stethoscope',
                            'medicine' => 'fas fa-stethoscope',
                            'nursing' => 'fas fa-user-nurse',
                            'pharmacy' => 'fas fa-pills',
                            'law' => 'fas fa-scale-balanced',
                            'legal' => 'fas fa-gavel',
                            'business' => 'fas fa-briefcase',
                            'management' => 'fas fa-clipboard-list',
                            'administration' => 'fas fa-clipboard-list',
                            'account' => 'fas fa-calculator',
                            'finance' => 'fas fa-money-bill-trend-up',
                            'banking' => 'fas fa-building-columns',
                            'social' => 'fas fa-people-group',
                            'education' => 'fas fa-graduation-cap',
                            'agric' => 'fas fa-tractor',
                            'art' => 'fas fa-palette',
                            'design' => 'fas fa-pen-ruler',
                            'communication' => 'fas fa-comments',
                            'information' => 'fas fa-database',
                            'technology' => 'fas fa-microchip',
                        ];
                    @endphp

                    @if ($showSingleFaculty)

                        @foreach ($data['faculties'] as $faculty)
                            @php
                                $facultyName = strtolower($faculty->name ?? '');
                                $iconClass = 'fas fa-building-columns';

                                foreach ($iconKeywords as $keyword => $icon) {
                                    if (str_contains($facultyName, $keyword)) {
                                        $iconClass = $icon;
                                        break;
                                    }
                                }
                            @endphp

                            <div class="col-sm-6 col-md-4">
                                <div class="service-card">
                                    <i class="{{ $iconClass }} w-100 text-center"></i>

                                    <h3 class="w-100 text-center">
                                        {{ $faculty->name }}
                                        @if ($faculty->acronym)
                                            ({{ $faculty->acronym }})
                                        @endif
                                    </h3>

                                    <p>
                                        {!! \Illuminate\Support\Str::words(strip_tags(html_entity_decode($faculty->description ?? '')), 12, '...') !!}
                                    </p>

                                    <a href="{{ route('guestViewFaculty', ['uuid' => $faculty->uuid]) }}"
                                        class="w-100 text-end d-block">
                                        Learn More
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @foreach ($data['departments'] as $department)
                        @php
                            $departmentName = strtolower($department->name ?? '');
                            $iconClass = 'fas fa-building-columns';

                            foreach ($iconKeywords as $keyword => $icon) {
                                if (str_contains($departmentName, $keyword)) {
                                    $iconClass = $icon;
                                    break;
                                }
                            }
                        @endphp

                        {{-- <div class="col-sm-6 col-md-4 col-lg-3"> --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="service-card">
                                <i class="{{ $iconClass }} w-100 text-center"></i>

                                <h3 class="w-100 text-center">
                                    {{ $department->name }}
                                </h3>

                                <p>
                                    {!! \Illuminate\Support\Str::words(strip_tags(html_entity_decode($department->description ?? '')), 12, '...') !!}
                                </p>

                                <a href="{{ route('guestViewDepartment', ['uuid' => $department->uuid]) }}"
                                    class="w-100 text-end d-block">
                                    Learn More
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    {{-- departments end --}}

    @if ($data['studentLeaders']->isNotEmpty())
        <section class="leadership-slider-section">
            <div class="container">

                <div class="leadership-heading">
                    <span class="leadership-kicker">Leadership</span>

                    <h2>
                        Meet Our <span>Leaders</span>
                    </h2>

                    <p>
                        Meet the students representing and serving the association
                        for the current academic session.
                    </p>
                </div>

                <div class="leadership-slider">
                    <button class="slider-arrow slider-prev" type="button" aria-label="Previous leader">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div class="slider-viewport">
                        <div class="slider-track" id="leadershipSliderTrack">
                            @foreach ($data['studentLeaders'] as $leader)
                                @php
                                    $rep = $leader->theRep;

                                    $profile = $rep?->memberPersonalProfile;

                                    $leaderName = $profile
                                        ? trim(
                                            $profile->first_name .
                                                ' ' .
                                                ($profile->middle_name ? $profile->middle_name . ' ' : '') .
                                                $profile->last_name,
                                        )
                                        : $rep?->name ?? 'Association Leader';

                                    $leaderPosition = $leader->position?->name ?? 'Leader';
                                    $leaderArm = $leader->position?->arm?->name ?? '';

                                    $leaderPhoto = asset('system/images/default_profile_pic.webp');

                                    if (
                                        $rep?->photo &&
                                        file_exists(
                                            public_path(
                                                'assets/tenant_' .
                                                    tenant()->id .
                                                    '/uploads/users/members/photo/' .
                                                    $rep->photo,
                                            ),
                                        )
                                    ) {
                                        $leaderPhoto = asset(
                                            'assets/tenant_' .
                                                tenant()->id .
                                                '/uploads/users/members/photo/' .
                                                $rep->photo,
                                        );
                                    }
                                @endphp

                                <article class="leader-slide">
                                    <div class="leader-photo">
                                        <img src="{{ $leaderPhoto }}" alt="{{ $leaderName }}" loading="lazy">
                                    </div>

                                    <div class="leader-details">
                                        @if ($leaderArm)
                                            <span class="leader-arm">
                                                {{ $leaderArm }}
                                            </span>
                                        @endif

                                        <h3>{{ $leaderName }}</h3>
                                        <p>{{ $leaderPosition }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <button class="slider-arrow slider-next" type="button" aria-label="Next leader">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <div class="slider-dots" id="leadershipSliderDots"></div>
                </div>

            </div>
        </section>
    @endif

    @if ($data['publications']->isNotEmpty())
        <section class="publications-showcase">
            <div class="container">

                <div class="publications-heading">
                    <span class="publications-kicker">Media</span>

                    <h2>
                        Latest <span>Publications</span>
                    </h2>

                    <p>
                        Stay informed with selected updates, articles, and
                        announcements from the association.
                    </p>
                </div>

                <div class="row g-4">
                    @foreach ($data['publications']->take(3) as $publication)
                        @php
                            $coverImage = $publication->cover_image ?? null;

                            $coverPath = $coverImage
                                ? public_path(
                                    'assets/tenant_' . tenantId() . '/uploads/media/publications/' . $coverImage,
                                )
                                : null;

                            $hasCoverImage = $coverPath && file_exists($coverPath);

                            $coverUrl = $hasCoverImage
                                ? asset('assets/tenant_' . tenantId() . '/uploads/media/publications/' . $coverImage)
                                : asset('system/images/wide_img_placeholder.jpeg');

                            $authorName = $publication->authorUser?->memberPersonalProfile
                                ? trim(
                                    $publication->authorUser->memberPersonalProfile->first_name .
                                        ' ' .
                                        ($publication->authorUser->memberPersonalProfile->middle_name
                                            ? $publication->authorUser->memberPersonalProfile->middle_name . ' '
                                            : '') .
                                        $publication->authorUser->memberPersonalProfile->last_name,
                                )
                                : $publication->author ?? 'Association';

                            $publishedAt = $publication->published_at ?? $publication->created_at;

                            $shortDescription =
                                $publication->meta_description ?:
                                \Illuminate\Support\Str::limit(
                                    strip_tags(html_entity_decode($publication->content)),
                                    120,
                                );

                            $primaryTag = $publication->tags->first()?->name ?? 'Publication';
                        @endphp

                        <div class="col-12 col-md-6 col-lg-4">
                            <article class="publication-card">
                                <a href="{{ route('guestViewNewsAndArticle', ['uuid' => $publication->uuid]) }}"
                                    class="publication-image">
                                    <img src="{{ $coverUrl }}" alt="{{ $publication->title }}" loading="lazy">

                                    <span class="publication-badge">
                                        {{ $primaryTag }}
                                    </span>
                                </a>

                                <div class="publication-body">
                                    <div class="publication-meta">
                                        <span>
                                            <i class="fa-solid fa-calendar-days"></i>
                                            {{ $publishedAt?->format('d M, Y') ?? 'Date unavailable' }}
                                        </span>

                                        <span>
                                            <i class="fa-solid fa-eye"></i>
                                            {{ number_format($publication->views ?? 0) }}
                                        </span>
                                    </div>

                                    <h3>
                                        {{ \Illuminate\Support\Str::limit($publication->title, 78) }}
                                    </h3>

                                    <p>
                                        {{ $shortDescription }}
                                    </p>
                                </div>

                                <div class="publication-footer">
                                    <span class="publication-author">
                                        <i class="fa-solid fa-user-pen"></i>
                                        {{ $authorName }}
                                    </span>

                                    <a href="{{ route('guestViewNewsAndArticle', ['uuid' => $publication->uuid]) }}"
                                        class="publication-read">
                                        Read
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="publications-actions">
                    <a href="{{ route('newsAndArticles') }}" class="mx-button mx-button-outline">
                        View All Publications
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            </div>
        </section>
    @endif


    {{-- <section class="cta-section">
        <div class="container text-center">
            <h2>Support Our Projects</h2>

            <p>
                Support various projects from NAPES, NAEES, NACOMES, NAMES, and NICE-SA by making donations or
                paying invoices.
            </p>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="#" class="btn-primary-custom">Donation</a>
                <a href="#" class="btn-outline-custom">Invoice</a>
            </div>
        </div>
    </section> --}}



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.getElementById('leadershipSliderTrack');
            const dotsContainer = document.getElementById('leadershipSliderDots');

            if (!track) return;

            const slides = Array.from(track.children);
            const prevButton = document.querySelector('.slider-prev');
            const nextButton = document.querySelector('.slider-next');

            let currentIndex = 0;
            let autoplayTimer;
            const autoplayDelay = 4500;

            function slidesPerView() {
                if (window.innerWidth <= 575) return 1;
                if (window.innerWidth <= 991) return 2;
                return 3;
            }

            function maxIndex() {
                return Math.max(0, slides.length - slidesPerView());
            }

            function createDots() {
                dotsContainer.innerHTML = '';

                for (let i = 0; i <= maxIndex(); i++) {
                    const dot = document.createElement('button');
                    dot.type = 'button';
                    dot.className = 'slider-dot';
                    dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                    dot.addEventListener('click', () => {
                        goToSlide(i);
                        restartAutoplay();
                    });

                    dotsContainer.appendChild(dot);
                }
            }

            function updateSlider() {
                const slide = slides[0];
                const gap = 22;
                const slideWidth = slide.getBoundingClientRect().width + gap;

                track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;

                dotsContainer.querySelectorAll('.slider-dot').forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }

            function goToSlide(index) {
                currentIndex = Math.min(Math.max(index, 0), maxIndex());
                updateSlider();
            }

            function nextSlide() {
                currentIndex = currentIndex >= maxIndex() ? 0 : currentIndex + 1;
                updateSlider();
            }

            function previousSlide() {
                currentIndex = currentIndex <= 0 ? maxIndex() : currentIndex - 1;
                updateSlider();
            }

            function startAutoplay() {
                autoplayTimer = setInterval(nextSlide, autoplayDelay);
            }

            function stopAutoplay() {
                clearInterval(autoplayTimer);
            }

            function restartAutoplay() {
                stopAutoplay();
                startAutoplay();
            }

            nextButton.addEventListener('click', () => {
                nextSlide();
                restartAutoplay();
            });

            prevButton.addEventListener('click', () => {
                previousSlide();
                restartAutoplay();
            });

            window.addEventListener('resize', () => {
                createDots();
                goToSlide(0);
            });

            createDots();
            updateSlider();
            startAutoplay();
        });
    </script>

@endsection
