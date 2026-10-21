<div class="modal fade" id="editArciveFileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Update File Archive</h4>
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
                    action="{{ route('updateArchiveFileSubmit', ['uuid' => $data['file']->uuid]) }}"
                     enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="title">Title: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="title"
                                    value="{{ $data['file']->title }}" required>
                                <p style="color: red">{{ $errors->first('title') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description: </label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ $data['file']->description }}">
                                <p style="color: red">{{ $errors->first('description') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="members_can_see"><span class="text-capitalize">Member Access: <span
                                            class="text-red">*</span></span></label>
                                <select class="form-select" name="members_can_see" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="0"
                                        {{ $data['file']->members_can_see == '0' ? 'selected' : '' }}>
                                        No (Admins Only)</option>
                                    <option value="1"
                                        {{ $data['file']->members_can_see == '1' ? 'selected' : '' }}>
                                        Yes (Both admins & members)</option>
                                </select>
                                <p style="color: red">
                                    {{ $errors->first('members_can_see') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="file">Change File:</label>
                                <input type="file" class="form-control" name="file" value="{{ old('file') }}"
                                    accept=".pdf,.doc,.docx,.ppt,.pptx, .xls, .csv, .xlsx">

                                <p style="color: red">{{ $errors->first('file') }}</p>
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

        </div>
    </div>
</div>
