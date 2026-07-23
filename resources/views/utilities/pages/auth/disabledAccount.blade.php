@extends('layouts.authLayout')
@section('title', 'Account Disabled')
@section('content')
    <div class="outer-container">
        @include('utilities.pages.auth.include.slider')
        <div class="login-form">
            <div class="form-container">
                @include('utilities.pages.auth.include.headerLogo')


                <h2 class="m-2 text-center auth_header">Account Disabled</h2>
                <p class="m-2 text-center">Your account has been temporarily disabled. If you believe this is a mistake or
                    need assistance restoring access, please contact the system administrator or support team.</p>

                <hr>
                <div class="d-flex gap-2 mt-4">

                    <a href="{{ url()->current() }}" class="btn btn-primary flex-fill py-2">
                        <i class="fas fa-rotate-right me-2"></i>
                        Refresh
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
