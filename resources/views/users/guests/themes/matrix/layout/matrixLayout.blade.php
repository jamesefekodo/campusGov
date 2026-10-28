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
    </style>
    <link rel="stylesheet" href="{{ asset('custom/frontendTheme/matrix/css/matrix.css') }}">
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
                        <a class="nav-link {{ Str::startsWith(request()->path(), 'home') ? 'active' : '' }}"
                            href="{{ route('homepage') }}">Home</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Str::startsWith(request()->path(), 'about') ? 'active' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            About Us
                        </a>
                        @php
                            $departmentItems = departments();
                        @endphp
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ Str::startsWith(request()->path(), 'about_us') ? 'active' : '' }}"
                                    href="{{ route('guestAboutUs') }}">{{ strtoupper(basicSystemInfo()->association_acronym) }}
                                    - {{ strtoupper(basicSystemInfo()->school_acronym) }}</a></li>

                            @if (basicSystemInfo()->operationalLevel == 'institute' && activeFaculties()->isNotEmpty())
                                @foreach (activeFaculties() as $faculty)
                                    <li><a class="dropdown-item"
                                            href="{{ route('guestViewFaculty', ['uuid' => $leader->uuid]) }}">{{ Str::limit($faculty->name, 25) }}
                                            @if ($faculty->acronym)
                                                ({{ $faculty->acronym }})
                                            @endif
                                        </a></li>
                                @endforeach
                            @elseif (basicSystemInfo()->operationalLevel == 'faculty' && activeFaculties()->isNotEmpty())
                                @foreach (activeFaculties() as $faculty)
                                    <li><a class="dropdown-item" href="#">{{ Str::limit($faculty->name, 25) }}
                                            @if ($faculty->acronym)
                                                ({{ $faculty->acronym }})
                                            @endif
                                        </a></li>
                                @endforeach
                                @foreach (activeDepartments() as $department)
                                    <li><a class="dropdown-item" href="#">{{ Str::limit($department->name, 25) }}
                                        </a></li>
                                @endforeach
                                {{-- department --}}
                            @elseif (activeDepartments()->isNotEmpty())
                                @foreach (activeDepartments() as $department)
                                    <li><a class="dropdown-item {{ Str::startsWith(request()->path(), 'about/department/'. $department->uuid) ? 'active' : '' }}"
                                            href="{{ route('guestViewDepartment', ['uuid' => $department->uuid]) }}">{{ Str::limit($department->name, 25) }}
                                        </a></li>
                                @endforeach
                            @endif
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
    <div class="mb-5">
        @yield('content')

        @include('layouts.include.supportModal')

    </div>


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
                <a href="https://odusystems.com.ng/" target="_blank">©
                    {{ strtoupper(basicSystemInfo()->association_acronym) }}
                    {{ strtoupper(basicSystemInfo()->school_acronym) }}
                    (2025{{ date('Y') > 2025 ? ' – ' . date('Y') : '' }}). All Rights Reserved. | CampusGov (v0.2)</a>
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
