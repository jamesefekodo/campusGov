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

    @include('users.guests.themes.matrix.layout.include.matrixNavBar')

    
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
