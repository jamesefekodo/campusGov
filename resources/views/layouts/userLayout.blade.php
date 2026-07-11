<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --system-primary: {{ basicSystemInfo()['primary_colour'] ?: '#3b5edd' }};
            --system-secondary: {{ basicSystemInfo()['secondary_colour'] ?: '#7a97ff' }};
            --system-tertiary: {{ basicSystemInfo()['tertiary_colour'] ?: 'var(--system-secondary)' }};
        }
    </style>


    <link rel="stylesheet" href="{{ asset('custom/css/admindashboard.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('custom/css/custom.css') }}"> --}}

    {{-- Production --}}
    {{-- bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Sweet Alert --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css">
    {{-- Owl Carousel --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
        integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- Summer Note JS --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>


    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('portal-dashboard/assets/css/portal.css') }}">

    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Toastr CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    {{-- Cropper.js --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.41.0/apexcharts.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>


    <title>
        @yield('title') | {{ basicSystemInfo()->association_acronym }}
    </title>

    @yield('style')

    {{-- google analytics --}}
    {{-- @include('layouts.include.googleAnalytics') --}}
</head>



<body class="app">
    <header class="app-header fixed-top">

        @php
            $user = auth()->user();
        @endphp

        @include('layouts.include.userHeader')
        <div id="app-sidepanel" class="app-sidepanel">
            <div id="sidepanel-drop" class="sidepanel-drop"></div>
            <div class="sidepanel-inner d-flex flex-column">
                <a href="#" id="sidepanel-close" class="sidepanel-close d-xl-none">&times;</a>
                <div id="sidepanel-drop" class="sidepanel-drop"></div>
                <div class="sidepanel-inner d-flex flex-column">
                    <a href="#" id="sidepanel-close" class="sidepanel-close d-xl-none">&times;</a>
                    <div class="app-branding">

                        <a href="{{ url('/') }}" class="zone-header">
                            <div class="zone-header-item">
                                <img class="logo-icon logo-sidebar me-2" src="{{ associationLogo() }}" alt="logo">
                            </div>
                            <div class="zone-header-item">
                                <span class="logo-text">{{ basicSystemInfo()->association_acronym }}</span>
                            </div>
                        </a>

                        {{-- @switch(getUserType())
                            @case('root')
                            @case('admin')
                                <a href="{{ url('/') }}" class="zone-header">
                                    <div class="zone-header-item">
                                        <img class="logo-icon me-2" src="{{ asset('system/images/logo.webp') }}" alt="logo">
                                    </div>
                                    <div class="zone-header-item">
                                        <span class="logo-text">{{ config('app.name') }}</span>
                                    </div>
                                </a>
                            @break

                            @case('association')
                                @php
                                    $user = Auth::user();
                                    $associationLogo = asset('system/images/logo_placeholder.png');
                                    if (
                                        $user->passport &&
                                        file_exists(
                                            public_path('assets/uploads/users/associations/logo/' . $user->passport),
                                        )
                                    ) {
                                        $associationLogo = asset(
                                            'assets/uploads/users/associations/logo/' . $user->passport,
                                        );
                                    }
                                @endphp
                                <a href="{{ url('/') }}" class="zone-header">
                                    <div class="zone-header-item">
                                        <img class="logo-icon me-2" src="{{ $associationLogo }}" alt="logo">
                                    </div>
                                    <div class="zone-header-item">
                                        <span class="logo-text">{{ $user->associationProfile->acronym }}
                                            (OGITECH)</span>
                                    </div>
                                </a>
                            @break

                            @default
                                <a href="{{ url('/') }}" class="zone-header">
                                    <div class="zone-header-item">
                                        <img class="logo-icon me-2" src="{{ asset('system/images/logo.webp') }}"
                                            alt="logo">
                                    </div>
                                    <div class="zone-header-item">
                                        <span class="logo-text">{{ config('app.name') }}</span>
                                    </div>
                                </a>
                        @endswitch --}}
                        <hr>

                    </div><!--//app-branding-->

                    @if (getUserType() === 'companyStaff')
                        @include('layouts.include.contextSwitcher')
                    @endif

                    @if (in_array(getUserType(), ['root', 'admin']))
                        @include('layouts.include.adminSidebar')
                    @endif
                    @if (getUserType() === 'companyAdmin')
                        @include('layouts.include.companySidebar')
                    @endif




                </div><!--//sidepanel-inner-->
            </div><!--//app-sidepanel-->
    </header><!--//app-header-->

    <div class="app-wrapper px-3">
        @yield('content')


        {{-- @include('layouts.include.utilities.whatsappSupport') --}}

    </div><!--//app-wrapper-->
    <br><br>
    {{-- @include('layouts.include.footer') --}}


    <!-- Javascript -->
    <script src="{{ asset('portal-dashboard/assets/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('portal-dashboard/assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- Charts JS -->
    <script src="{{ asset('portal-dashboard/assets/plugins/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('portal-dashboard/assets/js/index-charts.js') }}"></script>
    <!-- Page Specific JS -->
    <script src="{{ asset('portal-dashboard/assets/js/app.js') }}"></script>


    {{-- popper --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    {{-- Summer Note JS --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    {{-- Sweet Alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script>

    @include('layouts.include.toastrNotificationFunction')
    {{-- @include('layouts.include.utilities.swalNotificationFunctions') --}}


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

</body>



</html>
