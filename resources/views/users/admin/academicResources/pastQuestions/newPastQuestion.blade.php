@extends('layouts.userLayout')
@section('title', 'Add New Past Question')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">
                <div class="navigation">
                    {{-- @include('users.admin.academicResources.pastQuestions.include.inner_nav') --}}
                </div>
                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container pb-5">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        Add New Past Question
                                    </h4>
                                    <div class="col-md-12 row px-4 py-1 g-2">
                                        <div class="col-md-12 p-1">
                                            <div class="card stretch-full shadow-sm border-0">
                                                <form class="form my_form" method="POST"
                                                    action="{{ route('addNewPastQuestionSubmit') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-4">
                                                            <label for="program-select">
                                                                Program <span class="text-red">*</span>
                                                            </label>

                                                            <select class="form-select" id="program-select" name="program"
                                                                required>

                                                                <option value="">- Select -</option>

                                                                @foreach ($data['activeProgram'] as $program)
                                                                    <option value="{{ $program->slug }}"
                                                                        {{ old('program') == $program->slug ? 'selected' : '' }}>
                                                                        {{ $program->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                            <p class="text-danger">{{ $errors->first('program') }}</p>
                                                        </div>

                                                        <div class="form-group col-md-4">
                                                            <label for="program-type-select">
                                                                Program Type <span class="text-red">*</span>
                                                            </label>

                                                            <select class="form-select" id="program-type-select"
                                                                name="program_type" required disabled>

                                                                <option value="">- Select Program First -</option>
                                                            </select>

                                                            <p class="text-danger">{{ $errors->first('program_type') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label for="faculty-select">
                                                                Faculty <span class="text-red">*</span>
                                                            </label>

                                                            <select class="form-select" id="faculty-select" name="faculty"
                                                                required>

                                                                <option value="">- Select -</option>

                                                                @foreach ($data['activeFaculties'] as $faculty)
                                                                    <option value="{{ $faculty->slug }}"
                                                                        {{ old('faculty') == $faculty->slug ? 'selected' : '' }}>
                                                                        {{ $faculty->name }}
                                                                    </option>
                                                                @endforeach

                                                            </select>

                                                            <p class="text-danger">{{ $errors->first('faculty') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label for="department-select">
                                                                Department <span class="text-red">*</span>
                                                            </label>

                                                            <select class="form-select" id="department-select"
                                                                name="department" required disabled>

                                                                <option value="">- Select Faculty First -</option>

                                                            </select>

                                                            <p class="text-danger">{{ $errors->first('department') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label for="academic_year">Academic Session<span
                                                                    class="text-red">*</span>:</label>
                                                            <select name="academic_year" class="form-control" required>
                                                                <option value="" disabled selected>Select</option>
                                                                @php
                                                                    $startYear = schoolFoundingYear();
                                                                    $endYear = currentAcademicYear();
                                                                @endphp

                                                                @for ($year = $endYear; $year >= $startYear; $year--)
                                                                    <option value="{{ $year }}"
                                                                        {{ old('academic_year') == "$year" ? 'selected' : '' }}>
                                                                        {{ $year }}/{{ $year + 1 }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                            <p style="color: red">{{ $errors->first('academic_year') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label for="level">Level (Year)<span
                                                                    class="text-red">*</span>:</label>
                                                            <select name="level" class="form-control" required>
                                                                <option value="" disabled selected>Select</option>

                                                                <option value="1"
                                                                    {{ old('level') == '1' ? 'selected' : '' }}>
                                                                    Year 1
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('level') == '2' ? 'selected' : '' }}>
                                                                    Year 2
                                                                </option>
                                                                <option value="3"
                                                                    {{ old('level') == '3' ? 'selected' : '' }}>
                                                                    Year 3
                                                                </option>
                                                                <option value="4"
                                                                    {{ old('level') == '4' ? 'selected' : '' }}>
                                                                    Year 4
                                                                </option>
                                                                <option value="5"
                                                                    {{ old('level') == '5' ? 'selected' : '' }}>
                                                                    Year 5
                                                                </option>
                                                            </select>
                                                            <p style="color: red">{{ $errors->first('level') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label for="semester">Semester<span
                                                                    class="text-red">*</span>:</label>
                                                            <select name="semester" class="form-control" required>
                                                                <option value="" disabled selected>Select</option>

                                                                <option value="first"
                                                                    {{ old('semester') == 'first' ? 'selected' : '' }}>
                                                                    First
                                                                </option>
                                                                <option value="second"
                                                                    {{ old('semester') == 'second' ? 'selected' : '' }}>
                                                                    Second
                                                                </option>
                                                                <option value="third"
                                                                    {{ old('semester') == 'third' ? 'selected' : '' }}>
                                                                    Third
                                                                </option>
                                                                <option value="fourth"
                                                                    {{ old('semester') == 'fourth' ? 'selected' : '' }}>
                                                                    Fourth
                                                                </option>
                                                                <option value="fifth"
                                                                    {{ old('semester') == 'fifth' ? 'selected' : '' }}>
                                                                    Fifth
                                                                </option>
                                                                <option value="sixth"
                                                                    {{ old('semester') == 'sixth' ? 'selected' : '' }}>
                                                                    Sixth
                                                                </option>
                                                            </select>
                                                            <p style="color: red">{{ $errors->first('semester') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label for="is_exam">Type: <span
                                                                    class="text-red">*</span></label>
                                                            <select name="is_exam" class="form-control" required>
                                                                <option value="" disabled selected>Select</option>

                                                                <option value="1"
                                                                    {{ old('is_exam') == '1' ? 'selected' : '' }}>
                                                                    True
                                                                </option>
                                                                <option value="0"
                                                                    {{ old('is_exam') == '0' ? 'selected' : '' }}>
                                                                    False
                                                                </option>
                                                            </select>
                                                            <p style="color: red">{{ $errors->first('course_code') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label for="course_code">Course Code: <span
                                                                    class="text-red">*</span></label>
                                                            <input type="text" class="form-control" name="course_code"
                                                                value="{{ old('course_code') }}" required>
                                                            <p style="color: red">{{ $errors->first('course_code') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="course_title">Course Title: <span
                                                                    class="text-red">*</span></label>
                                                            <input type="text" class="form-control"
                                                                name="course_title" value="{{ old('course_title') }}"
                                                                required>
                                                            <p style="color: red">{{ $errors->first('course_title') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label for="content">Questions (text):</label>
                                                            <textarea id="content" name="content">{{ old('content') }}</textarea>
                                                            <p style="color: red">
                                                                {{ $errors->first('content') }}</p>
                                                        </div>

                                                        @php
                                                            $imagePlaceholder = asset(
                                                                'system/images/wide_img_placeholder.jpeg',
                                                            );
                                                        @endphp

                                                        <div class="form-group col-md-12">
                                                            <label>
                                                                Question Pages
                                                                <span class="text-muted">(Optional, maximum 6
                                                                    images)</span>
                                                            </label>

                                                            <div id="imageUploadList" class="row">

                                                                <div class="image-upload-item col-md-6 mb-3"
                                                                    data-image-index="1">

                                                                    <label for="image_1">
                                                                        Page 1:
                                                                    </label>

                                                                    <div class="smart-image-upload" id="imageUploadBox_1">

                                                                        <input type="file" name="image_1"
                                                                            id="image_1" class="image-file-input"
                                                                            accept="image/jpeg,image/png,image/webp"
                                                                            hidden>

                                                                        <div class="image-preview-wrapper">
                                                                            <img src="{{ $imagePlaceholder }}"
                                                                                id="imagePreview_1" class="image-preview"
                                                                                alt="Page 1 preview">

                                                                            <div class="image-upload-overlay">
                                                                                <div>
                                                                                    <i
                                                                                        class="fas fa-cloud-upload-alt fa-2x mb-2"></i>

                                                                                    <div class="fw-bold">
                                                                                        Click to upload image
                                                                                    </div>

                                                                                    <small>
                                                                                        JPG, PNG or WEBP · Maximum 2MB
                                                                                    </small>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="image-upload-info">
                                                                            <span class="image-file-name">
                                                                                No image selected
                                                                            </span>

                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-danger remove-image d-none">

                                                                                <i class="fas fa-times"></i>
                                                                                Remove
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    <p class="text-danger image-error"></p>
                                                                </div>

                                                            </div>

                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm mt-2"
                                                                id="addImageButton">

                                                                <i class="fas fa-plus"></i>
                                                                Add More Image
                                                            </button>

                                                            <small class="d-block text-muted mt-2">
                                                                All images are optional. You may upload up to 6 images.
                                                            </small>
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

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 justify-content-center text-center mb-4">
                        <a class="btn btn-success" href="{{ route('pastQuestionsHome') }}">

                            <i class="fas fa-chevron-left"></i>
                            Back
                        </a>
                    </div>
                </div>
            </div>


            <style>
                .image-upload-item {
                    animation: imageItemIn .25s ease;
                }

                .smart-image-upload {
                    overflow: hidden;
                    border: 1px solid #dee2e6;
                    border-radius: 10px;
                    background: #fff;
                }

                .smart-image-upload.dragging {
                    border: 2px dashed #0d6efd;
                    background: #eef6ff;
                }

                .image-preview-wrapper {
                    position: relative;
                    height: 260px;
                    overflow: hidden;
                    cursor: pointer;
                    background: #f4f6f8;
                }

                .image-preview {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .image-upload-overlay {
                    position: absolute;
                    inset: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                    background: rgba(0, 0, 0, .44);
                    color: #fff;
                    text-align: center;
                    opacity: 0;
                    transition: opacity .2s ease;
                }

                .image-preview-wrapper:hover .image-upload-overlay {
                    opacity: 1;
                }

                .image-upload-info {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    min-height: 54px;
                    padding: 10px 13px;
                    color: #65757d;
                    font-size: 13px;
                }

                .image-file-name {
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }

                @keyframes imageItemIn {
                    from {
                        opacity: 0;
                        transform: translateY(8px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @media (max-width: 767px) {
                    .image-upload-item {
                        width: 100%;
                    }
                }
            </style>



            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const imageUploadList = document.getElementById('imageUploadList');
                    const addImageButton = document.getElementById('addImageButton');

                    const placeholder = @json($imagePlaceholder);

                    const maxImages = 6;
                    const maxFileSize = 2 * 1024 * 1024;

                    let imageCount = 1;

                    const allowedTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ];

                    function initialiseUploader(item) {
                        const uploadBox = item.querySelector('.smart-image-upload');
                        const fileInput = item.querySelector('.image-file-input');
                        const preview = item.querySelector('.image-preview');
                        const fileName = item.querySelector('.image-file-name');
                        const removeButton = item.querySelector('.remove-image');
                        const errorMessage = item.querySelector('.image-error');

                        uploadBox.addEventListener('click', function(event) {
                            if (event.target.closest('.remove-image')) {
                                return;
                            }

                            fileInput.click();
                        });

                        fileInput.addEventListener('change', function() {
                            const file = this.files && this.files[0];

                            if (file) {
                                showPreview(file);
                            }
                        });

                        removeButton.addEventListener('click', function(event) {
                            event.preventDefault();
                            event.stopPropagation();

                            resetUploader();
                        });

                        uploadBox.addEventListener('dragover', function(event) {
                            event.preventDefault();
                            uploadBox.classList.add('dragging');
                        });

                        uploadBox.addEventListener('dragleave', function() {
                            uploadBox.classList.remove('dragging');
                        });

                        uploadBox.addEventListener('drop', function(event) {
                            event.preventDefault();

                            uploadBox.classList.remove('dragging');

                            const file = event.dataTransfer.files[0];

                            if (!file) {
                                return;
                            }

                            try {
                                const dataTransfer = new DataTransfer();

                                dataTransfer.items.add(file);
                                fileInput.files = dataTransfer.files;

                                showPreview(file);
                            } catch (error) {
                                showPreview(file);
                            }
                        });

                        function showPreview(file) {
                            errorMessage.textContent = '';

                            if (!allowedTypes.includes(file.type)) {
                                errorMessage.textContent =
                                    'Please select a JPG, PNG or WEBP image.';

                                resetUploader();
                                return;
                            }

                            if (file.size > maxFileSize) {
                                errorMessage.textContent =
                                    'Image size must not exceed 2MB.';

                                resetUploader();
                                return;
                            }

                            const reader = new FileReader();

                            reader.onload = function(event) {
                                preview.src = event.target.result;
                                fileName.textContent = file.name;
                                removeButton.classList.remove('d-none');
                            };

                            reader.onerror = function() {
                                errorMessage.textContent =
                                    'Unable to preview this image.';

                                resetUploader();
                            };

                            reader.readAsDataURL(file);
                        }

                        function resetUploader() {
                            fileInput.value = '';
                            preview.src = placeholder;
                            fileName.textContent = 'No image selected';
                            removeButton.classList.add('d-none');
                        }
                    }

                    function createUploader(index) {
                        const item = document.createElement('div');

                        item.className = 'image-upload-item col-md-6 mb-3';
                        item.dataset.imageIndex = index;

                        item.innerHTML = `
                <label for="image_${index}">
                    Page ${index}:
                </label>

                <div
                    class="smart-image-upload"
                    id="imageUploadBox_${index}">

                    <input
                        type="file"
                        name="image_${index}"
                        id="image_${index}"
                        class="image-file-input"
                        accept="image/jpeg,image/png,image/webp"
                        hidden>

                    <div class="image-preview-wrapper">
                        <img
                            src="${placeholder}"
                            class="image-preview"
                            alt="Page ${index} preview">

                        <div class="image-upload-overlay">
                            <div>
                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>

                                <div class="fw-bold">
                                    Click to upload image
                                </div>

                                <small>
                                    JPG, PNG or WEBP · Maximum 2MB
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="image-upload-info">
                        <span class="image-file-name">
                            No image selected
                        </span>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger remove-image d-none">

                            <i class="fas fa-times"></i>
                            Remove
                        </button>
                    </div>
                </div>

                <p class="text-danger image-error"></p>
            `;

                        imageUploadList.appendChild(item);

                        initialiseUploader(item);
                    }

                    const firstUploader = document.querySelector(
                        '[data-image-index="1"]'
                    );

                    if (firstUploader) {
                        initialiseUploader(firstUploader);
                    }

                    addImageButton.addEventListener('click', function() {
                        if (imageCount >= maxImages) {
                            return;
                        }

                        imageCount++;

                        createUploader(imageCount);

                        if (imageCount >= maxImages) {
                            addImageButton.disabled = true;
                            addImageButton.innerHTML =
                                '<i class="fas fa-check"></i> Maximum 6 Images';
                        }
                    });
                });
            </script>


            <script>
                $(document).ready(function() {
                    $('#content').summernote({
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







            {{-- program/type/fac/dept start --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const programSelect = document.getElementById('program-select');
                    const programTypeSelect = document.getElementById('program-type-select');

                    if (!programSelect || !programTypeSelect) return;

                    const selectedProgramType =
                        "{{ old('program_type') }}";

                    function loadProgramTypes(programSlug, selected = '') {

                        if (!programSlug) {
                            programTypeSelect.innerHTML = '<option value="">- Select Program First -</option>';
                            programTypeSelect.disabled = true;
                            return;
                        }

                        programTypeSelect.disabled = true;
                        programTypeSelect.innerHTML = '<option>Loading...</option>';

                        fetch("{{ route('fetchProgramTypes') }}?program_slug=" + programSlug)
                            .then(res => res.json())
                            .then(res => {

                                programTypeSelect.innerHTML = '<option value="">- Select -</option>';

                                if (res.programTypes.length === 0) {

                                    programTypeSelect.innerHTML +=
                                        '<option disabled>No program types found</option>';

                                } else {

                                    res.programTypes.forEach(type => {

                                        programTypeSelect.innerHTML += `
                            <option value="${type.slug}"
                                ${selected === type.slug ? 'selected' : ''}>
                                ${type.type_name}
                            </option>`;
                                    });

                                }

                                programTypeSelect.disabled = false;

                            });
                    }

                    programSelect.addEventListener('change', function() {
                        loadProgramTypes(this.value);
                    });

                    // Edit Mode
                    if (programSelect.value) {
                        loadProgramTypes(programSelect.value, selectedProgramType);
                    }

                });
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const facultySelect = document.getElementById('faculty-select');
                    const departmentSelect = document.getElementById('department-select');

                    if (!facultySelect || !departmentSelect) return;

                    const selectedDepartment = "{{ old('department') }}";

                    function loadDepartments(facultySlug, selected = '') {

                        if (!facultySlug) {
                            departmentSelect.innerHTML = '<option value="">- Select Faculty First -</option>';
                            departmentSelect.disabled = true;
                            return;
                        }

                        departmentSelect.disabled = true;
                        departmentSelect.innerHTML = '<option>Loading...</option>';

                        fetch("{{ route('fetchDepartments') }}?faculty_slug=" + facultySlug)
                            .then(res => res.json())
                            .then(res => {

                                departmentSelect.innerHTML = '<option value="">- Select -</option>';

                                if (res.departments.length === 0) {

                                    departmentSelect.innerHTML +=
                                        '<option disabled>No departments found</option>';

                                } else {

                                    res.departments.forEach(department => {

                                        departmentSelect.innerHTML += `
                            <option value="${department.slug}"
                                ${selected === department.slug ? 'selected' : ''}>
                                ${department.name}
                            </option>`;
                                    });

                                }

                                departmentSelect.disabled = false;

                            });
                    }

                    facultySelect.addEventListener('change', function() {
                        loadDepartments(this.value);
                    });

                    // Edit Mode
                    if (facultySelect.value) {
                        loadDepartments(facultySelect.value, selectedDepartment);
                    }

                });
            </script>
            {{-- scripts end --}}

            {{-- scripts start --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const programSelect = document.getElementById('program-select');
                    const programTypeSelect = document.getElementById('program-type-select');

                    if (!programSelect || !programTypeSelect) return;

                    const selectedProgramType =
                        "{{ old('program_type') }}";

                    function loadProgramTypes(programSlug, selected = '') {

                        if (!programSlug) {
                            programTypeSelect.innerHTML = '<option value="">- Select Program First -</option>';
                            programTypeSelect.disabled = true;
                            return;
                        }

                        programTypeSelect.disabled = true;
                        programTypeSelect.innerHTML = '<option>Loading...</option>';

                        fetch("{{ route('fetchProgramTypes') }}?program_slug=" + programSlug)
                            .then(res => res.json())
                            .then(res => {

                                programTypeSelect.innerHTML = '<option value="">- Select -</option>';

                                if (res.programTypes.length === 0) {

                                    programTypeSelect.innerHTML +=
                                        '<option disabled>No program types found</option>';

                                } else {

                                    res.programTypes.forEach(type => {

                                        programTypeSelect.innerHTML += `
                            <option value="${type.slug}"
                                ${selected === type.slug ? 'selected' : ''}>
                                ${type.type_name}
                            </option>`;
                                    });

                                }

                                programTypeSelect.disabled = false;

                            });
                    }

                    programSelect.addEventListener('change', function() {
                        loadProgramTypes(this.value);
                    });

                    // Edit Mode
                    if (programSelect.value) {
                        loadProgramTypes(programSelect.value, selectedProgramType);
                    }

                });
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const facultySelect = document.getElementById('faculty-select');
                    const departmentSelect = document.getElementById('department-select');

                    if (!facultySelect || !departmentSelect) return;

                    const selectedDepartment = "{{ old('department') }}";

                    function loadDepartments(facultySlug, selected = '') {

                        if (!facultySlug) {
                            departmentSelect.innerHTML = '<option value="">- Select Faculty First -</option>';
                            departmentSelect.disabled = true;
                            return;
                        }

                        departmentSelect.disabled = true;
                        departmentSelect.innerHTML = '<option>Loading...</option>';

                        fetch("{{ route('fetchDepartments') }}?faculty_slug=" + facultySlug)
                            .then(res => res.json())
                            .then(res => {

                                departmentSelect.innerHTML = '<option value="">- Select -</option>';

                                if (res.departments.length === 0) {

                                    departmentSelect.innerHTML +=
                                        '<option disabled>No departments found</option>';

                                } else {

                                    res.departments.forEach(department => {

                                        departmentSelect.innerHTML += `
                            <option value="${department.slug}"
                                ${selected === department.slug ? 'selected' : ''}>
                                ${department.name}
                            </option>`;
                                    });

                                }

                                departmentSelect.disabled = false;

                            });
                    }

                    facultySelect.addEventListener('change', function() {
                        loadDepartments(this.value);
                    });

                    // Edit Mode
                    if (facultySelect.value) {
                        loadDepartments(facultySelect.value, selectedDepartment);
                    }

                });
            </script>
            {{-- program/type/fac/dept end --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
