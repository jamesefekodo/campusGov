@extends('layouts.authLayout')
@section('title', 'Login')
@section('content')
    <div class="outer-container">
        @include('utilities.pages.auth.include.slider')
        <div class="login-form">
            <div class="form-container">
                @include('utilities.pages.auth.include.headerLogo')


                <h2 class="m-2 text-center auth_header">Welcome Back</h2>
                <p class="m-2 text-center">Please sign in to your account</p>
                <form class="" {{-- action="{{ route('submitLoginRequest') }}" --}} method="POST">
                    @csrf
                    <input type="text" placeholder="Username/Email" name="login_id" required />
                    <p style="color: red">{{ $errors->first('login_id') }}</p>

                    <div class="password-container">
                        <input type="password" id="password" placeholder="********" name="password" autocomplete="off"
                            value="{{ old('password') }}" required />
                        <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                    </div>
                    <p style="color: red">{{ $errors->first('password') }}</p>

                    <button type="submit" class="">
                        <span class="btn-text">
                            Login
                        </span>

                        <span class="btn-loading" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
                    </button>
                </form>

                <div class="form-options my-4">
                    <p class="">Have no account? <a href="{{ route('registrationPage') }}">Register</a></p>
                    <p class=""><a
                            href="
                        {{-- {{ route('forgotPassword') }} --}}
                         ">Forgot
                            Password</a>
                    </p>
                    <p class=""><a
                            href="
                        {{ route('homepage') }}
                         ">Homepage</a>
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
