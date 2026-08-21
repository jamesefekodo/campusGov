<div class="modal fade" id="updateApplicationBioManifestoModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                   Update application for the position of {{ $data['application']->position->position }}</h4>
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
                        action="{{ route('memberUpdateSelfApplyForPositionSubmit', ['uuid' => $data['application']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="bio">Bio:<span class="text-red">*</span></label>
                                <!-- Using single names since each modal has its own isolated form -->
                                <textarea id="bio" name="bio" required>{{ $data['application']->bio }}</textarea>
                                <p style="color: red">{{ $errors->first('bio') }}</p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="manifesto">Manifesto:<span
                                        class="text-red">*</span></label>
                                <textarea id="manifesto" name="manifesto" required>{{ $data['application']->manifesto }}</textarea>
                                <p style="color: red">{{ $errors->first('manifesto') }}</p>
                            </div>

                            <script>
                                $(document).ready(function() {
                                    $('#bio').summernote({
                                        height: 200,
                                        toolbar: [
                                            ['style', ['bold', 'italic', 'underline', 'clear']],
                                            ['font', ['strikethrough', 'superscript', 'subscript']],
                                            ['fontsize', ['fontsize']],
                                            ['color', ['color']],
                                            ['para', ['ul', 'ol', 'paragraph']],
                                            ['height', ['height']]
                                        ]
                                    });

                                    $('#manifesto').summernote({
                                        height: 200,
                                        toolbar: [
                                            ['style', ['bold', 'italic', 'underline', 'clear']],
                                            ['font', ['strikethrough', 'superscript', 'subscript']],
                                            ['fontsize', ['fontsize']],
                                            ['color', ['color']],
                                            ['para', ['ul', 'ol', 'paragraph']],
                                            ['height', ['height']]
                                        ]
                                    });
                                });
                            </script>
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
