<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Change Password</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST"
                        @php
if(request()->routeIs('viewAdmin')){
                        $isForAdminManagement = true;
                        $isForAdminSelf = false;
                    }elseif(request()->routeIs('adminSelfProfile')){
                        $isForAdminManagement = false;
                        $isForAdminSelf = true;
                    }
                    else{
                        $isForAdminManagement = false;
                        $isForAdminSelf = false;
                    } @endphp
                        @if ($isForAdminManagement == true) action="{{ route('changeAdminPasswordSubmit', ['uuid' => $data['admin']->uuid]) }}"
                        @elseif ($isForAdminSelf == true)
                        action="{{ route('adminChangeSelfPasswordSubmit') }}"
                    @else
                         action="{{ route('changeSelfPasswordSubmit') }}" @endif
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            @if ($isForAdminManagement == false)
                                <div class="form-group col-md-12">
                                    <label for="current_password">Current Password <span
                                            class="text-red">*</span></label>
                                    <input type="text" value="{{ old('current_password') }}" class="form-control"
                                        autocomplete="off" name="current_password" required>
                                    <p style="color: red">{{ $errors->first('current_password') }}</p>
                                </div>
                            @endif
                            {{-- Password --}}
                            <div class="form-group col-md-12">
                                <label for="password" class="form-label">
                                    New Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password"
                                        autocomplete="off" value="{{ old('password') }}" required>
                                    <span class="input-group-text" style="cursor:pointer;" id="togglePassword">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                                <p style="color:red; font-size:13px;">{{ $errors->first('password') }}</p>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="form-group col-md-12">
                                <label for="password2" class="form-label">
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password2" name="confirm_password"
                                        autocomplete="off" value="{{ old('confirm_password') }}" required>
                                    <span class="input-group-text" style="cursor:pointer;" id="togglePassword2">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                                <p style="color:red; font-size:13px;">{{ $errors->first('confirm_password') }}</p>
                            </div>

                            {{-- Password Rules --}}
                            <div class="col-md-12 mb-3">
                                <span class="fw-bold text-danger" style="font-size:13px;">Password Rules:</span>
                                <div class="row col-md-12">
                                    <div class="col-md-6">
                                        <ul style="list-style:none; padding:0; margin:6px 0 0;">
                                            <li id="length-rule" class="rule">✗ Minimum 8 characters</li>
                                            <li id="uppercase-rule" class="rule">✗ At least one uppercase letter</li>
                                            <li id="number-rule" class="rule">✗ At least one number</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul style="list-style:none; padding:0; margin:6px 0 0;">
                                            <li id="lowercase-rule" class="rule">✗ At least one lowercase letter</li>
                                            <li id="special-char-rule" class="rule">✗ At least one special character
                                            </li>
                                            <li id="match-rule" class="rule">✗ Passwords must match</li>
                                        </ul>
                                    </div>
                                </div>

                            </div>



                        </div>
                        <hr>

                        <button type="submit" class="green-button submit-btn">
                            <span class="btn-text">
                                Submit
                            </span>

                            <span class="btn-loading" style="display:none;">
                                <i class="fas fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                    </form>
                    {{-- </div> --}}


                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>



<style>
    .rule {
        font-size: 13px;
        color: red;
        margin-bottom: 4px;
        transition: color .2s;
    }

    .rule.pass {
        color: green;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const passwordField = document.getElementById('password');
        const passwordField2 = document.getElementById('password2');
        const togglePassword = document.getElementById('togglePassword');
        const togglePassword2 = document.getElementById('togglePassword2');

        // ── Toggle visibility ──────────────────────────────────────────────────
        togglePassword.addEventListener('click', function() {
            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        togglePassword2.addEventListener('click', function() {
            const isPassword = passwordField2.type === 'password';
            passwordField2.type = isPassword ? 'text' : 'password';
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // ── Rules ──────────────────────────────────────────────────────────────
        function setRule(id, passes) {
            const el = document.getElementById(id);
            const label = {
                'length-rule': 'Minimum 8 characters',
                'lowercase-rule': 'At least one lowercase letter',
                'uppercase-rule': 'At least one uppercase letter',
                'special-char-rule': 'At least one special character',
                'number-rule': 'At least one number',
                'match-rule': 'Passwords must match',
            };
            el.textContent = (passes ? '✓ ' : '✗ ') + label[id];
            el.classList.toggle('pass', passes);
        }

        function validatePassword() {
            const val = passwordField.value;
            setRule('length-rule', val.length >= 8);
            setRule('lowercase-rule', /[a-z]/.test(val));
            setRule('uppercase-rule', /[A-Z]/.test(val));
            setRule('special-char-rule', /[^A-Za-z0-9]/.test(val));
            setRule('number-rule', /\d/.test(val));
            matchPasswords();
        }

        function matchPasswords() {
            setRule('match-rule',
                passwordField.value.length > 0 &&
                passwordField.value === passwordField2.value
            );
        }

        passwordField.addEventListener('input', validatePassword);
        passwordField2.addEventListener('input', matchPasswords);
    });
</script>
