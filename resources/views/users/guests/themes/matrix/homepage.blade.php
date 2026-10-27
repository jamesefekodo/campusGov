<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $systemInfo = basicSystemInfo();

        $associationName = $systemInfo->association_name ?? ($systemInfo->association_acronym ?? config('app.name'));

        $associationAcronym = $systemInfo->association_acronym ?? config('app.name');

        $schoolAcronym = $systemInfo->school_acronym ?? null;
    @endphp


    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Toastr CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <title>
        @yield('title') | {{ basicSystemInfo()->association_acronym ?? config('app.name') }}
    </title>
    <meta name="description" content="Welcome to {{ $associationName }}. Login or register to access the platform.">
    <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}">



    @livewireStyles

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    {{-- OG data --}}
    <meta property="og:url"
        content="{{ tenant()->domain ? 'https://' . tenant()->domain : config('app.url') ?? url()->current() }}">
    <meta property="og:type" content="website">

    {{-- OG Title --}}
    <meta property="og:title" content="@yield('og_title', seoSettings()->meta_title ?? ($associationName ?? ($associationAcronym ?? config('app.name'))))">
    <meta property="og:description" content="@yield('og_description', seoSettings()->meta_description ?? 'CampusGov - Management System for Student Unions/Associations')">
    <meta property="og:image" content="@yield('og_image', associationLogo())">

    {{-- SEO --}}
    <meta name="description" content="@yield('meta_description', seoSettings()->meta_description ?? 'CampusGov - Management System for Student Unions/Associations')">
    <meta name="keywords" content="@yield('meta_keywords', seoSettings()->meta_keywords ?? '')">

    {{-- Header Scripts --}}
    {!! seoSettings()->header_scripts ?? '' !!}

    <style>
        :root {
            --theme-primary: {{ $themeColors['primary'] ?? '#1696e7' }};
            --theme-secondary: {{ $themeColors['secondary'] ?? '#22d3ee' }};
            --theme-accent: {{ $themeColors['accent'] ?? '#dc2626' }};
            --theme-background: {{ $themeColors['background'] ?? '#0f172a' }};
            --theme-surface: {{ $themeColors['surface'] ?? 'rgba(30, 41, 59, 0.5)' }};
            --theme-text: {{ $themeColors['text'] ?? '#ffffff' }};
            --theme-muted: {{ $themeColors['muted_text'] ?? '#9ca3af' }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--theme-background);
            color: var(--theme-text);
            overflow-x: hidden;
        }

        a,
        a:hover,
        a:focus,
        a:active {
            text-decoration: none !important;
        }

        .navbar {
            background-color: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 2rem;
        }

        .navbar-brand img {
            margin-right: 10px;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: var(--theme-secondary) !important;
            transform: scale(1.2);
        }

        .navbar-nav .nav-link.active {
            color: var(--theme-primary) !important;
            transform: scale(1.2);
        }

        .navbar-nav .nav-link.active::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background: var(--theme-primary);
        }

        .dropdown-menu {
            background: rgba(15, 23, 42, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            padding: 0.5rem;
            margin-top: 0.5rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .dropdown-item {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background: color-mix(in srgb, var(--theme-primary) 12%, transparent);
            color: var(--theme-primary);
        }

        .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.1);
            margin: 0.5rem 0;
        }

        @media (min-width: 992px) {
            .nav-item.dropdown:hover .dropdown-menu {
                display: block;
            }
        }

        .login-btn {
            background: transparent;
            border: 2px solid var(--theme-primary);
            color: var(--theme-primary);
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 0.5rem;
        }

        .login-btn:hover {
            background: var(--theme-primary);
            color: var(--theme-background);
        }

        .hero-carousel {
            height: 100vh;
            margin-top: 76px;
        }

        .carousel-item {
            height: 100vh;
        }

        .carousel-item::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to left,
                    color-mix(in srgb, var(--theme-background) 90%, transparent),
                    color-mix(in srgb, var(--theme-background) 60%, transparent),
                    transparent);
            z-index: 1;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            margin-top: 30px;
        }

        .carousel-caption {
            position: absolute;
            left: auto !important;
            right: 8% !important;
            bottom: 15%;
            transform: none !important;
            text-align: left;
            max-width: 550px;
            z-index: 2;
        }



        .glass-card {
            background: color-mix(in srgb, var(--theme-background) 70%, transparent);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 2rem;
            padding: 3rem;
            color: var(--theme-text);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px color-mix(in srgb, var(--theme-primary) 60%, transparent);
        }

        .glass-card h1 {
            font-size: 3rem;
            font-weight: 300;
        }

        .glass-card h3 {
            font-size: 1.5rem;
            font-weight: 300;
            color: #e5e7eb;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 60px;
            height: 60px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            opacity: 1;
            z-index: 10;
        }

        .carousel-indicators button {
            width: 40px;
            height: 4px;
            border-radius: 2px;
            background-color: rgba(255, 255, 255, 0.4);
        }

        .carousel-indicators button.active {
            background-color: var(--theme-primary);
        }

        .stats-section {
            background: var(--theme-surface);
            padding: 5rem 0;
        }

        .stat-card {
            background: var(--theme-surface);
            border: 1px solid rgba(71, 85, 105, 0.5);
            border-radius: 1.5rem;
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: color-mix(in srgb, var(--theme-secondary) 50%, transparent);
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 3rem;
            color: var(--theme-primary);
            margin-bottom: 1.5rem;
        }

        .stat-card h3 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--theme-primary);
        }

        .section-title {
            font-size: 3rem;
            font-weight: 300;
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title span {
            color: var(--theme-primary);
        }

        .services-section {
            padding: 5rem 0;
            background: var(--theme-background);
        }

        .service-card {
            background: var(--theme-surface);
            border: 1px solid rgba(71, 85, 105, 0.5);
            border-radius: 1.5rem;
            padding: 2.5rem;
            height: 100%;
            transition: all 0.3s ease;
        }

        .service-card:hover {
            border-color: color-mix(in srgb, var(--theme-primary) 50%, transparent);
            transform: translateY(-5px);
        }

        .service-card i {
            font-size: 3rem;
            color: var(--theme-primary);
            margin-bottom: 1.5rem;
        }

        .service-card h3 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .service-card p {
            color: var(--theme-muted);
            line-height: 1.8;
        }

        .service-card a {
            color: var(--theme-primary);
        }

        .association-card {
            background: color-mix(in srgb, var(--theme-background) 70%, transparent);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .association-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px color-mix(in srgb, var(--theme-primary) 40%, transparent);
        }

        .association-logo {
            max-height: 160px;
            object-fit: contain;
            border-radius: 8px;
        }

        .btn-red {
            background-color: var(--theme-accent);
            border-color: var(--theme-accent);
            color: white;
        }

        .btn-red:hover {
            background-color: color-mix(in srgb, var(--theme-accent) 80%, black);
            border-color: color-mix(in srgb, var(--theme-accent) 80%, black);
            color: white;
        }

        .president-img {
            max-height: 500px;
            object-fit: cover;
            width: 100%;
            transition: transform 0.4s ease;
        }

        .president-img:hover {
            transform: scale(1.05);
        }

        .overlay-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
        }

        .team-carousel {
            display: flex;
            gap: 24px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            padding: 20px 0;
            scrollbar-width: none;
        }

        .team-carousel::-webkit-scrollbar {
            display: none;
        }

        .team-profile-card {
            flex: 0 0 280px;
            height: 380px;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            scroll-snap-align: start;
            background-size: cover;
            background-position: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .team-profile-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .team-card-overlay {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .news-card {
            height: 100%;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 18px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: all 0.35s ease;
        }

        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px color-mix(in srgb, var(--theme-primary) 35%, transparent);
        }

        .news-image {
            height: 220px;
            overflow: hidden;
        }

        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .news-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            background: var(--theme-accent);
            color: #fff;
            font-size: 11px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .btn-read {
            background: var(--theme-accent);
            color: #fff;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
        }

        .btn-read:hover {
            background: color-mix(in srgb, var(--theme-accent) 80%, black);
            color: #fff;
        }

        .cta-section {
            background: linear-gradient(to right,
                    color-mix(in srgb, var(--theme-secondary) 25%, transparent),
                    var(--theme-background));
            padding: 5rem 0;
        }

        .btn-primary-custom {
            background: var(--theme-primary);
            color: var(--theme-background);
            padding: 1rem 2.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .btn-primary-custom:hover {
            background: var(--theme-secondary);
        }

        .btn-outline-custom {
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: var(--theme-text);
            padding: 1rem 2.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .btn-outline-custom:hover {
            border-color: var(--theme-primary);
        }

        footer {
            background: #020617;
            padding: 3rem 0 1.5rem;
        }

        footer ul {
            list-style: none;
            padding: 0;
        }

        footer ul li a {
            color: var(--theme-muted);
        }

        footer ul li a:hover {
            color: var(--theme-primary);
        }

        @media (max-width: 768px) {
            .glass-card {
                padding: 2rem;
            }

            .glass-card h1 {
                font-size: 2rem;
            }

            .carousel-caption {
                right: 3%;
                bottom: 20%;
                max-width: 85%;
            }

            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ route('homepage') }}">
                <img src="{{ associationLogo() }}" alt="{{ $associationAcronym }} Logo" width="80px">
                @if (schoolLogo())
                    <img src="{{ schoolLogo() }}" alt="OGITECH" width="65px">
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('homepage') }}">Home</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            About
                        </a>

                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">NAPES</a></li>
                            <li><a class="dropdown-item" href="#">NAEES</a></li>
                            <li><a class="dropdown-item" href="#">NACOMES</a></li>
                            <li><a class="dropdown-item" href="#">NAMES</a></li>
                            <li><a class="dropdown-item" href="#">Faculty of Engineering</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Media
                        </a>

                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Publications</a></li>
                            <li><a class="dropdown-item" href="#">News</a></li>
                            <li><a class="dropdown-item" href="#">Articles</a></li>
                            <li><a class="dropdown-item" href="#">Gallery</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Events
                        </a>

                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Regular Events</a></li>
                            <li><a class="dropdown-item" href="#">Elections</a></li>
                            <li><a class="dropdown-item" href="#">Upcoming Events</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Academic Resources
                        </a>

                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Past Questions</a></li>
                            <li><a class="dropdown-item" href="#">Recommended Courses</a></li>
                            <li><a class="dropdown-item" href="#">Recommended Books</a></li>
                            <li><a class="dropdown-item" href="#">Engineering & Tech Bodies</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Payments
                        </a>

                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Invoice</a></li>
                            <li><a class="dropdown-item" href="#">Donation</a></li>
                        </ul>
                    </li>
                </ul>

                <a href="{{ route('loginPage') }}" class="login-btn">Login</a>
            </div>
        </div>
    </nav>

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

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
                data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    @endif

    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                {{-- department --}}
        {{-- faculty --}}
        {{-- institute --}}
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

                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fa fa-building-columns"></i>
                        <h3 class="stat-number" data-target="12" data-suffix="">0</h3>
                        <p>Faculties</p>
                    </div>
                </div>

                 <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fa fa-sitemap"></i>
                        <h3 class="stat-number" data-target="12" data-suffix="">0</h3>
                        <p>Departments</p>
                    </div>
                </div>
                 <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fa fa-book-open"></i>
                        <h3 class="stat-number" data-target="12" data-suffix="">0</h3>
                        <p>Academic Resources</p>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <div class="row align-items-center g-4">
            <div class="col-md-6">
                <div class="position-relative rounded overflow-hidden shadow-sm">
                    <img src="https://placehold.co/800x600/0f172a/1696e7?text=President+Photo"
                        class="img-fluid w-100 president-img" alt="President">

                    <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white overlay-glass text-center">
                        <h4 class="mb-1">John Doe</h4>
                        <p class="mb-0">President, NAPES, 2025/2026 Session.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-4 rounded shadow-sm overlay-glass">
                    <h3 style="color: var(--theme-primary)">President's Address</h3>

                    <p>
                        This is placeholder text for the president’s remarks. Replace this paragraph with the
                        actual welcome message, vision, and priorities for the association.
                    </p>

                    <div class="d-flex justify-content-end">
                        <a href="#" class="btn btn-primary">More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="services-section">
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
    </section>

    <section class="services-section">
        <div class="container">
            <h2 class="section-title">Our <span>Faculty/Departments</span></h2>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="service-card">
                        <i class="fas fa-cogs w-100 text-center"></i>
                        <h3 class="w-100 text-center">Faculty of Engineering (FENG)</h3>
                        <p>Placeholder description for the faculty of engineering.</p>
                        <a href="#" class="w-100 text-end d-block">Learn More</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="service-card">
                        <i class="fas fa-cogs w-100 text-center"></i>
                        <h3 class="w-100 text-center">Mechanical Engineering</h3>
                        <p>Placeholder description for the mechanical engineering department.</p>
                        <a href="#" class="w-100 text-end d-block">Learn More</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="service-card">
                        <i class="fas fa-lightbulb w-100 text-center"></i>
                        <h3 class="w-100 text-center">Electrical Engineering</h3>
                        <p>Placeholder description for the electrical engineering department.</p>
                        <a href="#" class="w-100 text-end d-block">Learn More</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="service-card">
                        <i class="fas fa-microchip w-100 text-center"></i>
                        <h3 class="w-100 text-center">Computer Engineering</h3>
                        <p>Placeholder description for the computer engineering department.</p>
                        <a href="#" class="w-100 text-end d-block">Learn More</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="service-card">
                        <i class="fas fa-drafting-compass w-100 text-center"></i>
                        <h3 class="w-100 text-center">Civil Engineering</h3>
                        <p>Placeholder description for the civil engineering department.</p>
                        <a href="#" class="w-100 text-end d-block">Learn More</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="service-card">
                        <i class="fas fa-flask w-100 text-center"></i>
                        <h3 class="w-100 text-center">Chemical Engineering</h3>
                        <p>Placeholder description for the chemical engineering department.</p>
                        <a href="#" class="w-100 text-end d-block">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                            <img src="https://placehold.co/800x500/0f172a/1696e7?text=Publication+1"
                                alt="Publication">
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
                            <img src="https://placehold.co/800x500/111827/22d3ee?text=Publication+2"
                                alt="Publication">
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
                            <img src="https://placehold.co/800x500/020617/63b4e6?text=Publication+3"
                                alt="Publication">
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

    <footer>
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <h5>NAPES-OGITECH</h5>
                    <p>Engineering in practice!</p>
                </div>

                <div class="col-md-3">
                    <h5>About</h5>
                    <ul>
                        <li><a href="#">Faculty/Departments & Leaders</a></li>
                        <li><a href="#">Associations & Leaders</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h5>Resources</h5>
                    <ul>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Academic Resources</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Download App</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h5>Legal</h5>
                    <ul>
                        <li><a href="#">Privacy</a></li>
                        <li><a href="#">Terms</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="text-center">
                <a href="#">Transcend Technologies</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function animateCounter(element, target, duration, suffix = '') {
            let current = 0;
            const increment = target / (duration / 16);

            const timer = setInterval(() => {
                current += increment;

                if (current >= target) {
                    element.textContent = target + suffix;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current) + suffix;
                }
            }, 16);
        }

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');

                    entry.target.querySelectorAll('.stat-number').forEach(counter => {
                        const target = parseInt(counter.getAttribute('data-target'));
                        const suffix = counter.getAttribute('data-suffix') || '';

                        animateCounter(counter, target, 2000, suffix);
                    });
                }
            });
        }, {
            threshold: 0.5
        });

        window.addEventListener('DOMContentLoaded', () => {
            const statsSection = document.querySelector('.stats-section');

            if (statsSection) {
                observer.observe(statsSection);
            }
        });
    </script>

    @include('layouts.include.toastrNotificationFunction')



    <script>
        document.querySelectorAll('form').forEach(form => {

            form.addEventListener('submit', function() {

                const btn = form.querySelector('.submit-btn');

                if (!btn) return;

                btn.disabled = true;

                btn.querySelector('.btn-text').style.display = 'none';
                btn.querySelector('.btn-loading').style.display = 'inline';
            });

        });
    </script>

    @livewireScripts

</body>

</html>
