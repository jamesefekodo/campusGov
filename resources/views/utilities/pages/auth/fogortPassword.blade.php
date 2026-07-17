@extends('layouts.authLayout')
@section('title', 'Forgot Password')
@section('content')
    <div class="outer-container">
        @include('utilities.pages.auth.include.slider')
        <div class="login-form">
            <div class="form-container">
                @include('utilities.pages.auth.include.headerLogo')


                <h2 class="m-2 text-center auth_header">Forgot Password</h2>
                {{-- <p class="m-2 text-center">Please sign in to your account</p> --}}
                <form class="" action="{{ route('requestPasswordResetLink') }}" method="POST">
                    @csrf
                    <input type="email" placeholder="Email" name="email" required />
                    <p style="color: red">{{ $errors->first('email') }}</p>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">
                            Send Password Reset Link
                        </span>

                        <span class="btn-loading" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
                    </button>
                </form>

                <div class="form-options my-4">
                    <p class=""><a href="{{ route('loginPage') }}">Login</a></p>
                    <p class=""><a
                            href="{{ route('homepage') }}">Homepage</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
    <script>
        // Get the elements
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        // Toggle password visibility
        togglePassword.addEventListener('click', function() {
            // Toggle the type between "password" and "text"
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            // Toggle the eye icon (between eye and eye-slash)
            if (passwordField.type === 'password') {
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            } else {
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            }
        });
    </script>



@endsection
