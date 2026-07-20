@extends('layouts.authLayout')
@section('title', 'Manage Sessions')
@section('content')
    <div class="outer-container">
        @include('utilities.pages.auth.include.slider')
        <div class="login-form">
            <div class="form-container">
                @include('utilities.pages.auth.include.headerLogo')


                <h2 class="m-2 text-center auth_header">Manage Sessions</h2>
                <p class="m-2 text-center">You currently have more than the allowed number of active login sessions. Please
                    select one of the options below to proceed:</p>
                <p class="m-2 text-center">Thank you for your understanding.</p>

                <ul class="mx-3 p-0" style="text-align: start">
                    <li class="mx-3 p-0"><strong>End Other Sessions</strong> – This will automatically sign you out of all
                        other devices.
                    </li>
                    <li class="mx-3 p-0"><strong>Logout</strong> – End this session.</li>
                </ul>

                <hr>
                <div class="d-flex gap-2 mt-4">

                    <a href="{{ route('endOtherSessions') }}" class="btn btn-primary flex-fill py-2">
                        <i class="fas fa-ban me-2"></i>
                        End Other Sessions
                    </a>

                    <a href="{{ route('logoutRequest') }}" class="btn btn-outline-danger flex-fill py-2">
                        <i class="fas fa-right-from-bracket me-2"></i>
                        Logout
                    </a>

                </div>
            </div>
        </div>
    </div>

@endsection
