<div class="modal fade" id="editSliderModal_{{ $slider->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Update Slider</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('editSliderSubmit', ['id' => $slider->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            @php
                                $bgImage = asset('system/images/slider_placeholder.webp');
                                if (
                                    $slider->background_image &&
                                    file_exists(
                                        public_path(
                                            'assets/tenant_' .
                                                tenant()->id .
                                                '/uploads/sliders/' .
                                                $slider->background_image,
                                        ),
                                    )
                                ) {
                                    $bgImage = asset(
                                        'assets/tenant_' .
                                            tenant()->id .
                                            '/uploads/sliders/' .
                                            $slider->background_image,
                                    );
                                }
                            @endphp
                            <div class="form-group upload-container">
                                <div class="row d-flex justify-content-center">

                                    <div class="col-md-7">
                                        <label for="slider-image-upload-{{ $slider->id }}" class="preview-frame"
                                            style="cursor: pointer;">
                                            <img id="slider-preview-image-{{ $slider->id }}"
                                                class="slider-preview-image" src="{{ $bgImage }}"
                                                alt="Slider Background">

                                            <div class="overlay">
                                                <i class="fas fa-upload"></i>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="col-md-5">

                                        <input type="file" id="slider-image-upload-{{ $slider->id }}"
                                            class="slider-image-upload" accept=".jpg, .jpeg, .png, .webp"
                                            name="background_image"
                                            data-preview="slider-preview-image-{{ $slider->id }}">

                                    </div>

                                    <p class="text-red">
                                        {{ $errors->first('background_image') }}
                                    </p>

                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="header">Header</label>
                                    <input type="text" value="{{ $slider->header }}" class="form-control"
                                        name="header">
                                    <p style="color: red">{{ $errors->first('header') }}</p>
                                </div>
                                <div class="form-group col-md-9">
                                    <label for="description">Description</label>
                                    <input type="text" value="{{ $slider->description }}" class="form-control"
                                        name="description">
                                    <p style="color: red">{{ $errors->first('description') }}</p>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="order">Order</label>
                                    <input type="number" value="{{ $slider->order }}" class="form-control"
                                        name="order">
                                    <p style="color: red">{{ $errors->first('order') }}</p>
                                </div>
                            </div>
                            <hr class="javascript-form">
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




{{-- Preview Passport --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.slider-image-upload').forEach(function(input) {

            input.addEventListener('change', function() {

                const file = this.files[0];

                if (!file) {
                    return;
                }

                const previewId = this.dataset.preview;
                const preview = document.getElementById(previewId);

                if (!preview) {
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            });

        });

    });
</script>
