@extends('layouts.authLayout')
@section('title', 'Authenticate Login')
@section('content')
    <div class="outer-container">
        @include('utilities.pages.auth.include.slider')
        <div class="login-form">
            <div class="form-container">
                @include('utilities.pages.auth.include.headerLogo')


                <h2 class="m-2 text-center auth_header">Authenticate Login</h2>
                <p class="m-2 text-center">Enter the six (6) digit OTP sent to your email</p>
                <form class="" action="{{ route('verifyLoginOtpSubmit') }}" method="POST">
                    @csrf
                    <input type="text" placeholder="OTP" name="login_otp" required autocomplete="off" />
                    <p style="color: red">{{ $errors->first('login_otp') }}</p>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">
                            Submit
                        </span>

                        <span class="btn-loading" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
                    </button>
                </form>

                <div class="d-flex gap-2 mt-4">

                    <a href="{{ route('resendLoginOtp') }}" class="btn btn-primary flex-fill py-2">
                        <i class="fas fa-rotate-right me-2"></i>
                        Resend OTP
                    </a>

                    <a href="{{ route('logoutRequest') }}" class="btn btn-outline-danger flex-fill py-2">
                        <i class="fas fa-right-from-bracket me-2"></i>
                        Logout
                    </a>

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
