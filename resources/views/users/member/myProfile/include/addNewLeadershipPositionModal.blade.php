<div class="modal fade" id="addNewLeadershipPositionModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New</h4>
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
                    action="{{ route('addLeadershipPositionSubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="position">Position <span class="text-red">*</span></label>
                                <input type="text" value="{{ old('position') }}" class="form-control" name="position"
                                    required>
                                <p style="color: red">{{ $errors->first('position') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="organization">Organization <span class="text-red">*</span></label>
                                <input type="text" value="{{ old('organization') }}" class="form-control"
                                    name="organization" required>
                                <p style="color: red">{{ $errors->first('organization') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address">Address <span class="text-red">*</span></label>
                                <input type="text" value="{{ old('address') }}" class="form-control" name="address"
                                    required>
                                <p style="color: red">{{ $errors->first('address') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="is_current">Currently holding this position? <span
                                        class="text-red">*</span></label>
                                <select class="form-select" name="is_current" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value=1 {{ old('is_current') == 1 ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value=0 {{ old('is_current') == 0 ? 'selected' : '' }}>
                                        No</option>
                                </select>
                                <p style="color: red">{{ $errors->first('is_current') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="start_date">Start Date <span class="text-red">*</span></label>
                                <input type="month" value="{{ old('start_date') }}" class="form-control"
                                    name="start_date" required>
                                <p style="color: red">{{ $errors->first('start_date') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date">End Date</label>
                                <input type="month" value="{{ old('end_date') }}" class="form-control"
                                    name="end_date">
                                <p style="color: red">{{ $errors->first('end_date') }}</p>
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
