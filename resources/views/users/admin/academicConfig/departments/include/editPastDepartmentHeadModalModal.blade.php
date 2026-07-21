<div class="modal fade" id="editPastDepartmentHeadModalModal_{{ $head->uuid }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Update Department Head's Records</h4>
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
                        action="{{ route('updatePastDepartmentHeadProfileSubmit', ['uuid' => $head->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <input type="hidden" class="form-control" name="department"
                                value="{{ $data['department']->uuid }}" required>
                            <p style="color: red">{{ $errors->first('department') }}</p>


                            <div class="form-group col-md-4">
                                <label for="name">Name:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $head->name }}"
                                    required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="qualifications">Qualifications:</label>
                                <input type="text" class="form-control" name="qualifications"
                                    value="{{ $head->qualifications }}">
                                <p style="color: red">{{ $errors->first('qualifications') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="assumed_office_on">Assumed Office On <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="assumed_office_on"
                                    value="{{ $head->assumed_office_on }}" required>
                                <p style="color: red">{{ $errors->first('assumed_office_on') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="tenure_end_on">Tenure ends on <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tenure_end_on"
                                    value="{{ $head->tenure_end_on }}" required>
                                <p style="color: red">{{ $errors->first('tenure_end_on') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="phone">Phone:</label>
                                <input type="text" class="form-control" name="phone" value="{{ $head->phone }}">
                                <p style="color: red">{{ $errors->first('phone') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" name="email" value="{{ $head->email }}">
                                <p style="color: red">{{ $errors->first('email') }}</p>
                            </div>
                            @php
                                $image = asset('system/images/hod_default_pic.webp');
                                if ($head->photo && $head->photo) {
                                    $image = asset('assets/uploads/department/head/photo/' . $head->photo);
                                }
                            @endphp
                            <div class="form-group upload-container row">
                                <div class="col-md-6 d-flex justify-content-center align-items-center">
                                    <label for="photo-upload" class="preview-frame">
                                        <img id="image-preview" src="{{ $image }}" alt="image" width="200px"
                                            height="200px">
                                    </label>
                                </div>


                                <div class="col-md-6">
                                    <label for="photo">Photo</label>
                                    <input type="file" id="photo-upload" accept=".jpg, .jpeg, .png, .webp"
                                        onchange="previewImage()" name="photo">
                                    <p style="color: red">
                                        {{ $errors->first('photo') }}</p>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="profile">Profile:<span class="text-red">*</span></label>
                                <textarea id="profile_{{ $head->uuid }}" name="profile" value="{{ old('profile') }}" required>
                                    {{ $head->profile }}
                                </textarea>
                                <p style="color: red">
                                    {{ $errors->first('profile') }}</p>
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



            <script>
                $(document).ready(function() {
                    $('#profile_{{ $head->uuid }}').summernote({
                        height: 200,
                        toolbar: [
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
                function previewImage() {
                    const preview = document.getElementById('image-preview');
                    const fileInput = document.getElementById('photo-upload');
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

        </div>
    </div>
</div>
