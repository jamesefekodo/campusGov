<div class="outerFrame">
    <img src="{{ associationLogo() }}" alt="{{ env('APP_NAME') }}" class="" width="100px" style="margin: 1rem">

    <div class="mailContent">
        @yield('content')
        <br>


        <p style="margin: 0">
            Best regards,
        </p>
        <p style="margin: 0">
            {{ config('app.name') }}.
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
        padding: 2rem;
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
</style>
