<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $systemInfo = basicSystemInfo();

        $associationName = $systemInfo->association_name ?? ($systemInfo->association_acronym ?? config('app.name'));

        $associationAcronym = $systemInfo->association_acronym ?? config('app.name');

        $primaryColour = $systemInfo->primary_colour ?: '#3b5edd';
        $secondaryColour = $systemInfo->secondary_colour ?: '#7a97ff';
        $tertiaryColour = $systemInfo->tertiary_colour ?: $secondaryColour;
    @endphp

    <title>{{ $associationAcronym }} | Welcome</title>

    <meta name="description" content="Welcome to {{ $associationName }}. Login or register to access the platform.">

    <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Optional: Existing project CSS --}}
    <link rel="stylesheet" href="{{ asset('custom/css/admindashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('portal-dashboard/assets/css/portal.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --system-primary: {{ $primaryColour }};
            --system-secondary: {{ $secondaryColour }};
            --system-tertiary: {{ $tertiaryColour }};

            --landing-white: #ffffff;
            --landing-text-light: rgba(255, 255, 255, 0.80);
            --landing-border: rgba(255, 255, 255, 0.23);
            --landing-dark-overlay: rgba(8, 15, 32, 0.68);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            background: var(--system-primary);
            color: var(--landing-white);
            font-family: "DM Sans", Arial, sans-serif;
        }

        .temporary-landing-page {
            position: relative;
            display: flex;
            min-height: 100vh;
            overflow: hidden;
            background:
                linear-gradient(125deg,
                    color-mix(in srgb, var(--system-primary) 88%, #000000),
                    color-mix(in srgb, var(--system-secondary) 73%, #000000));
        }

        /*
         * Optional background image:
         *
         * To use one, put a file in:
         * public/assets/images/landing-background.jpg
         *
         * Then change the .temporary-landing-page background above to:
         *
         * background:
         * linear-gradient(125deg,
         * rgba(0, 0, 0, 0.68),
         * color-mix(in srgb, var(--system-primary) 75%, #000000)),
         * url("{{ asset('assets/images/landing-background.jpg') }}") center/cover no-repeat;
         */

        .temporary-landing-page::before {
            position: absolute;
            top: -220px;
            right: -180px;
            width: 630px;
            height: 630px;
            border-radius: 50%;
            background: var(--system-tertiary);
            content: "";
            opacity: 0.19;
            filter: blur(4px);
        }

        .temporary-landing-page::after {
            position: absolute;
            bottom: -310px;
            left: -200px;
            width: 670px;
            height: 670px;
            border: 1px solid rgba(255, 255, 255, 0.20);
            border-radius: 50%;
            content: "";
        }

        .landing-container {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: min(1180px, 100%);
            min-height: 100vh;
            margin: 0 auto;
            padding: 38px 48px 28px;
        }

        /* Brand */

        .landing-brand {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            gap: 13px;
            color: var(--landing-white);
            text-decoration: none;
        }

        .landing-brand:hover {
            color: var(--landing-white);
        }

        .landing-logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            padding: 5px;
            border: 1px solid rgba(255, 255, 255, 0.48);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(10px);
        }

        .landing-logo {
            width: 100%;
            height: 100%;
            border-radius: 10px;
            object-fit: contain;
        }

        .landing-brand-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .landing-brand-acronym {
            max-width: 310px;
            overflow: hidden;
            font-size: 19px;
            font-weight: 800;
            letter-spacing: 0.2px;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .landing-brand-subtitle {
            color: rgba(255, 255, 255, 0.73);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Hero */

        .landing-hero-content {
            max-width: 780px;
            padding: 68px 0;
        }

        .landing-status {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 24px;
            padding: 9px 15px;
            border: 1px solid var(--landing-border);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.11);
            color: var(--landing-white);
            font-size: 13px;
            font-weight: 700;
            backdrop-filter: blur(10px);
        }

        .landing-status i {
            color: var(--system-tertiary);
        }

        .landing-heading {
            max-width: 750px;
            margin: 0;
            color: var(--landing-white);
            font-size: clamp(42px, 6.3vw, 76px);
            font-weight: 800;
            letter-spacing: -2.4px;
            line-height: 1.08;
        }

        .landing-heading span {
            color: var(--system-tertiary);
        }

        .landing-description {
            max-width: 640px;
            margin: 25px 0 0;
            color: var(--landing-text-light);
            font-size: clamp(16px, 2vw, 19px);
            font-weight: 400;
            line-height: 1.75;
        }

        .landing-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 35px;
        }

        .landing-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 165px;
            gap: 10px;
            padding: 15px 23px;
            border: 1px solid transparent;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 800;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
        }

        .landing-button:hover {
            transform: translateY(-3px);
            text-decoration: none;
        }

        .landing-button-login {
            background: var(--system-tertiary);
            color: #17201d;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.24);
        }

        .landing-button-login:hover {
            background: #ffffff;
            color: var(--system-primary);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.30);
        }

        .landing-button-register {
            border-color: rgba(255, 255, 255, 0.67);
            background: rgba(255, 255, 255, 0.05);
            color: var(--landing-white);
        }

        .landing-button-register:hover {
            border-color: var(--landing-white);
            background: var(--landing-white);
            color: var(--system-primary);
        }

        .landing-features {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 22px;
            margin-top: 42px;
        }

        .landing-feature {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.76);
            font-size: 13px;
            font-weight: 500;
        }

        .landing-feature i {
            color: var(--system-tertiary);
        }

        /* Footer */

        .landing-footer {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px 28px;
            padding-top: 22px;
            border-top: 1px solid var(--landing-border);
            color: rgba(255, 255, 255, 0.69);
            font-size: 13px;
        }

        .landing-footer p {
            margin: 0;
        }

        .landing-footer a {
            color: var(--landing-white);
            font-weight: 700;
            text-decoration: none;
        }

        .landing-footer a:hover {
            color: var(--system-tertiary);
        }

        @media (max-width: 768px) {
            .landing-container {
                padding: 28px 24px 24px;
            }

            .landing-hero-content {
                padding: 55px 0;
            }

            .landing-heading {
                letter-spacing: -1.5px;
            }

            .landing-actions {
                flex-direction: column;
            }

            .landing-button {
                width: 100%;
            }

            .landing-footer {
                flex-direction: column;
            }
        }

        @media (max-width: 430px) {
            .landing-container {
                padding-right: 18px;
                padding-left: 18px;
            }

            .landing-logo-wrapper {
                width: 48px;
                height: 48px;
            }

            .landing-brand-acronym {
                max-width: 210px;
                font-size: 16px;
            }

            .landing-brand-subtitle {
                font-size: 10px;
            }

            .landing-status {
                font-size: 12px;
            }

            .landing-features {
                flex-direction: column;
                gap: 9px;
            }
        }
    </style>

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

