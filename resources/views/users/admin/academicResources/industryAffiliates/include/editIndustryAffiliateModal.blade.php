<style>
    .smart-image-upload {
        width: 100%;
        cursor: pointer;
    }

    .image-preview-wrapper {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 7;
        min-height: 220px;
        overflow: hidden;
        border: 2px dashed #ced4da;
        border-radius: 12px;
        background: #f8f9fa;
        transition: all .2s ease;
    }

    .image-preview-wrapper:hover {
        border-color: #198754;
    }

    .image-preview {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        background: #f8f9fa;
    }

    .image-upload-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        background: rgba(0, 0, 0, .35);
        opacity: 0;
        transition: opacity .2s ease;
    }

    .image-preview-wrapper:hover .image-upload-overlay {
        opacity: 1;
    }

    .image-upload-overlay i {
        display: block;
    }

    .image-upload-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
        padding: 8px 10px;
        background: #f8f9fa;
        border-radius: 7px;
        font-size: 13px;
    }

    #imageFileName {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .smart-image-upload.dragging .image-preview-wrapper {
        border-color: #198754;
        background: #e9f7ef;
    }
</style>

<style>
    .smart-tags-wrapper {
        position: relative;
        /* Acts as the anchor for the absolute dropdown */
    }

    .smart-tags-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        padding: 6px 12px !important;
        cursor: text;
        min-height: 42px;
    }

    .tag-pills-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .tag-pill {
        background-color: #f1f3f5;
        color: #333;
        padding: 2px 8px 2px 10px;
        border-radius: 4px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #dee2e6;
    }

    .tag-pill .remove-tag {
        cursor: pointer;
        font-size: 0.75rem;
        color: #6c757d;
    }

    .tag-pill .remove-tag:hover {
        color: #dc3545;
    }

    .tags-text-input {
        flex-grow: 1;
        min-width: 120px;
        outline: none;
        font-size: 0.95rem;
        background: transparent;
    }

    .smart-tags-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 0 0 6px 6px;
        max-height: 180px;
        overflow-y: auto;
        z-index: 9999;
        /* Ensures it floats above card boundaries and other inputs */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .tag-suggestion-item {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 0.9rem;
        border-bottom: 1px solid #f8f9fa;
    }

    .tag-suggestion-item:hover {
        background-color: #f1f3f5;
        color: #0d6efd;
    }
</style>

