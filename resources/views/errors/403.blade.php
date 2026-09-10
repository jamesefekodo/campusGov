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

@endsection
