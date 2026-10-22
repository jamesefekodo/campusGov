@extends('layouts.userLayout')
@section('title', 'Edit Past Question :: ' . $data['pastQuestion']->course_code . ' :: ' .
    $data['pastQuestion']->academic_year . '/' . $data['pastQuestion']->academic_year + 1)
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
                                        Edit Past Question ::
                                        {{ $data['pastQuestion']->course_code .
                                            ' :: ' .
                                            $data['pastQuestion']->academic_year .
                                            '/' .
                                            $data['pastQuestion']->academic_year +
                                            1 }}
                                    </h4>
                                    <div class="col-md-12 row px-4 py-1 g-2">
                                        <div class="col-md-12 p-1">
                                            <div class="card stretch-full shadow-sm border-0">
                                                <form class="form my_form" method="POST"
                                                    action="{{ route('updatePastQuestionSubmit', ['uuid' => $data['pastQuestion']->uuid]) }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
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
                                                                        {{ $data['pastQuestion']->program == $program->slug ? 'selected' : '' }}>
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
                                                                        {{ $data['pastQuestion']->faculty == $faculty->slug ? 'selected' : '' }}>
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
                                                                        {{ $data['pastQuestion']->academic_year == "$year" ? 'selected' : '' }}>
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
                                                                    {{ $data['pastQuestion']->level == '1' ? 'selected' : '' }}>
                                                                    Year 1
                                                                </option>
                                                                <option value="2"
                                                                    {{ $data['pastQuestion']->level == '2' ? 'selected' : '' }}>
                                                                    Year 2
                                                                </option>
                                                                <option value="3"
                                                                    {{ $data['pastQuestion']->level == '3' ? 'selected' : '' }}>
                                                                    Year 3
                                                                </option>
                                                                <option value="4"
                                                                    {{ $data['pastQuestion']->level == '4' ? 'selected' : '' }}>
                                                                    Year 4
                                                                </option>
                                                                <option value="5"
                                                                    {{ $data['pastQuestion']->level == '5' ? 'selected' : '' }}>
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
                                                                    {{ $data['pastQuestion']->semester == 'first' ? 'selected' : '' }}>
                                                                    First
                                                                </option>
                                                                <option value="second"
                                                                    {{ $data['pastQuestion']->semester == 'second' ? 'selected' : '' }}>
                                                                    Second
                                                                </option>
                                                                <option value="third"
                                                                    {{ $data['pastQuestion']->semester == 'third' ? 'selected' : '' }}>
                                                                    Third
                                                                </option>
                                                                <option value="fourth"
                                                                    {{ old('semester') == 'fourth' ? 'selected' : '' }}>
                                                                    Fourth
                                                                </option>
                                                                <option value="fifth"
                                                                    {{ $data['pastQuestion']->semester == 'fifth' ? 'selected' : '' }}>
                                                                    Fifth
                                                                </option>
                                                                <option value="sixth"
                                                                    {{ $data['pastQuestion']->semester == 'sixth' ? 'selected' : '' }}>
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
                                                                    {{ $data['pastQuestion']->is_exam == '1' ? 'selected' : '' }}>
                                                                    True
                                                                </option>
                                                                <option value="0"
                                                                    {{ $data['pastQuestion']->is_exam == '0' ? 'selected' : '' }}>
                                                                    False
                                                                </option>
                                                            </select>
                                                            <p style="color: red">{{ $errors->first('course_code') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label for="course_code">Course Code: <span
                                                                    class="text-red">*</span></label>
                                                            <input type="text" class="form-control" name="course_code"
                                                                value="{{ $data['pastQuestion']->course_code }}" required>
                                                            <p style="color: red">{{ $errors->first('course_code') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="course_title">Course Title: <span
                                                                    class="text-red">*</span></label>
                                                            <input type="text" class="form-control"
                                                                name="course_title"
                                                                value="{{ $data['pastQuestion']->course_title }}"
                                                                required>
                                                            <p style="color: red">{{ $errors->first('course_title') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label for="content">Questions (text):</label>
                                                            <textarea id="content" name="content">{{ $data['pastQuestion']->content }}</textarea>
                                                            <p style="color: red">
                                                                {{ $errors->first('content') }}</p>
                                                        </div>

                                                        @php
                                                            $imagePlaceholder = asset(
                                                                'system/images/wide_img_placeholder.jpeg',
                                                            );
                                                            $pastQuestion = $data['pastQuestion'];
                                                        @endphp

                                                        <div class="form-group col-md-12">
                                                            <label>
                                                                Question Pages
                                                                <span class="text-muted">
                                                                    (Optional, maximum 6 images)
                                                                </span>
                                                            </label>

                                                            <div id="imageUploadList" class="row">

                                                                @for ($index = 1; $index <= 6; $index++)
                                                                    @php
                                                                        $column = 'image_' . $index;
                                                                        $existingImage = $pastQuestion->{$column};

                                                                        $existingImageUrl = $existingImage
                                                                            ? asset(ltrim($existingImage, '/'))
                                                                            : $imagePlaceholder;
                                                                    @endphp

                                                                    <div class="image-upload-item col-md-6 mb-3"
                                                                        data-image-index="{{ $index }}"
                                                                        data-existing-image="{{ $existingImage ? 'true' : 'false' }}">

                                                                        <label for="image_{{ $index }}">
                                                                            Page {{ $index }}:
                                                                        </label>

                                                                        <div class="smart-image-upload {{ $existingImage ? 'has-existing-image' : '' }}"
                                                                            id="imageUploadBox_{{ $index }}">

                                                                            <input type="file"
                                                                                name="image_{{ $index }}"
                                                                                id="image_{{ $index }}"
                                                                                class="image-file-input"
                                                                                accept="image/jpeg,image/png,image/webp"
                                                                                hidden>

                                                                            <input type="hidden"
                                                                                name="remove_image_{{ $index }}"
                                                                                class="remove-image-input" value="0">

                                                                            <div class="image-preview-wrapper">
                                                                                <img src="{{ $existingImageUrl }}"
                                                                                    class="image-preview"
                                                                                    alt="Page {{ $index }} preview">

                                                                                <div class="image-upload-overlay">
                                                                                    <div>
                                                                                        <i
                                                                                            class="fas fa-cloud-upload-alt fa-2x mb-2"></i>

                                                                                        <div class="fw-bold">
                                                                                            {{ $existingImage ? 'Click to replace image' : 'Click to upload image' }}
                                                                                        </div>

                                                                                        <small>
                                                                                            JPG, PNG or WEBP · Maximum 2MB
                                                                                        </small>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="image-upload-info">
                                                                                <span class="image-file-name">
                                                                                    @if ($existingImage)
                                                                                        Existing image
                                                                                    @else
                                                                                        No image selected
                                                                                    @endif
                                                                                </span>

                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-outline-danger remove-image">

                                                                                    <i class="fas fa-times"></i>
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                        <p class="text-danger image-error"></p>
                                                                    </div>
                                                                @endfor

                                                            </div>

                                                            <small class="d-block text-muted mt-2">
                                                                Existing images are shown automatically. You can replace,
                                                                remove,
                                                                or add images. Maximum 6 images.
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
                        <a class="btn btn-success"
                            href="{{ route('viewPastQuestion', ['uuid' => $data['pastQuestion']->uuid]) }}">

                            <i class="fas fa-chevron-left"></i>
                            Back
                        </a>
                    </div>
                </div>
            </div>


            <style>
                .image-upload-item {
                    animation: imageItemIn .25s ease both;
                }

                .smart-image-upload {
                    overflow: hidden;
                    border: 1px solid #dfe4e8;
                    border-radius: 12px;
                    background: #fff;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
                    transition:
                        border-color .2s ease,
                        box-shadow .2s ease,
                        transform .2s ease;
                }

                .smart-image-upload:hover {
                    border-color: #9aa9b4;
                    box-shadow: 0 8px 22px rgba(0, 0, 0, .08);
                }

                .smart-image-upload.dragging {
                    border: 2px dashed #0d6efd;
                    background: #eef6ff;
                    box-shadow: 0 0 0 4px rgba(13, 110, 253, .10);
                }

                .image-preview-wrapper {
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 260px;
                    overflow: hidden;
                    cursor: pointer;
                    background:
                        linear-gradient(135deg, #f8fafb 25%, transparent 25%) -10px 0/20px 20px,
                        linear-gradient(225deg, #f8fafb 25%, transparent 25%) -10px 0/20px 20px,
                        linear-gradient(315deg, #f8fafb 25%, transparent 25%) 0 0/20px 20px,
                        linear-gradient(45deg, #f8fafb 25%, #f4f6f8 25%) 0 0/20px 20px;
                }

                .image-preview {
                    display: block;
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    background: transparent;
                    transition:
                        opacity .2s ease,
                        filter .2s ease,
                        transform .25s ease;
                }

                .smart-image-upload:hover .image-preview {
                    transform: scale(1.015);
                }

                .image-upload-overlay {
                    position: absolute;
                    inset: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                    background: rgba(7, 15, 20, .58);
                    color: #fff;
                    text-align: center;
                    opacity: 0;
                    transition:
                        opacity .2s ease,
                        background .2s ease;
                }

                .image-preview-wrapper:hover .image-upload-overlay {
                    opacity: 1;
                }

                .image-upload-overlay i {
                    color: #8ed9ff;
                }

                .image-upload-overlay small {
                    display: block;
                    margin-top: 5px;
                    color: rgba(255, 255, 255, .75);
                    font-size: 11px;
                }

                .image-upload-info {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    min-height: 56px;
                    padding: 10px 13px;
                    background: #fff;
                    color: #65757d;
                    font-size: 13px;
                }

                .image-file-name {
                    display: block;
                    min-width: 0;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }

                .image-upload-info .remove-image {
                    flex-shrink: 0;
                    white-space: nowrap;
                }

                /*
                 * Existing image state
                 */
                .smart-image-upload.has-existing-image {
                    border-color: #b8d8c9;
                }

                .smart-image-upload.has-existing-image::before {
                    position: absolute;
                    z-index: 5;
                    top: 9px;
                    left: 9px;
                    padding: 4px 8px;
                    border-radius: 999px;
                    background: rgba(25, 135, 84, .90);
                    color: #fff;
                    content: "Existing image";
                    font-size: 10px;
                    pointer-events: none;
                }

                /*
                 * Removed image state
                 */
                .image-upload-item.is-removed .smart-image-upload {
                    border-color: #dc3545;
                }

                .image-upload-item.is-removed .image-preview {
                    opacity: .28;
                    filter: grayscale(1);
                }

                .image-upload-item.is-removed .image-upload-overlay {
                    opacity: 1;
                    background: rgba(145, 18, 30, .78);
                }

                .image-upload-item.is-removed .image-upload-overlay::after {
                    display: block;
                    margin-top: 7px;
                    color: #fff;
                    content: "Marked for removal";
                    font-size: 12px;
                    font-weight: 700;
                }

                .image-upload-item.is-removed .image-upload-info {
                    background: #fff5f5;
                }

                /*
                 * Empty slot state
                 */
                .image-upload-item[data-existing-image="false"] .image-preview-wrapper {
                    background-color: #f8fafb;
                }

                .image-upload-item[data-existing-image="false"] .image-preview {
                    opacity: .72;
                }

                /*
                 * Error message
                 */
                .image-error {
                    min-height: 20px;
                    margin: 5px 0 0;
                    font-size: 12px;
                }

                /*
                 * Animation
                 */
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

                /*
                 * Tablet and mobile
                 */
                @media (max-width: 991px) {
                    .image-preview-wrapper {
                        height: 230px;
                    }
                }

                @media (max-width: 767px) {
                    .image-upload-item {
                        width: 100%;
                    }

                    .image-preview-wrapper {
                        height: 260px;
                    }
                }

                @media (max-width: 420px) {
                    .image-preview-wrapper {
                        height: 220px;
                    }

                    .image-upload-info {
                        align-items: flex-start;
                        flex-direction: column;
                        gap: 8px;
                    }

                    .image-upload-info .remove-image {
                        width: 100%;
                    }
                }

                /*
                 * Accessibility / reduced motion
                 */
                @media (prefers-reduced-motion: reduce) {

                    .image-upload-item,
                    .image-preview,
                    .smart-image-upload,
                    .image-upload-overlay {
                        animation: none;
                        transition: none;
                    }
                }
            </style>



            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const imageItems = document.querySelectorAll(
                        '.image-upload-item'
                    );

                    const placeholder = @json($imagePlaceholder);

                    const maxFileSize = 2 * 1024 * 1024;

                    const allowedTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ];

                    imageItems.forEach(function(item) {
                        initialiseUploader(item);
                    });

                    function initialiseUploader(item) {
                        const uploadBox = item.querySelector(
                            '.smart-image-upload'
                        );

                        const fileInput = item.querySelector(
                            '.image-file-input'
                        );

                        const preview = item.querySelector(
                            '.image-preview'
                        );

                        const fileName = item.querySelector(
                            '.image-file-name'
                        );

                        const removeButton = item.querySelector(
                            '.remove-image'
                        );

                        const removeImageInput = item.querySelector(
                            '.remove-image-input'
                        );

                        const errorMessage = item.querySelector(
                            '.image-error'
                        );

                        const hasExistingImage =
                            item.dataset.existingImage === 'true';

                        const originalImageUrl = preview.getAttribute('src');

                        let currentState = hasExistingImage ?
                            'existing' :
                            'empty';

                        uploadBox.addEventListener('click', function(event) {
                            if (event.target.closest('.remove-image')) {
                                return;
                            }

                            fileInput.click();
                        });

                        fileInput.addEventListener('change', function() {
                            const file = this.files && this.files[0];

                            if (!file) {
                                return;
                            }

                            showPreview(file);
                        });

                        removeButton.addEventListener('click', function(event) {
                            event.preventDefault();
                            event.stopPropagation();

                            /*
                             * Existing image:
                             * - first click removes it
                             * - second click restores it
                             */
                            if (hasExistingImage) {
                                if (currentState !== 'removed') {
                                    markExistingImageForRemoval();
                                } else {
                                    restoreExistingImage();
                                }

                                return;
                            }

                            /*
                             * Empty slot or newly selected replacement.
                             */
                            resetEmptySlot();
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
                            } catch (error) {
                                /*
                                 * Some older browsers do not allow assigning
                                 * dropped files to the input. Preview still works.
                                 */
                            }

                            showPreview(file);
                        });

                        function showPreview(file) {
                            errorMessage.textContent = '';

                            if (!allowedTypes.includes(file.type)) {
                                errorMessage.textContent =
                                    'Please select a JPG, PNG or WEBP image.';

                                fileInput.value = '';

                                return;
                            }

                            if (file.size > maxFileSize) {
                                errorMessage.textContent =
                                    'Image size must not exceed 2MB.';

                                fileInput.value = '';

                                return;
                            }

                            const reader = new FileReader();

                            reader.onload = function(event) {
                                preview.src = event.target.result;
                                fileName.textContent = file.name;

                                /*
                                 * A new file replaces any old file.
                                 * The controller will delete the old physical file.
                                 */
                                removeImageInput.value = '1';

                                currentState = 'replacement';

                                item.classList.remove('is-removed');

                                removeButton.innerHTML =
                                    '<i class="fas fa-times"></i> Remove';

                                removeButton.classList.remove(
                                    'btn-outline-success'
                                );

                                removeButton.classList.add(
                                    'btn-outline-danger'
                                );
                            };

                            reader.onerror = function() {
                                errorMessage.textContent =
                                    'Unable to preview this image.';
                            };

                            reader.readAsDataURL(file);
                        }

                        function markExistingImageForRemoval() {
                            fileInput.value = '';

                            removeImageInput.value = '1';

                            preview.src = placeholder;
                            fileName.textContent = 'Image marked for removal';

                            item.classList.add('is-removed');

                            currentState = 'removed';

                            removeButton.innerHTML =
                                '<i class="fas fa-undo"></i> Undo';

                            removeButton.classList.remove(
                                'btn-outline-danger'
                            );

                            removeButton.classList.add(
                                'btn-outline-success'
                            );
                        }

                        function restoreExistingImage() {
                            fileInput.value = '';

                            removeImageInput.value = '0';

                            preview.src = originalImageUrl;
                            fileName.textContent = 'Existing image';

                            item.classList.remove('is-removed');

                            currentState = 'existing';

                            removeButton.innerHTML =
                                '<i class="fas fa-times"></i> Remove';

                            removeButton.classList.remove(
                                'btn-outline-success'
                            );

                            removeButton.classList.add(
                                'btn-outline-danger'
                            );
                        }

                        function resetEmptySlot() {
                            fileInput.value = '';

                            removeImageInput.value = '0';

                            preview.src = placeholder;
                            fileName.textContent = 'No image selected';

                            item.classList.remove('is-removed');

                            currentState = 'empty';

                            removeButton.innerHTML =
                                '<i class="fas fa-times"></i> Remove';

                            removeButton.classList.remove(
                                'btn-outline-success'
                            );

                            removeButton.classList.add(
                                'btn-outline-danger'
                            );
                        }
                    }
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
                        "{{ $data['pastQuestion']->program_type ?? old('program_type') }}";

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

                    const selectedDepartment = "{{ $data['pastQuestion']->department ?? old('department') }}";

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
