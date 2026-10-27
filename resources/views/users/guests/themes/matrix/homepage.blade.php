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

                        <p class="text-justify">
                            {!! \Illuminate\Support\Str::words(
                                strip_tags(html_entity_decode($data['basicInfo']->president_remarks)),
                                150,
                                '...',
                            ) !!}
                        </p>

                        <div class="d-flex justify-content-end">
                            <a href="#" class="btn btn-primary">More</a>
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
    {{-- faculties start --}}
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

                            <a {{-- href="{{ $faculty->slug ? route('guestViewFaculty', ['slug' => $faculty->slug]) : '#' }}" --}} class="w-100 text-end d-block">
                                Learn More
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    {{-- faculties end --}}

    {{-- departments start --}}
    <section class="services-section">
        <div class="container">
            <h2 class="section-title">
                Our <span>Faculties/Departments</span>
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
                    ];
                @endphp

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

                            <a {{-- href="{{ $department->slug
                            ? route('guestViewDepartment', ['slug' => $department->slug])
                            : '#' }}" --}} class="w-100 text-end d-block">
                                Learn More
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    {{-- departments end --}}

    <section class="services-section">
        <div class="container">
            <h2 class="section-title">Meet Our <span>Leaders</span></h2>

            <div class="team-carousel" id="teamCarousel">
                <a href="#" class="team-profile-card"
                    style="background-image: url('https://placehold.co/600x800/0f172a/1696e7?text=Leader+1');">
                    <div class="team-card-overlay">
                        <h3>Leader One</h3>
                        <p>President, NAPES</p>
                    </div>
                </a>

                <a href="#" class="team-profile-card"
                    style="background-image: url('https://placehold.co/600x800/111827/22d3ee?text=Leader+2');">
                    <div class="team-card-overlay">
                        <h3>Leader Two</h3>
                        <p>President, NAEES</p>
                    </div>
                </a>

                <a href="#" class="team-profile-card"
                    style="background-image: url('https://placehold.co/600x800/020617/63b4e6?text=Leader+3');">
                    <div class="team-card-overlay">
                        <h3>Leader Three</h3>
                        <p>President, NACOMES</p>
                    </div>
                </a>

                <a href="#" class="team-profile-card"
                    style="background-image: url('https://placehold.co/600x800/0f172a/ffffff?text=Leader+4');">
                    <div class="team-card-overlay">
                        <h3>Leader Four</h3>
                        <p>President, NAMES</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section class="services-section">
        <div class="container">
            <h2 class="section-title">Latest <span>Publications</span></h2>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="news-card">
                        <div class="news-image position-relative">
                            <span class="news-badge">News</span>
                            <img src="https://placehold.co/800x500/0f172a/1696e7?text=Publication+1" alt="Publication">
                        </div>

                        <div class="p-3 flex-grow-1">
                            <small class="text-white-50">Monday, 26 October, 2026</small>
                            <h5 class="mt-2">Placeholder publication title one</h5>
                            <p class="text-white-50">This is placeholder publication content for the homepage.</p>
                        </div>

                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <a href="#" class="btn-read">Read more</a>
                            <div>
                                <span>120 <i class="fas fa-eye"></i></span>
                                <span class="ms-2">24 <i class="fas fa-share"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="news-card">
                        <div class="news-image position-relative">
                            <span class="news-badge">Article</span>
                            <img src="https://placehold.co/800x500/111827/22d3ee?text=Publication+2" alt="Publication">
                        </div>

                        <div class="p-3 flex-grow-1">
                            <small class="text-white-50">Monday, 26 October, 2026</small>
                            <h5 class="mt-2">Placeholder publication title two</h5>
                            <p class="text-white-50">This is placeholder publication content for the homepage.</p>
                        </div>

                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <a href="#" class="btn-read">Read more</a>
                            <div>
                                <span>85 <i class="fas fa-eye"></i></span>
                                <span class="ms-2">12 <i class="fas fa-share"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="news-card">
                        <div class="news-image position-relative">
                            <span class="news-badge">News</span>
                            <img src="https://placehold.co/800x500/020617/63b4e6?text=Publication+3" alt="Publication">
                        </div>

                        <div class="p-3 flex-grow-1">
                            <small class="text-white-50">Monday, 26 October, 2026</small>
                            <h5 class="mt-2">Placeholder publication title three</h5>
                            <p class="text-white-50">This is placeholder publication content for the homepage.</p>
                        </div>

                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <a href="#" class="btn-read">Read more</a>
                            <div>
                                <span>64 <i class="fas fa-eye"></i></span>
                                <span class="ms-2">8 <i class="fas fa-share"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
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
    </section>


@endsection
