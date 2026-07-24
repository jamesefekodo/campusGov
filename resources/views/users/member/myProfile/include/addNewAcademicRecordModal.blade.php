<div class="modal fade" id="addNewAcademicRecordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Academic Record</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('addNewAcademicRecordSubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="institution">Institution <span class="text-red">*</span></label>
                                <input type="text" value="" class="form-control" name="institution" required>
                                <p style="color: red">{{ $errors->first('institution') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address">Address <span class="text-red">*</span></label>
                                <input type="text" value="" class="form-control" name="address" required>
                                <p style="color: red">{{ $errors->first('address') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="certificate">Certificate Obtained <span class="text-red">*</span></label>
                                <input type="text" value="" class="form-control" name="certificate" required>
                                <p style="color: red">{{ $errors->first('certificate') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="date">Date <span class="text-red">*</span></label>
                                <input type="month" name="date"
                                    value="{{ old('date') }}">
                                <p style="color: red">{{ $errors->first('date') }}</p>
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
