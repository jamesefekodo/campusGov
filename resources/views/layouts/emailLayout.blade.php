{{-- <div class="outerFrame">
    <img src="{{ associationLogo() }}" alt="{{ env('APP_NAME') }}" class="" width="100px" style="margin: 1rem">


    <div class="mailContent">
        @yield('content')
        <br>


        <p style="margin: 0">
            Best regards,
        </p>
        <p style="margin: 0">
            {{ basicSystemInfo()->association_acronym ?? config('app.name') }}.
        </p>
    </div>

</div>



<style>
    :root {
        --system-primary: {{ basicSystemInfo()['primary_colour'] ?: '#3b5edd' }};
        --system-secondary: {{ basicSystemInfo()['secondary_colour'] ?: '#7a97ff' }};
        --system-tertiary: {{ basicSystemInfo()['tertiary_colour'] ?: 'var(--system-secondary)' }};
    }

    .outerFrame {
        width: 100%;
        background: #f0f0f0;
        text-align: center;
        padding: 1rem;
    }

    .mailContent {
        width: 80%;
        background: #ffffff;
        text-align: left;
        margin: 0.2rem auto;
        padding: 1.7rem;
        font-family: Arial, sans-serif;
    }

    .mailLink {
        text-align: center;
    }

    .link-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: var(--system-primary);
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .link-button:hover {
        background-color: #77c3ec;
    }

    .otp {
        text-align: center;
        justify-content: center;
    }

    .link-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: var(--system-primary);
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }


    .otp-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: var(--system-primary);
        color: #fff;
        font-size: 1.5rem;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .otp-button:hover {
        background-color: var(--system-secondary);
    }
</style> --}}

@php
    $primaryColour = basicSystemInfo()['primary_colour'] ?: '#3b5edd';
    $secondaryColour = basicSystemInfo()['secondary_colour'] ?: '#7a97ff';
    $tertiaryColour = basicSystemInfo()['tertiary_colour'] ?: $secondaryColour;
@endphp

<div class="outerFrame">
    <div style="text-align:center;">
        <img src="{{ emailSafeLogo() }}" alt="{{ env('APP_NAME') }}" width="80"
            style="margin: 1rem auto; background-color:#ffffff; display:block;">
    </div>
    <div class="mailContent">
        @yield('content')
        <br>

        <p style="margin: 0">Best regards,</p>
        <p style="margin: 0">{{ basicSystemInfo()->association_acronym ?? config('app.name') }}.</p>
    </div>
</div>

<style>
    .outerFrame {
        width: 100%;
        background: #f0f0f0;
        text-align: center;
        padding: 1rem;
    }

    .mailContent {
        width: 80%;
        background: #ffffff;
        text-align: left;
        margin: 0.2rem auto;
        padding: 1.7rem;
        font-family: Arial, sans-serif;
    }

    .link-button,
    .otp-button {
        display: block;
        width: fit-content;
        margin: 1rem auto;
        padding: 10px 20px;
        background-color: {{ $primaryColour }};
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }

    .otp-button {
        font-size: 1.5rem;
    }

    .link-button:hover,
    .otp-button:hover {
        background-color: {{ $secondaryColour }};
    }
</style>