<div class="modal fade" id="editIndustryAffiliateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Industry Affiliate</h4>
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
                    action="{{ route('updateIndustryAffiliateSubmit', ['slug' => $data['affiliate']->slug]) }}"
                     enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="title">Title: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="title"
                                    value="{{ $data['affiliate']->title }}" required>
                                <p style="color: red">{{ $errors->first('title') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="is_published">Status:<span class="text-red">*</span></label>
                                <select class="form-select" name="is_published" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $data['affiliate']->is_published == '1' ? 'selected' : '' }}>
                                        Published</option>
                                    <option value="0" {{ $data['affiliate']->is_published == '0' ? 'selected' : '' }}>
                                        Not-Published</option>
                                </select>
                                <p style="color: red">
                                    {{ $errors->first('is_published') }}</p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="image">
                                    Cover Image:
                                </label>

                                <div class="smart-image-upload" id="imageUploadBox">

                                    <input type="file" name="image" id="image" name="cover_image"
                                        accept="image/jpeg,image/png,image/webp" hidden>


                                    @php
                                        $coverImageName = $data['affiliate']->cover_image ?? null;

                                        // Check if the physical file exists using public_path
                                        $coverPath = $coverImageName
                                            ? public_path(
                                                'assets/tenant_' .
                                                    tenant()->id .
                                                    '/uploads/academicResources/industryAffilate/' .
                                                    $coverImageName,
                                            )
                                            : null;

                                        $hasCoverImage = $coverPath && file_exists($coverPath);

                                        // Assign the proper asset URL based on whether the file exists
                                        if ($hasCoverImage) {
                                            $coverImage = asset(
                                                'assets/tenant_' .
                                                    tenant()->id .
                                                    '/uploads/academicResources/industryAffilate/' .
                                                    $coverImageName,
                                            );
                                        } else {
                                            $coverImage = asset('system/images/wide_img_placeholder.jpeg');
                                        }
                                    @endphp


                                    <div class="image-preview-wrapper" id="imagePreviewWrapper">
                                        <img src="{{ $coverImage }}" id="imagePreview" class="image-preview"
                                            alt="Image preview">

                                        <div class="image-upload-overlay">
                                            <div>
                                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                                <div class="fw-bold">Click to upload image</div>
                                                <small>JPG, PNG or WEBP</small>
                                            </div>
                                        </div>
                                    </div>
                                    <p style="color: red">{{ $errors->first('cover_image') }}</p>


                                    <div class="image-upload-info">
                                        <span id="imageFileName">No image selected</span>
                                        <button type="button" class="btn btn-sm btn-outline-danger d-none"
                                            id="removeImage">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </div>

                                </div>

                                <p style="color: red">{{ $errors->first('image') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description:<span class="text-red">*</span></label>
                                <textarea id="description" name="description" required>
                                    {{ $data['affiliate']->description }}
                                </textarea>
                                <p style="color: red">
                                    {{ $errors->first('description') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="tags_input">Tags: <span class="text-red">*</span></label>

                                <!-- Smart Tags Wrapper -->
                                <div class="smart-tags-wrapper position-relative">
                                    <div class="smart-tags-container form-control h-auto" id="smartTagsContainer">
                                        <div class="tag-pills-list" id="tagPillsList"></div>
                                        <input type="text" id="tagsInput"
                                            class="tags-text-input border-0 p-0 shadow-none"
                                            placeholder="Type or select tags...">
                                    </div>

                                    <!-- Suggestions Dropdown -->
                                    <div class="smart-tags-dropdown d-none" id="tagsDropdown">
                                        @foreach ($data['industryTags'] as $tag)
                                            <div class="tag-suggestion-item" data-tag-name="{{ $tag->name }}">
                                                {{ $tag->name }}</div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Preloads existing publication tags for edit, or old input on validation error -->
                                @php
                                    $record = $data['affiliate'] ?? ($recommendedBook ?? null);
                                    $existingTags =
                                        $record && $record->relationLoaded('tags')
                                            ? $record->tags->pluck('name')->implode(',')
                                            : ($record
                                                ? $record->tags->pluck('name')->implode(',')
                                                : '');
                                @endphp

                                <input type="hidden" name="tags" id="tagsHiddenInput"
                                    value="{{ old('tags', $existingTags) }}" required>

                                <small class="text-muted">Type a tag and press comma (<strong>,</strong>) or Enter, or
                                    select from the list.</small>
                                <p style="color: red">{{ $errors->first('tags') }}</p>
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



<script>
    document.addEventListener('DOMContentLoaded', function() {

        const uploadBox = document.getElementById('imageUploadBox');
        const fileInput = document.getElementById('image');
        const preview = document.getElementById('imagePreview');
        const fileName = document.getElementById('imageFileName');
        const removeButton = document.getElementById('removeImage');

        const placeholder = "{{ asset('system/images/slider_placeholder.webp') }}";

        // Click anywhere on upload area
        uploadBox.addEventListener('click', function(e) {

            // Don't open file picker when clicking Remove
            if (e.target.closest('#removeImage')) {
                return;
            }

            fileInput.click();
        });


        // File selected
        fileInput.addEventListener('change', function() {

            if (this.files && this.files.length > 0) {
                showImage(this.files[0]);
            }
        });


        function showImage(file) {

            // Validate file type
            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!allowedTypes.includes(file.type)) {
                alert('Please select a JPG, PNG or WEBP image.');
                resetImage();
                return;
            }


            // Validate size - 2MB
            if (file.size > 2 * 1024 * 1024) {
                alert('Image size must not exceed 2MB.');
                resetImage();
                return;
            }


            const reader = new FileReader();

            reader.onload = function(e) {

                preview.src = e.target.result;

                fileName.textContent = file.name;

                removeButton.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        }


        // Remove selected image
        removeButton.addEventListener('click', function(e) {

            e.stopPropagation();

            resetImage();
        });


        function resetImage() {

            fileInput.value = '';

            preview.src = placeholder;

            fileName.textContent = 'No image selected';

            removeButton.classList.add('d-none');
        }


        // Drag & Drop
        uploadBox.addEventListener('dragover', function(e) {

            e.preventDefault();

            uploadBox.classList.add('dragging');
        });


        uploadBox.addEventListener('dragleave', function() {

            uploadBox.classList.remove('dragging');
        });


        uploadBox.addEventListener('drop', function(e) {

            e.preventDefault();

            uploadBox.classList.remove('dragging');

            const files = e.dataTransfer.files;

            if (files.length > 0) {

                const file = files[0];

                // Put dropped file into input
                const dataTransfer = new DataTransfer();

                dataTransfer.items.add(file);

                fileInput.files = dataTransfer.files;

                showImage(file);
            }
        });

    });
</script>

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



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.querySelector('.smart-tags-wrapper');
        const container = document.getElementById('smartTagsContainer');
        const tagsInput = document.getElementById('tagsInput');
        const pillsList = document.getElementById('tagPillsList');
        const hiddenInput = document.getElementById('tagsHiddenInput');
        const dropdown = document.getElementById('tagsDropdown');
        const suggestionItems = dropdown.querySelectorAll('.tag-suggestion-item');

        let tags = [];

        // Initialize from preloaded hidden input value (Edit mode or Old validation input)
        if (hiddenInput.value) {
            tags = hiddenInput.value.split(',').map(t => t.trim()).filter(t => t.length > 0);
            renderPills();
        }

        // Focus text box when container is clicked
        container.addEventListener('click', () => tagsInput.focus());

        // Show dropdown on focus or typing
        tagsInput.addEventListener('focus', filterAndShowDropdown);
        tagsInput.addEventListener('input', filterAndShowDropdown);

        function filterAndShowDropdown() {
            const query = tagsInput.value.trim().toLowerCase();
            let visibleCount = 0;

            suggestionItems.forEach(item => {
                const text = item.getAttribute('data-tag-name').toLowerCase();
                if (text.includes(query) && !tags.includes(item.getAttribute('data-tag-name'))) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (visibleCount > 0) {
                dropdown.classList.remove('d-none');
            } else {
                dropdown.classList.add('d-none');
            }
        }

        // Handle typing delimiters (comma or enter)
        tagsInput.addEventListener('keydown', function(e) {
            if (e.key === ',' || e.key === 'Enter') {
                e.preventDefault();
                addTag(tagsInput.value);
            } else if (e.key === 'Backspace' && tagsInput.value === '' && tags.length > 0) {
                tags.pop();
                updateAndRender();
                filterAndShowDropdown();
            }
        });

        // Click suggestion item to add
        suggestionItems.forEach(item => {
            item.addEventListener('mousedown', function(e) {
                e.preventDefault();
                addTag(this.getAttribute('data-tag-name'));
                dropdown.classList.add('d-none');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                dropdown.classList.add('d-none');
            }
        });

        function addTag(text) {
            const cleaned = text.replace(/,/g, '').trim();
            if (cleaned && !tags.includes(cleaned)) {
                tags.push(cleaned);
                updateAndRender();
            }
            tagsInput.value = '';
            dropdown.classList.add('d-none');
        }

        window.removeTag = function(index) {
            tags.splice(index, 1);
            updateAndRender();
            if (document.activeElement === tagsInput) {
                filterAndShowDropdown();
            }
        };

        function updateAndRender() {
            hiddenInput.value = tags.join(',');
            renderPills();
        }

        function renderPills() {
            pillsList.innerHTML = '';
            tags.forEach((tag, index) => {
                const pill = document.createElement('div');
                pill.className = 'tag-pill';
                pill.innerHTML = `
                    <span>${tag}</span>
                    <span class="remove-tag" onclick="removeTag(${index})"><i class="fas fa-times"></i></span>
                `;
                pillsList.appendChild(pill);
            });
        }
    });
</script>