</head>

<body>
    <main class="temporary-landing-page">
        <div class="landing-container">

            {{-- System logo and association identity --}}
            <a href="{{ route('homepage') }}" class="landing-brand">
                <div class="landing-logo-wrapper">
                    <img src="{{ associationLogo() }}" alt="{{ $associationAcronym }} Logo" class="landing-logo">
                </div>

                <div class="landing-brand-text">
                    <span class="landing-brand-acronym">
                        {{ $associationAcronym }}
                    </span>

                    <span class="landing-brand-subtitle">
                        Official Platform
                    </span>
                </div>
            </a>

            {{-- Main welcome content --}}
            <section class="landing-hero-content">
                <div class="landing-status">
                    <i class="fas fa-sparkles"></i>
                    <span>Our improved website is coming soon</span>
                </div>

                <h1 class="landing-heading">
                    Welcome to<br>
                    <span>{{ $associationName }}</span>
                </h1>

                <p class="landing-description">
                    We are currently updating our system and improving the landing page.
                    Your data is 100% unaffected. All features and operations for members
                    and administrators remain fully active. Please log in or register to
                    proceed.
                </p>

                <div class="landing-actions">
                    <a href="{{ route('loginPage') }}" class="landing-button landing-button-login">
                        <i class="fas fa-right-to-bracket"></i>
                        Login
                    </a>

                    {{--
                        Change registerPage to your actual register route name if needed.
                        For example: route('register')
                    --}}
                    <a href="{{ route('registrationPage') }}" class="landing-button landing-button-register">
                        <i class="fas fa-user-plus"></i>
                        Register
                    </a>
                </div>

                <div class="landing-features">
                    <span class="landing-feature">
                        <i class="fas fa-user-plus"></i>
                        Simple registration
                    </span>

                    <span class="landing-feature">
                        <i class="fas fa-building"></i>
                        Multi-tenant management
                    </span>

                    <span class="landing-feature">
                        <i class="fas fa-users-gear"></i>
                        Membership management
                    </span>

                    <span class="landing-feature">
                        <i class="fas fa-newspaper"></i>
                        Media and publications
                    </span>

                    <span class="landing-feature">
                        <i class="fas fa-money-bill-wave"></i>
                        Dues and payment management
                    </span>

                    <span class="landing-feature">
                        <i class="fas fa-check-to-slot"></i>
                        Election management
                    </span>

                    <span class="landing-feature">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Financial claims
                    </span>

                    <span class="landing-feature">
                        <i class="fas fa-book-open"></i>
                        Past questions and resources
                    </span>

                    <span class="landing-feature">
                        <i class="fas fa-chart-line"></i>
                        Reports and analytics
                    </span>

                    <span class="landing-feature">
                        <i class="fas fa-ellipsis"></i>
                        And much more
                    </span>
                </div>
            </section>

            {{-- Footer --}}
            <footer class="landing-footer">
                <p>
                    © {{ date('Y') }} {{ $associationAcronym }}.
                    All rights reserved.
                </p>

                <p>
                    Need assistance?
                    <a href="mailto:{{ config('mail.from.address', 'support@example.com') }}">
                        Contact support
                    </a>
                </p>
            </footer>

        </div>
    </main>
</body>

</html>
