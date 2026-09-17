@extends('layouts.authLayout')
@section('title', 'Register')
@section('content')
    <div class="outer-container">
        @include('utilities.pages.auth.include.slider')
        <div class="login-form">
            <div class="form-container">
                @include('utilities.pages.auth.include.headerLogo')


                <h2 class="m-2 text-center auth_header">Register</h2>
                {{-- <p class="m-2 text-center">Please sign in to your account</p> --}}
                <form class="" action="{{ route('submitUserRegistrationRequest') }}" method="POST">
                    @csrf
                    <input type="text" placeholder="Username" name="username" required />
                    <p style="color: red">{{ $errors->first('username') }}</p>
                    <input type="text" placeholder="Phone" name="phone" required />
                    <p style="color: red">{{ $errors->first('phone') }}</p>
                    <input type="email" placeholder="Email" name="email" required />
                    <p style="color: red">{{ $errors->first('email') }}</p>

                    <div class="password-container">
                        <input type="password" id="password" placeholder="Password" name="password" autocomplete="off"
                            value="{{ old('password') }}" required />
                        <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                    </div>
                    <p style="color: red">{{ $errors->first('password') }}</p>

                    <div class="password-container">
                        <input type="password" id="password2" placeholder="Confirm Password" name="confirm_password"
                            autocomplete="off" value="{{ old('confirm_password') }}" required />
                        <i class="fa fa-eye" id="togglePassword2" style="cursor: pointer;"></i>
                    </div>
                    {{-- password rules --}}
                    <div id="password-rules" class="mt-3">
                        <ul>
                            <div class="col-md-12">
                                <span class="text-bold text-danger text-sm">Password Rules:</span>
                                <div class="row m-2">
                                    <div class="col-md-12">
                                        <li id="length-rule" class="rule">Minimum 8 of characters</li>
                                        <li id="lowercase-rule" class="rule">At least one lowercase letter</li>
                                        <li id="uppercase-rule" class="rule">At least one uppercase letter</li>
                                        <li id="special-char-rule" class="rule">At least one special character/symbol</li>
                                        <li id="number-rule" class="rule">At least one number</li>
                                        <li id="match-rule" class="rule">
                                            Passwords must be the same
                                        </li>
                                    </div>
                                </div>
                            </div>
                        </ul>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">
                            Submit
                        </span>

                        <span class="btn-loading" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
                    </button>
                </form>

                <div class="form-options my-4">
                    <p class=""><a href="{{ route('loginPage') }}">Login</a></p>
                    <p class=""><a
                            href="
                        {{ route('forgotPassword') }}
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
        document.addEventListener('DOMContentLoaded', function() {
            // Get the elements for password fields
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            const togglePassword2 = document.getElementById('togglePassword2');
            const passwordField2 = document.getElementById('password2');

            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordField.type === 'password' ? 'text' : 'password';
                passwordField.type = type;

                // Toggle the eye icon (between eye and eye-slash)
                this.classList.toggle('fa-eye-slash');
                this.classList.toggle('fa-eye');
            });

            togglePassword2.addEventListener('click', function() {
                const type = passwordField2.type === 'password' ? 'text' : 'password';
                passwordField2.type = type;

                // Toggle the eye icon (between eye and eye-slash)
                this.classList.toggle('fa-eye-slash');
                this.classList.toggle('fa-eye');
            });

            // Password rules validation
            passwordField.addEventListener('input', function() {
                validatePassword();
                matchPasswords();
            });

            passwordField2.addEventListener('input', function() {
                matchPasswords();
            });

            function validatePassword() {
                const passwordValue = passwordField.value;

                // Length rule
                const lengthRule = document.getElementById('length-rule');
                if (passwordValue.length >= 8) {
                    lengthRule.style.color = 'green';
                } else {
                    lengthRule.style.color = 'red';
                }

                // Lowercase letter rule
                const lowercaseRule = document.getElementById('lowercase-rule');
                if (/[a-z]/.test(passwordValue)) {
                    lowercaseRule.style.color = 'green';
                } else {
                    lowercaseRule.style.color = 'red';
                }

                // Uppercase letter rule
                const uppercaseRule = document.getElementById('uppercase-rule');
                if (/[A-Z]/.test(passwordValue)) {
                    uppercaseRule.style.color = 'green';
                } else {
                    uppercaseRule.style.color = 'red';
                }

                // Special character rule
                const specialCharRule = document.getElementById('special-char-rule');
                if (/[^A-Za-z0-9]/.test(passwordValue)) {
                    specialCharRule.style.color = 'green';
                } else {
                    specialCharRule.style.color = 'red';
                }

                // Number rule
                const numberRule = document.getElementById('number-rule');
                if (/\d/.test(passwordValue)) {
                    numberRule.style.color = 'green';
                } else {
                    numberRule.style.color = 'red';
                }
            }

            function matchPasswords() {
                const passwordValue = passwordField.value;
                const confirmPasswordValue = passwordField2.value;
                const matchRule = document.getElementById('match-rule');

                if (passwordValue !== confirmPasswordValue) {
                    matchRule.style.color = 'red'; // If passwords don't match
                } else {
                    matchRule.style.color = 'green'; // If passwords match
                }
            }
        });
    </script>


    <style>
        #password-rules {
            padding: 0;
            margin: 0;
            list-style-type: none;
        }

        #password-rules ul {
            padding: 0;
            margin: 0;
        }

        #password-rules li {
            font-size: 12px;
            margin-bottom: 2px;
        }

        .rule {
            font-size: 14px;
            color: red;
        }

        .password-container {
            margin-bottom: 15px;
        }

        #password-error,
        #confirm-password-error {
            font-size: 12px;
            color: red;
            margin-top: 5px;
        }
    </style>

@endsection
