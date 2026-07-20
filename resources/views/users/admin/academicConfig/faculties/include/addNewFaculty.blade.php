<div class="modal fade" id="addNewFacultyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Faculty</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('addFacultySubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="name">Name:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="administrative_head_title">Admin Head Title <span class="small text-secondary">(Dean, Director)</span>:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="administrative_head_title" value="{{ old('administrative_head_title') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('administrative_head_title') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="acronym">Acronym:</label>
                                <input type="text" class="form-control" name="acronym" value="{{ old('acronym') }}">
                                <p style="color: red">{{ $errors->first('acronym') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description:<span class="text-red">*</span></label>
                                <textarea id="description" name="description" value="{{ old('description') }}" required>
                                    {{ old('description') }}
                                </textarea>
                                <p style="color: red">
                                    {{ $errors->first('description') }}</p>
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

        </div>
    </div>
</div>
