<div class="modal fade" id="addNewSliderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Slider</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('addNewSliderSubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group upload-container">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-md-7"><label for="image-upload" class="preview-frame">
                                            <img id="slider-preview"
                                                src="{{ asset('system/images/slider_placeholder.webp') }}"
                                                alt="">
                                            <div class="overlay"><i class="fas fa-upload"></i></div>
                                        </label>
                                    </div>
                                    <div class="col-md-5"><input type="file" id="image-upload"
                                            accept=".jpg, .jpeg, .png, .webp" onchange="previewImage()"
                                            name="background_image">
                                    </div>
                                    <p class="text-red">{{ $errors->first('background_image') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="header">Header</label>
                                    <input type="text" value="{{ old('header') }}" class="form-control"
                                        name="header">
                                    <p style="color: red">{{ $errors->first('header') }}</p>
                                </div>
                                <div class="form-group col-md-9">
                                    <label for="description">Description</label>
                                    <input type="text" value="{{ old('description') }}" class="form-control"
                                        name="description">
                                    <p style="color: red">{{ $errors->first('description') }}</p>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="order">Order</label>
                                    <input type="number" value="{{ old('order') }}" class="form-control"
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
    function previewImage() {
        const preview = document.getElementById('slider-preview');
        const fileInput = document.getElementById('image-upload');
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
