<div class="modal fade" id="editPlatformModal_{{ $platform->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Role</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('editRecognizedSocialMediaPlatform', ['slug' => $platform->slug]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group upload-container">
                            @php
                                $image = asset('system/images/logo_placeholder.png');
                                if (
                                    $platform->logo &&
                                    file_exists(
                                        public_path(
                                            'assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo,
                                        ),
                                    )
                                ) {
                                    $image = asset(
                                        'assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo,
                                    );
                                }
                            @endphp
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6"><label for="passport-upload" class="preview-frame">
                                        <img id="passport-preview"
                                            src="{{ $image }}" alt="">
                                        <div class="overlay"><i class="fas fa-upload"></i></div>
                                    </label>
                                </div>
                                <div class="col-md-6"><input type="file" id="passport-upload"
                                        accept=".jpg, .jpeg, .png, .webp" onchange="previewPassport()" name="logo">
                                </div>
                                <p class="text-red">{{ $errors->first('logo') }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="name">Platform Name:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $platform->name }}"
                                    required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>
                             <div class="form-group col-md-4">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $platform->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $platform->is_active == '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                <p style="color: red">{{ $errors->first('is_active') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ $platform->description }}" required>
                                <p style="color: red">{{ $errors->first('description') }}</p>
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

                    {{-- Preview image --}}
                    <script>
                        function previewPassport() {
                            const preview = document.getElementById('passport-preview');
                            const fileInput = document.getElementById('passport-upload');
                            const file = fileInput.files[0];
                            const reader = new FileReader();

                            reader.onload = function() {
                                preview.src = reader.result;
                            }

                            if (file) {
                                reader.readAsDataURL(file);
                            } else {
                                preview.src = 'placeholder.jpg';
                            }
                        }
                    </script>


                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>
