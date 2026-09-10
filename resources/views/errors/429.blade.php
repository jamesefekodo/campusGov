{{-- @extends('layouts.authLayout')
@section('title', 'Error 429 | Too Many Requests')
@section('content')
    <div class="outer-container">
        @include('utilities.pages.auth.include.slider')
        <div class="login-form">
            <div class="form-container">
                @include('utilities.pages.auth.include.headerLogo')


                <h2 class="m-2 text-center auth_header">Error 429 | Too Many Requests</h2>
                <p class="m-2 text-center"> You have made too many requests in a short period of time.
                    For security and stability reasons, we limit how often certain
                    actions can be performed. Please wait a moment before trying again. </p>

                <hr>
                <div class="d-flex gap-2 mt-4">

                    <a href="{{ url()->previous() }}" class="btn btn-primary flex-fill py-2">
                        <i class="fas fa-arrow-left me-1"></i>
                        Go Back
                    </a>

                    <a href="{{ url('/') }}" class="btn btn-outline-primary flex-fill py-2">
                        <i class="fas fa-home me-1"></i>
                        Go Home
                    </a>

                </div>
            </div>
        </div>
    </div>

@endsection --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 — Too Many Requests</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: system-ui, sans-serif;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
            padding: 3rem 2.5rem;
            max-width: 480px;
            width: 100%;
            text-align: center
        }

        .code {
            font-size: 72px;
            font-weight: 700;
            color: #ffc107;
            line-height: 1;
            margin-bottom: .5rem
        }

        h1 {
            font-size: 22px;
            font-weight: 600;
            color: #1a1a18;
            margin-bottom: .75rem
        }

        p {
            font-size: 15px;
            color: #6c757d;
            line-height: 1.7;
            margin-bottom: 2rem
        }

        .btn {
            display: inline-block;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            margin: 0 4px
        }

        .btn-primary {
            background: #0d6efd;
            color: #fff
        }

        .btn-outline {
            background: transparent;
            color: #6c757d;
            border: 1px solid #dee2e6
        }

        .btn:hover {
            opacity: .85
        }

        .countdown {
            font-size: 13px;
            color: #adb5bd;
            margin-top: 1.5rem
        }
    </style>
</head>

<body>
    <div class="card">
        <div style="font-size:64px;margin-bottom:1rem;">🚦</div>
        <div class="code">429</div>
        <h1>Too Many Requests</h1>
        <p>
            You have made too many requests in a short period of time.
            For security and stability reasons, we limit how often certain
            actions can be performed. Please wait a moment before trying again.
        </p>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-outline">← Go Back</a>
            <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
        </div>
        <p class="countdown">Please wait a minute before retrying.</p>
    </div>
</body>

</html>
