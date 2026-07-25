<div class="modal fade" id="editEmploymentAndBusinessModal_{{ $record->id }}" tabindex="-1" role="dialog"
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
                        action="{{ route('editNewBusinessSubmit', ['id' => $record->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="business">Business</label>
                                <input type="text" value="{{ $record->business }}" class="form-control"
                                    name="business">
                                <p style="color: red">{{ $errors->first('business') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address">Address</label>
                                <input type="text" value="{{ $record->address }}" class="form-control"
                                    name="address">
                                <p style="color: red">{{ $errors->first('address') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="position">Position</label>
                                <input type="text" value="{{ $record->position }}" class="form-control"
                                    name="position">
                                <p style="color: red">{{ $errors->first('position') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="start_date">Start Date</label>
                                <input type="month" value="{{ $record->start_date }}" class="form-control"
                                    name="start_date">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date">End Date</label>
                                <input type="month" value="{{ $record->end_date }}" class="form-control"
                                    name="end_date">
                            </div>
                            <p style="color: red">{{ $errors->first('end_date') }}</p>
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
