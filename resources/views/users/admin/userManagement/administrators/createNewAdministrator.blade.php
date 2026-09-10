@extends('layouts.userLayout')
@section('title', 'Create Admin Profile')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container pb-5">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        Create Admin Profile
                                    </h4>

                                    <form class="form my_form" method="POST"
                                        action="{{ route('createAdminProfileSubmit') }}" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row d-flex justify-content-center">
                                            @php
                                                $imagePlaceholder = asset('system/images/default_profile_pic.jpg');

                                                // if ($data['member']->photo) {
                                                //     $imagePlaceholder = asset(
                                                //         'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['member']->photo,
                                                //     );
                                                // }

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
                                            <div class="form-group col-md-6">
                                                <label for="username">Username<span class="text-red">*</span>:</label>
                                                <input type="text" class="form-control" name="username"
                                                    value="{{ old('username') }}" required>
                                                <p style="color: red">{{ $errors->first('username') }}</p>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="name">Full Name<span class="text-red">*</span>:</label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ old('name') }}" required>
                                                <p style="color: red">{{ $errors->first('name') }}</p>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="phone">Phone<span class="text-red">*</span>:</label>
                                                <input type="text" class="form-control" name="phone"
                                                    value="{{ old('phone') }}" required>
                                                <p style="color: red">{{ $errors->first('phone') }}</p>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="email">Email<span class="text-red">*</span>:</label>
                                                <input type="email" class="form-control" name="email"
                                                    value="{{ old('email') }}" required>
                                                <p style="color: red">{{ $errors->first('email') }}</p>
                                            </div>


                                            <div class="form-group col-md-6">
                                                <label for="position">Position<span class="text-red">*</span>:</label>
                                                <input type="text" class="form-control" name="position"
                                                    value="{{ old('position') }}" required>
                                                <p style="color: red">{{ $errors->first('position') }}</p>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="description">Description<span
                                                        class="text-red">*</span>:</label>
                                                <input type="text" class="form-control" name="description"
                                                    value="{{ old('description') }}" required>
                                                <p style="color: red">{{ $errors->first('description') }}</p>
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
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4 pt-5">
                                <a class="btn btn-primary" href="{{ route('administrators') }}"
                                    >
                                    <i class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- script start --}}
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

            {{-- script end --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
