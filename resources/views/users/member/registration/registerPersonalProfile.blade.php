@extends('layouts.miscellaneousLayout')

@section('title', 'Personal Profile')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="user_page_title">Account Setup</div>
        <div class="container-fluid">
            <div class="">

                <div class="individual-review-card-container row">
                    <div class="col-md-3 individual-review-card text-black">
                        @include('users.member.registration.include.sideBar')
                    </div>
                    <div class="col-md-9 individual-review-card">
                        <div class="col-md-12 m-auto">
                            <div class="my_form_container">

                                <h4 class="block text-center py-2 pt-3 text-uppercase type-b-page-header">
                                    Register Personal Profile
                                </h4>
                                <div class="card-body">
                                    <form class="form my_form" method="POST"
                                        action="{{ route('memberSelfCreatePersonalProfileSubmit') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="row d-flex justify-content-center">
                                            @php
                                                $imagePlaceholder = asset('system/images/default_profile_pic.jpg');

                                                if ($data['member']->photo) {
                                                    $imagePlaceholder = asset(
                                                        'assets/uploads/users/members/photo/' . $data['member']->photo,
                                                    );
                                                }
                                            @endphp

                                            {{-- Preview --}}
                                            <div class="col-md-12 text-center mb-3">
                                                <img id="passport-preview" src="{{ $imagePlaceholder }}" alt="Profile Photo"
                                                    class="rounded-circle"
                                                    style="width:180px; height:180px; object-fit:cover; border: 3px solid #e3e6f0;">
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div class="col-md-12 text-center mb-3 d-flex justify-content-center gap-2">
                                                {{-- Upload --}}
                                                <label for="passport-upload" class="btn btn-sm btn-outline-primary mb-0">
                                                    <i class="fa fa-upload me-1"></i> Upload Photo
                                                </label>
                                                <input type="file" id="passport-upload" accept=".jpg,.jpeg,.png,.webp"
                                                    class="d-none">

                                                {{-- Camera --}}
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    id="open-camera-btn">
                                                    <i class="fa fa-camera me-1"></i> Take Photo
                                                </button>
                                            </div>

                                            {{-- Camera Section --}}
                                            <div class="col-md-12" id="camera-section" style="display:none;">
                                                <div class="text-center mb-2">
                                                    <video id="camera-stream" autoplay playsinline
                                                        style="width:100%; max-width:600px; border-radius:8px; border:2px solid #e3e6f0;"></video>
                                                </div>
                                                <div class="text-center d-flex justify-content-center gap-2 mb-3">
                                                    <button type="button" class="btn btn-sm btn-success" id="snap-btn">
                                                        <i class="fa fa-circle me-1"></i> Snap
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        id="close-camera-btn">
                                                        <i class="fa fa-times me-1"></i> Cancel
                                                    </button>
                                                </div>
                                                <canvas id="snap-canvas" style="display:none;"></canvas>
                                            </div>

                                            {{-- Crop Modal --}}
                                            <div class="modal fade" id="cropModal" tabindex="-1" data-bs-backdrop="static">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        {{-- <div class="modal-header">
                                                            <h6 class="modal-title">Crop Photo
                                                            </h6>
                                                        </div> --}}
                                                        <div
                                                            class="modal-body text-center p-0 d-flex align-items-center justify-content-center mt-3">
                                                            <img id="crop-image" src="" alt="Crop"
                                                                style="max-width:100%;" class="img-fluid"
                                                                class="w-auto mw-100 mh-100">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                id="cancel-crop-btn">
                                                                Cancel
                                                            </button>
                                                            <button type="button" class="btn btn-primary btn-sm"
                                                                id="apply-crop-btn">
                                                                <i class="fa fa-check me-1"></i>
                                                                Apply Crop
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Hidden input for cropped base64 image --}}
                                            <input type="hidden" name="photo_cropped" id="photo-cropped">

                                            <p class="text-red text-center">{{ $errors->first('photo_cropped') }}</p>
                                            <p class="text-red text-center">{{ $errors->first('photo') }}</p>

                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="first_name">First Name<span class="text-red">*</span>:</label>
                                                <input type="text" class="form-control text-capitalize" name="first_name"
                                                    value="{{ old('first_name', $data['member']->memberPersonalProfile?->first_name) }}"
                                                    required>
                                                <p style="color: red">{{ $errors->first('first_name') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="middle_name">Middle Name:</label>
                                                <input type="text" class="form-control text-capitalize"
                                                    name="middle_name"
                                                    value="{{ old('middle_name', $data['member']->memberPersonalProfile?->middle_name) }}">
                                                <p style="color: red">{{ $errors->first('middle_name') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="last_name">Last Name<span class="text-red">*</span>:</label>
                                                <input type="text" class="form-control text-capitalize" name="last_name"
                                                    value="{{ old('last_name', $data['member']->memberPersonalProfile?->last_name) }}"
                                                    required>
                                                <p style="color: red">{{ $errors->first('last_name') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="gender">Gender
                                                    <span class="text-red">*</span></label>
                                                <select class="form-select" name="gender" required>
                                                    <option selected disabled>
                                                        -Select-</option>
                                                    <option value="male"
                                                        {{ old('gender', $data['member']->memberPersonalProfile?->gender) == 'male' ? 'selected' : '' }}>
                                                        Male
                                                    </option>
                                                    <option value="female"
                                                        {{ old('gender', $data['member']->memberPersonalProfile?->gender) == 'female' ? 'selected' : '' }}>
                                                        Female
                                                    </option>
                                                </select>
                                                <p style="color: red">
                                                    {{ $errors->first('gender') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="email1">Email 1<span class="text-red">*</span>:</label>
                                                <input type="email" class="form-control" name="email1"
                                                    value="{{ old('email1', $data['member']->email) }}" disabled>
                                                <p style="color: red">{{ $errors->first('email1') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="phone1">Phone 1<span class="text-red">*</span>:</label>
                                                <input type="text" class="form-control" name="phone1"
                                                    value="{{ old('phone1', $data['member']->phone) }}" disabled>
                                                <p style="color: red">{{ $errors->first('phone1') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="email2">Email 2:</label>
                                                <input type="email" class="form-control" name="email2"
                                                    value="{{ old('email2', $data['member']->memberPersonalProfile?->email2) }}">
                                                <p style="color: red">{{ $errors->first('email2') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="phone2">Phone 2:</label>
                                                <input type="text" class="form-control" name="phone2"
                                                    value="{{ old('phone2', $data['member']->memberPersonalProfile?->phone2) }}">
                                                <p style="color: red">{{ $errors->first('phone2') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="dob">Date of Birth
                                                    <span class="text-red">*</span></label>
                                                <input type="date" class="form-control" name="dob"
                                                    value="{{ old('dob', $data['member']->memberPersonalProfile?->dob) }}"
                                                    max="{{ now()->subYears(12)->format('Y-m-d') }}" required>
                                                <p style="color: red">
                                                    {{ $errors->first('dob') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="nationality">Nationality
                                                    <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="nationality"
                                                    value="{{ old('nationality', $data['member']->memberPersonalProfile?->nationality) }}">
                                                <p style="color: red" required>
                                                    {{ $errors->first('nationality') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="state_of_origin">State of Origin
                                                    <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="state_of_origin"
                                                    value="{{ old('state_of_origin', $data['member']->memberPersonalProfile?->state_of_origin) }}">
                                                <p style="color: red" required>
                                                    {{ $errors->first('state_of_origin') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="lga_of_origin">LGA of Origin
                                                    <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="lga_of_origin"
                                                    value="{{ old('lga_of_origin', $data['member']->memberPersonalProfile?->lga_of_origin) }}"
                                                    required>
                                                <p style="color: red">
                                                    {{ $errors->first('lga_of_origin') }}</p>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="address1">Address 1
                                                    <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="address1"
                                                    value="{{ old('address1', $data['member']->memberPersonalProfile?->address1) }}"
                                                    required>
                                                <p style="color: red">
                                                    {{ $errors->first('address1') }}</p>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="address2">Address 2</label>
                                                <input type="text" class="form-control" name="address2"
                                                    value="{{ old('address2', $data['member']->memberPersonalProfile?->address2) }}">
                                                <p style="color: red">
                                                    {{ $errors->first('address2') }}</p>
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
                                        </div>
                                    </form>
                                </div>
                            </div>


                        </div>
                        {{-- Back Home --}}
                        <div class="w-100 justify-content-center text-center my-4">
                            <a class="btn btn-primary" href="{{ route('studentAccountSetup') }}">
                                <i class="fas fa-chevron-left"></i>
                                Back
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            {{-- modals start --}}

            {{-- modal end --}}



            {{-- process image --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    @if ($errors->any())
                        $('#personalProfileModal').modal('show');
                    @endif

                    let cropper = null;
                    let stream = null;

                    const preview = document.getElementById('passport-preview');
                    const uploadInput = document.getElementById('passport-upload');
                    const cropImage = document.getElementById('crop-image');
                    const croppedInput = document.getElementById('photo-cropped');
                    const cameraSection = document.getElementById('camera-section');
                    const video = document.getElementById('camera-stream');
                    const canvas = document.getElementById('snap-canvas');
                    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));

                    // ── Helper: open cropper modal
                    function openCropper(imageSrc) {
                        cropImage.src = imageSrc;
                        cropModal.show();

                        // Destroy existing cropper before re-initializing
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }

                        cropImage.onload = function() {
                            cropper = new Cropper(cropImage, {
                                aspectRatio: 1, // square for profile photo
                                viewMode: 1,
                                autoCropArea: 1,
                                checkOrientation: true,
                                movable: true,
                                zoomable: true,
                                rotatable: true,
                                scalable: true,
                                minContainerHeight: 250,
                                minContainerWidth: 500,
                                center: true,
                                responsive: true,
                                dragMode: 'move',
                                guides: true,
                                highlight: false,
                                background: false, // Speeds up render time




                            });
                        };
                    }

                    // ── Upload from file
                    uploadInput.addEventListener('change', function() {
                        const file = this.files[0];
                        if (!file) return;

                        const reader = new FileReader();
                        reader.onload = e => openCropper(e.target.result);
                        reader.readAsDataURL(file);

                        // Reset input so same file can be re-selected
                        this.value = '';
                    });

                    // ── Apply crop
                    document.getElementById('apply-crop-btn').addEventListener('click', function() {
                        if (!cropper) return;

                        const canvas = cropper.getCroppedCanvas({
                            width: 400,
                            height: 400
                        });
                        const base64 = canvas.toDataURL('image/jpeg', 0.9);

                        preview.src = base64;
                        croppedInput.val = base64;
                        document.getElementById('photo-cropped').value = base64;

                        cropModal.hide();
                        cropper.destroy();
                        cropper = null;
                    });

                    // ── Cancel crop
                    document.getElementById('cancel-crop-btn').addEventListener('click', function() {
                        cropModal.hide();
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                    });

                    // ── Open camera
                    document.getElementById('open-camera-btn').addEventListener('click', async function() {
                        try {
                            stream = await navigator.mediaDevices.getUserMedia({
                                video: {
                                    facingMode: 'user'
                                },
                                audio: false
                            });
                            video.srcObject = stream;
                            cameraSection.style.display = 'block';
                            this.style.display = 'none';
                        } catch (err) {
                            alert('Camera access denied or not available.');
                            console.error(err);
                        }
                    });

                    // ── Snap photo
                    document.getElementById('snap-btn').addEventListener('click', function() {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0);

                        const imageSrc = canvas.toDataURL('image/jpeg');
                        stopCamera();
                        openCropper(imageSrc);
                    });

                    // ── Close camera
                    document.getElementById('close-camera-btn').addEventListener('click', function() {
                        stopCamera();
                    });

                    function stopCamera() {
                        if (stream) {
                            stream.getTracks().forEach(track => track.stop());
                            stream = null;
                        }
                        cameraSection.style.display = 'none';
                        document.getElementById('open-camera-btn').style.display = 'inline-block';
                    }

                });
            </script>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
