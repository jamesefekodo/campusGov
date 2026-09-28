@extends('layouts.authLayout')
@section('title', 'Error 403 | Access Denied')
@section('content')
    <div class="outer-container">
        @include('utilities.pages.auth.include.slider')
        <div class="login-form">
            <div class="form-container">
                @include('utilities.pages.auth.include.headerLogo')


                <h2 class="m-2 text-center auth_header">Error 403 | Access Denied</h2>
                <p class="m-2 text-center">You do not have permission to access this page.
                    If you believe this is a mistake, please contact your administrator.</p>

                <hr>
                <div class="row g-2 mt-4">

                    <div class="col-md-6">
                        <a href="{{ url()->previous() }}" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ url('/') }}" class="btn btn-outline-primary w-100 py-2">
                            <i class="fas fa-home me-1"></i>
                            Go Home
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('logoutRequest') }}" class="btn btn-danger w-100 py-2">
                            <i class="fas fa-right-from-bracket me-1"></i>
                            Logout
                        </a>
                    </div>

                    <div class="col-md-6">
                        @include('layouts.include.contextSwitcher')
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
