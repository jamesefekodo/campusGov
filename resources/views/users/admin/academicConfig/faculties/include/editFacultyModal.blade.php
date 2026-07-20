<div class="modal fade" id="editFacultyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Faculty</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('updateFacultySubmit', ['uuid' => $data['faculty']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Name:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ $data['faculty']->name }}" required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="administrative_head_title">Admin Head Title <span
                                        class="small text-secondary">(Dean, Director)</span>:<span
                                        class="text-red">*</span></label>
                                <input type="text" class="form-control" name="administrative_head_title"
                                    value="{{ $data['faculty']->administrative_head_title }}" required>
                                <p style="color: red">{{ $errors->first('administrative_head_title') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="acronym">Acronym:</label>
                                <input type="text" class="form-control" name="acronym"
                                    value="{{ $data['faculty']->acronym }}">
                                <p style="color: red">{{ $errors->first('acronym') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $data['faculty']->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $data['faculty']->is_active == '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                <p style="color: red">{{ $errors->first('is_active') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description:<span class="text-red">*</span></label>
                                <textarea id="description" name="description" required>
                                    {{ $data['faculty']->description }}
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
