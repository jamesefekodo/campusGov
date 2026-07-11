<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ associationLogo() }}">

    <style>
        :root {
            --system-primary: {{ basicSystemInfo()['primary_colour'] ?: '#3b5edd' }};
            --system-secondary: {{ basicSystemInfo()['secondary_colour'] ?: '#7a97ff' }};
            --system-tertiary: {{ basicSystemInfo()['tertiary_colour'] ?: 'var(--system-secondary)' }};
        }
    </style>

    <link rel="stylesheet" href="{{ asset('auth/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    {{-- font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Sweet Alert --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css">

    <!-- Toastr CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>



    <title>
        @yield('title') | {{ config('app.name') }}
    </title>

    @yield('style')

</head>

<body>
    {{-- <div class="container"> --}}

    @yield('content')
    {{-- </div> --}}



    <script src="{{ asset('auth/script.js') }}"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <!-- Popper.js (required for some Bootstrap components like tooltips and popovers) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"
        integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    {{-- Sweet Alert --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script> --}}
    {{-- @include('layouts.include.swalNotificationFunctions') --}}

    {{-- toastr notifications --}}
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

</html>
