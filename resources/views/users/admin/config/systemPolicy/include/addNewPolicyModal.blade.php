<div class="modal fade" id="addNewPermissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Policy</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('addNewSystemPolicySubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="title">Title: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ old('title') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('title') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="policy_statement">Policy Statement: <span class="text-danger">*</span></label>
                                <textarea id="policy_statement" name="policy_statement" value="{{ old('policy_statement') }}" required>
                                    {{ old('policy_statement') }}
                                </textarea>
                                <p style="color: red">
                                    {{ $errors->first('policy_statement') }}</p>
                            </div>

                            <div class="d-flex align-items-center">
                                <hr class="flex-grow-1">
                                <span class="mx-2 text-black">Applicable to:</span>
                                <hr class="flex-grow-1">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="applicable_to_member"><span
                                        class="text-capitalize">Members: <span class="text-danger">*</span></span></label>
                                <select class="form-select" name="applicable_to_member" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="0" {{ old('applicable_to_member') == '0' ? 'selected' : '' }}>
                                        No</option>
                                    <option value="1" {{ old('applicable_to_member') == '1' ? 'selected' : '' }}>
                                        Yes</option>
                                </select>
                                <p style="color: red">
                                    {{ $errors->first('applicable_to_member') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="applicable_to_administrator"><span
                                        class="text-capitalize">Administrators:</span></label>
                                <select class="form-select" name="applicable_to_administrator" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="0"
                                        {{ old('applicable_to_administrator') == '0' ? 'selected' : '' }}>
                                        No</option>
                                    <option value="1"
                                        {{ old('applicable_to_administrator') == '1' ? 'selected' : '' }}>
                                        Yes</option>
                                </select>
                                <p style="color: red">
                                    {{ $errors->first('applicable_to_administrator') }}</p>
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
                    $('#policy_statement').summernote({
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
