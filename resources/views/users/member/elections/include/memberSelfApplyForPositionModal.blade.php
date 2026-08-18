<div class="modal fade" id="memberSelfApplyForPositionModal_{{ $position->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Apply for the position of {{ $position->position }}</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('memberInitiateSelfApplyForPositionSubmit', ['uuid' => $position->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="bio_{{ $position->id }}">Bio:<span class="text-red">*</span></label>
                                <!-- Using array notation for names like bio[{{ $position->id }}] is also best practice for loops -->
                                <textarea id="bio_{{ $position->id }}" name="bio_{{ $position->id }}" required>{{ old('bio_' . $position->id) }}</textarea>
                                <p style="color: red">{{ $errors->first('bio_' . $position->id) }}</p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="manifesto_{{ $position->id }}">Manifesto:<span
                                        class="text-red">*</span></label>
                                <textarea id="manifesto_{{ $position->id }}" name="manifesto_{{ $position->id }}" required>{{ old('manifesto_' . $position->id) }}</textarea>
                                <p style="color: red">{{ $errors->first('manifesto_' . $position->id) }}</p>
                            </div>

                            <script>
                                $(document).ready(function() {
                                    // Initialize Summernote uniquely for this iteration using attribute starts-with [id^=...]
                                    // or by injecting the ID directly:
                                    $('#bio_{{ $position->id }}').summernote({
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

                                    $('#manifesto_{{ $position->id }}').summernote({
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
