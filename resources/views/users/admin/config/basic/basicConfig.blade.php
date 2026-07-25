@extends('layouts.userLayout')
@section('title', 'Basic System Configuration')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        Basic System Configuration
                                    </h4>
                                    <div class="col-md-12 row px-4 py-1 g-2">
                                        {{-- academic year start --}}
                                        <div class="col-md-6 p-1">
                                            <div class="card stretch-full shadow-sm border-0">

                                                <div class="card-header p-0 border-0">
                                                    <h3
                                                        class="w-100 text-center py-3 mb-0 text-uppercase bg-blue text-white">
                                                        <i class="fas fa-calendar-days me-2"></i>
                                                        Academic Year
                                                    </h3>
                                                </div>

                                                <form class="form my_form" method="POST" enctype="multipart/form-data"
                                                    action="{{ route('updateAcademicYear') }}">

                                                    @csrf
                                                    @method('PUT')

                                                    <div class="card-body">

                                                        <div class="text-center mb-4">
                                                            <label class="form-label fw-semibold d-block">
                                                                Current
                                                            </label>

                                                            <span
                                                                class="badge bg-blue text-white fs-5 px-4 py-2 rounded-pill">
                                                                {{ $data['basicSystemSetting']->current_academic_year ?? 'Not Set' }}
                                                            </span>
                                                        </div>


                                                        <div class="d-flex gap-2">
                                                            <button type="submit" name="direction" value="backward"
                                                                class="btn btn-outline-secondary flex-fill py-2">
                                                                <i class="fas fa-chevron-left me-2"></i>
                                                                Previous Session
                                                            </button>

                                                            <button type="submit" name="direction" value="forward"
                                                                class="btn btn-outline-primary flex-fill py-2">
                                                                Next Session
                                                                <i class="fas fa-chevron-right ms-2"></i>
                                                            </button>
                                                        </div>

                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                        {{-- academic year end --}}

                                        {{-- maintenance mode start --}}
                                        <div class="col-md-6 p-1">
                                            @php
                                                $isOn = $data['basicSystemSetting']->maintenance_mode === true;
                                            @endphp

                                            <div class="card stretch-full shadow-sm border-0">

                                                <div class="card-header p-0 border-0">
                                                    <h3
                                                        class="w-100 text-center py-3 mb-0 text-uppercase
                {{ $isOn ? 'bg-soft-danger text-danger' : 'bg-soft-primary text-primary' }}">
                                                        <i class="fas fa-tools me-2"></i>
                                                        Maintenance Mode
                                                    </h3>
                                                </div>

                                                <form class="form my_form" method="POST" enctype="multipart/form-data"
                                                    action="{{ route('updateMaintenanceMode') }}">

                                                    @csrf
                                                    @method('PUT')

                                                    <div class="card-body">

                                                        <div class="text-center mb-4">
                                                            <label class="form-label fw-semibold d-block">
                                                                Current Status
                                                            </label>

                                                            <span
                                                                class="badge {{ $isOn ? 'bg-danger' : 'bg-success' }} text-white fs-5 px-4 py-2 rounded-pill">
                                                                <i
                                                                    class="fas {{ $isOn ? 'fa-lock' : 'fa-check-circle' }} me-2"></i>
                                                                {{ $isOn ? 'Enabled' : 'Disabled' }}
                                                            </span>
                                                        </div>


                                                        {{-- Send opposite status --}}
                                                        <input type="hidden" name="maintenance_mode"
                                                            value="{{ $isOn ? 'false' : 'true' }}">


                                                        <button type="submit"
                                                            class="btn {{ $isOn ? 'btn-success' : 'btn-danger' }} w-100 submit-btn py-2">

                                                            <span class="btn-text">
                                                                <i
                                                                    class="fas {{ $isOn ? 'fa-power-off' : 'fa-tools' }} me-2"></i>

                                                                {{ $isOn ? 'Disable Maintenance Mode' : 'Enable Maintenance Mode' }}
                                                            </span>

                                                            <span class="btn-loading" style="display:none;">
                                                                <i class="fas fa-spinner fa-spin"></i> Processing...
                                                            </span>

                                                        </button>

                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                        {{-- maintenance mode end --}}
                                        {{-- system info start --}}
                                        <div class="col-md-12 p-1">
                                            <div class="card stretch-full shadow-sm border-0">

                                                <div class="card-header p-0 border-0">
                                                    <h3
                                                        class="w-100 text-center py-3 mb-0 text-uppercase bg-blue text-white">
                                                        <i class="fas fa-circle-info me-2"></i>
                                                        System Info
                                                    </h3>
                                                </div>

                                                <form class="form my_form" method="POST"
                                                    action="{{ route('updateBasicSystemInfoUpdate') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row">
                                                        <div class="mb-4 col-md-4">
                                                            <label class="form-label">App Name <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control text-center"
                                                                name="APP_NAME" value="{{ $data['appName'] }}" required>
                                                            <p class="text-danger">{{ $errors->first('APP_NAME') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-4">
                                                            <label class="form-label">App URL <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control text-center"
                                                                name="APP_URL" value="{{ $data['appUrl'] }}" required>
                                                            <p class="text-danger">{{ $errors->first('APP_URL') }}
                                                            </p>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Timezone <span class="text-danger">*</span></label>
                                                            <select class="form-select" name="timezone" required>
                                                                @foreach ($data['timezones'] as $timezone)
                                                                    @php
                                                                        $dateTime = new \DateTime(
                                                                            'now',
                                                                            new \DateTimeZone($timezone),
                                                                        );
                                                                        $offset = 'GMT' . $dateTime->format('P');
                                                                    @endphp

                                                                    <option value="{{ $timezone }}"
                                                                        {{ old('timezone', $data['timezone']) == $timezone ? 'selected' : '' }}>
                                                                        {{ str_replace('_', ' ', $timezone) }}
                                                                        ({{ $offset }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <p class="text-danger">{{ $errors->first('timezone') }}

                                                        </div>
                                                        <div class="mb-4 col-md-4">
                                                            <label class="form-label">Association Name<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control text-center"
                                                                name="association_name"
                                                                value="{{ $data['basicSystemSetting']->association_name }}"
                                                                required>
                                                            <p class="text-danger">{{ $errors->first('association_name') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-4">
                                                            <label class="form-label">Association Acronym<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control text-center"
                                                                name="association_acronym"
                                                                value="{{ $data['basicSystemSetting']->association_acronym }}"
                                                                required>
                                                            <p class="text-danger">
                                                                {{ $errors->first('association_acronym') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-4">
                                                            <label class="form-label">Association Slogan<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control text-center"
                                                                name="association_slogan"
                                                                value="{{ $data['basicSystemSetting']->association_slogan }}"
                                                                required>
                                                            <p class="text-danger">
                                                                {{ $errors->first('association_slogan') }}
                                                            </p>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label for="description">
                                                                Association Description<span class="text-red">*</span>:
                                                            </label>
                                                            <textarea id="description" name="description" required>{{ $data['basicSystemSetting']->description }}</textarea>
                                                            <p style="color: red">{{ $errors->first('description') }}</p>
                                                            <script>
                                                                $(document).ready(function() {
                                                                    $('#description').summernote({
                                                                        height: 200, // Set editor height
                                                                        toolbar: [
                                                                            // [groupName, [list of buttons]]
                                                                            ['style', ['bold', 'italic', 'underline', 'clear']],
                                                                            ['font', ['strikethrough', 'superscript', 'subscript']],
                                                                            ['fontsize', ['fontsize']],
                                                                            ['color', ['color']],
                                                                            ['para', ['ul', 'ol', 'paragraph']],
                                                                            ['height', ['height']],
                                                                            ['insert', []],
                                                                            ['view', []]
                                                                        ]
                                                                    });
                                                                });
                                                            </script>
                                                        </div>

                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Operational Level<span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select text-center"
                                                                name="operational_level" required>

                                                                <option disabled
                                                                    {{ is_null($data['basicSystemSetting']->operational_level) ? 'selected' : '' }}>
                                                                    - Select -
                                                                </option>

                                                                <option value="departnemt"
                                                                    {{ $data['basicSystemSetting']->operational_level == 'departnemt' ? 'selected' : '' }}>
                                                                    Department
                                                                </option>
                                                                <option value="faculty"
                                                                    {{ $data['basicSystemSetting']->operational_level == 'faculty' ? 'selected' : '' }}>
                                                                    Faculty (or School)
                                                                </option>
                                                                <option value="institute"
                                                                    {{ $data['basicSystemSetting']->operational_level == 'institute' ? 'selected' : '' }}>
                                                                    Institute (University, Polytechnic)
                                                                </option>

                                                            </select>

                                                            <p class="text-danger">
                                                                {{ $errors->first('association_acronym') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Is the institution a
                                                                polytechnic?<span class="text-danger">*</span></label>
                                                            <select class="form-select text-center" name="is_polytechnic"
                                                                required>

                                                                <option disabled
                                                                    {{ is_null($data['basicSystemSetting']->is_polytechnic) ? 'selected' : '' }}>
                                                                    - Select -
                                                                </option>

                                                                <option value="1"
                                                                    {{ old('is_polytechnic', $data['basicSystemSetting']->is_polytechnic) == 1 ? 'selected' : '' }}>
                                                                    Yes
                                                                </option>

                                                                <option value="0"
                                                                    {{ old('is_polytechnic', $data['basicSystemSetting']->is_polytechnic) == 0 ? 'selected' : '' }}>
                                                                    No
                                                                </option>
                                                            </select>

                                                            <p class="text-danger">
                                                                {{ $errors->first('is_polytechnic') }}
                                                            </p>
                                                        </div>

                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Institute Name<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control text-center"
                                                                name="school_name"
                                                                value="{{ $data['basicSystemSetting']->school_name }}"
                                                                required>
                                                            <p class="text-danger">{{ $errors->first('school_name') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Institute Acronym<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control text-center"
                                                                name="school_acronym"
                                                                value="{{ $data['basicSystemSetting']->school_acronym }}"
                                                                required>
                                                            <p class="text-danger">
                                                                {{ $errors->first('school_acronym') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $colors = [
                                                            'primary_colour' => 'Primary Colour',
                                                            'secondary_colour' => 'Secondary Colour',
                                                            'tertiary_colour' => 'Tertiary Colour',
                                                        ];
                                                    @endphp

                                                    <div class="row">

                                                        @foreach ($colors as $field => $label)
                                                            @php
                                                                $value = old(
                                                                    $field,
                                                                    $data['basicSystemSetting']->{$field} ?? '#000000',
                                                                );
                                                            @endphp

                                                            <div class="col-md-4 mb-3">

                                                                <label class="form-label fw-semibold">
                                                                    {{ $label }}
                                                                </label>

                                                                <div class="input-group">

                                                                    <input type="text" name="{{ $field }}"
                                                                        class="form-control text-center fw-semibold color-text"
                                                                        value="{{ $value }}" maxlength="7"
                                                                        placeholder="#000000"
                                                                        style="border:2px solid {{ $value }};"
                                                                        oninput="syncColor(this,'text')">

                                                                    <input type="color"
                                                                        class="form-control form-control-color color-picker"
                                                                        value="{{ $value }}"
                                                                        style="max-width:65px;"
                                                                        oninput="syncColor(this,'picker')">

                                                                </div>

                                                                @error($field)
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror

                                                            </div>
                                                        @endforeach

                                                    </div>

                                                    <script>
                                                        function syncColor(element, type) {

                                                            const group = element.closest('.input-group');
                                                            const text = group.querySelector('.color-text');
                                                            const picker = group.querySelector('.color-picker');

                                                            let value = type === 'picker' ?
                                                                element.value :
                                                                element.value;

                                                            value = value.replace(/[^#a-fA-F0-9]/g, '');

                                                            if (!value.startsWith('#')) {
                                                                value = '#' + value.replace(/^#+/, '');
                                                            }

                                                            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                                                                text.value = value.toUpperCase();
                                                                picker.value = value;
                                                                text.style.borderColor = value;
                                                            } else {
                                                                text.value = value.toUpperCase();
                                                            }
                                                        }
                                                    </script>

                                                    @php
                                                        $associationLogo = asset('system/images/logo_placeholder.png');

                                                        if (
                                                            !empty($data['basicSystemSetting']->association_logo) &&
                                                            file_exists(
                                                                public_path(
                                                                    'system/images/' .
                                                                        $data['basicSystemSetting']->association_logo,
                                                                ),
                                                            )
                                                        ) {
                                                            $associationLogo = asset(
                                                                'system/images/' .
                                                                    $data['basicSystemSetting']->association_logo,
                                                            );
                                                        }

                                                        $schoolLogo = asset('system/images/logo_placeholder.png');

                                                        if (
                                                            !empty($data['basicSystemSetting']->school_logo) &&
                                                            file_exists(
                                                                public_path(
                                                                    'system/images/' .
                                                                        $data['basicSystemSetting']->school_logo,
                                                                ),
                                                            )
                                                        ) {
                                                            $schoolLogo = asset(
                                                                'system/images/' .
                                                                    $data['basicSystemSetting']->school_logo,
                                                            );
                                                        }
                                                    @endphp

                                                    <div class="row">

                                                        {{-- Association Logo --}}
                                                        <div class="col-md-6">

                                                            <div class="card shadow-sm border-0 mb-3">

                                                                <div class="card-header bg-light">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-image me-1"></i>
                                                                        Association Logo
                                                                    </h6>
                                                                </div>

                                                                <div class="card-body text-center">

                                                                    <img id="association-logo-preview"
                                                                        src="{{ $associationLogo }}"
                                                                        alt="Association Logo"
                                                                        class="img-fluid rounded border asset-preview"
                                                                        data-default="{{ empty($data['basicSystemSetting']->association_logo) ? '1' : '0' }}"
                                                                        style="
                        width:100%;
                        max-width:420px;
                        height:250px;
                        object-fit:contain;
                        background:#f8f9fa;
                    ">

                                                                    <div class="mt-3">

                                                                        <label for="association-logo-upload"
                                                                            class="btn btn-sm btn-outline-primary">

                                                                            <i class="fa fa-upload me-1"></i>
                                                                            Association Logo
                                                                        </label>

                                                                        <input type="file" id="association-logo-upload"
                                                                            name="association_logo"
                                                                            accept=".jpg,.jpeg,.png,.webp" hidden>

                                                                    </div>

                                                                    <small class="text-muted d-block mt-2">
                                                                        Accepted: JPG, PNG, WEBP
                                                                    </small>

                                                                    <p class="text-danger mt-2 mb-0">
                                                                        {{ $errors->first('association_logo') }}
                                                                    </p>

                                                                </div>

                                                            </div>

                                                        </div>

                                                        {{-- School Logo --}}
                                                        <div class="col-md-6">

                                                            <div class="card shadow-sm border-0 mb-3">

                                                                <div class="card-header bg-light">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-image me-1"></i>
                                                                        School Logo
                                                                    </h6>
                                                                </div>

                                                                <div class="card-body text-center">

                                                                    <img id="school-logo-preview"
                                                                        src="{{ $schoolLogo }}" alt="School Logo"
                                                                        class="img-fluid rounded border asset-preview"
                                                                        data-default="{{ empty($data['basicSystemSetting']->school_logo) ? '1' : '0' }}"
                                                                        style="
                        width:100%;
                        max-width:420px;
                        height:250px;
                        object-fit:contain;
                        background:#f8f9fa;
                    ">

                                                                    <div class="mt-3">

                                                                        <label for="school-logo-upload"
                                                                            class="btn btn-sm btn-outline-primary">

                                                                            <i class="fa fa-upload me-1"></i>
                                                                            School Logo
                                                                        </label>

                                                                        <input type="file" id="school-logo-upload"
                                                                            name="school_logo"
                                                                            accept=".jpg,.jpeg,.png,.webp" hidden>

                                                                    </div>

                                                                    <small class="text-muted d-block mt-2">
                                                                        Accepted: JPG, PNG, WEBP
                                                                    </small>

                                                                    <p class="text-danger mt-2 mb-0">
                                                                        {{ $errors->first('school_logo') }}
                                                                    </p>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <style>
                                                        .asset-preview[data-default="1"] {
                                                            filter: grayscale(100%);
                                                            opacity: 0.75;
                                                        }
                                                    </style>

                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {

                                                            function setupImagePreview(inputId, previewId) {

                                                                const input = document.getElementById(inputId);
                                                                const preview = document.getElementById(previewId);

                                                                if (!input || !preview) return;

                                                                input.addEventListener('change', function() {

                                                                    const file = this.files[0];

                                                                    if (!file) return;

                                                                    const allowedTypes = [
                                                                        'image/jpeg',
                                                                        'image/jpg',
                                                                        'image/png',
                                                                        'image/webp'
                                                                    ];

                                                                    if (!allowedTypes.includes(file.type)) {

                                                                        alert('Please select a valid image file.');

                                                                        this.value = '';

                                                                        return;
                                                                    }

                                                                    preview.src = URL.createObjectURL(file);

                                                                    // Remove grayscale after selecting a new image
                                                                    preview.setAttribute('data-default', '0');
                                                                });
                                                            }

                                                            setupImagePreview('association-logo-upload', 'association-logo-preview');
                                                            setupImagePreview('school-logo-upload', 'school-logo-preview');

                                                        });
                                                    </script>



                                                    <button type="submit" class="btn btn-primary w-100 submit-btn">
                                                        <span class="btn-text">
                                                            Update
                                                        </span>

                                                        <span class="btn-loading" style="display:none;">
                                                            <i class="fas fa-spinner fa-spin"></i> Processing...
                                                        </span>
                                                    </button>

                                                </form>

                                            </div>
                                        </div>
                                        {{-- system info end --}}

                                        {{-- email setup start --}}
                                        <div class="col-md-6 p-1">
                                            <div class="card stretch-full shadow-sm border-0">

                                                <div class="card-header p-0 border-0">
                                                    <h3
                                                        class="w-100 text-center py-3 mb-0 text-uppercase bg-warning-subtle text-warning">
                                                        <i class="fas fa-envelope-open me-2"></i>
                                                        Email Config
                                                    </h3>
                                                </div>

                                                <form class="form my_form" method="POST"
                                                    action="{{ route('updateEmailConfigSubmit') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row">
                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Mailer</label>
                                                            <select class="form-control text-center" name="MAIL_MAILER"
                                                                id="MAIL_MAILER" required>
                                                                <option value="smtp"
                                                                    {{ old('MAIL_MAILER', $data['MAIL_MAILER']) == 'smtp' ? 'selected' : '' }}>
                                                                    SMTP</option>
                                                                <option value="sendmail"
                                                                    {{ old('MAIL_MAILER', $data['MAIL_MAILER']) == 'sendmail' ? 'selected' : '' }}>
                                                                    Sendmail</option>
                                                                <option value="mailgun"
                                                                    {{ old('MAIL_MAILER', $data['MAIL_MAILER']) == 'mailgun' ? 'selected' : '' }}>
                                                                    Mailgun</option>
                                                                <option value="ses"
                                                                    {{ old('MAIL_MAILER', $data['MAIL_MAILER']) == 'ses' ? 'selected' : '' }}>
                                                                    SES</option>
                                                            </select>
                                                            <p class="text-danger">{{ $errors->first('MAIL_MAILER') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Email host</label>
                                                            <input type="text" class="form-control text-center"
                                                                name="MAIL_HOST" value="{{ $data['MAIL_HOST'] }}">
                                                            <p class="text-danger">{{ $errors->first('MAIL_HOST') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Port</label>
                                                            <input type="text" class="form-control text-center"
                                                                name="MAIL_PORT" value="{{ $data['MAIL_PORT'] }}">
                                                            <p class="text-danger">{{ $errors->first('MAIL_PORT') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Mail Username</label>
                                                            <input type="text" class="form-control text-center"
                                                                name="MAIL_USERNAME"
                                                                value="{{ $data['MAIL_USERNAME'] }}">
                                                            <p class="text-danger">{{ $errors->first('MAIL_USERNAME') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Mail Password</label>
                                                            <input type="text" class="form-control text-center"
                                                                name="MAIL_PASSWORD"
                                                                value="{{ $data['MAIL_PASSWORD'] }}">
                                                            <p class="text-danger">{{ $errors->first('MAIL_PASSWORD') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-6">
                                                            <label class="form-label">Email Encryption</label>
                                                            <input type="text" class="form-control text-center"
                                                                name="MAIL_ENCRYPTION"
                                                                value="{{ $data['MAIL_ENCRYPTION'] }}">
                                                            <p class="text-danger">{{ $errors->first('MAIL_ENCRYPTION') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4 col-md-12">
                                                            <label class="form-label">Sender's Address</label>
                                                            <input type="text" class="form-control text-center"
                                                                name="MAIL_FROM_ADDRESS"
                                                                value="{{ $data['MAIL_FROM_ADDRESS'] }}">
                                                            <p class="text-danger">
                                                                {{ $errors->first('MAIL_FROM_ADDRESS') }}
                                                            </p>
                                                        </div>
                                                    </div>





                                                    <button type="submit" class="btn btn-warning w-100 submit-btn">
                                                        <span class="btn-text">
                                                            Update
                                                        </span>

                                                        <span class="btn-loading" style="display:none;">
                                                            <i class="fas fa-spinner fa-spin"></i> Processing...
                                                        </span>
                                                    </button>

                                                </form>

                                            </div>
                                        </div>
                                        {{-- email setup ends --}}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
