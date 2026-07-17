@extends('layouts.authLayout')
@section('title', 'Maintenance Mode')
@section('content')
    <div class="outer-container">
        @include('utilities.pages.auth.include.slider')
        <div class="login-form">
            <div class="form-container">
                @include('utilities.pages.auth.include.headerLogo')


                <h2 class="m-2 text-center auth_header">System Maintenance in Progress</h2>
                <p class="m-2 text-center">We're making important improvements behind the scenes to provide you with a better
                    experience. The system will be available again as soon as maintenance is complete.</p>
                <p class="m-2 text-center">Thank you for your understanding.</p>

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
