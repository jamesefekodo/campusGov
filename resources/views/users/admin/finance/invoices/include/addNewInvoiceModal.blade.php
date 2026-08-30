<div class="modal fade" id="addNewInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    New Invoice</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('addNewInvoiceSubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description">Description: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ old('description') }}" required>
                                <p style="color: red">{{ $errors->first('description') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount">Amount (₦)<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="amount" value="{{ old('amount') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('amount') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="is_registered_member">Payer is a registered member:<span
                                        class="text-red">*</span></label>
                                <select class="form-select" name="is_registered_member" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="yes" {{ old('is_registered_member') == 'yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="no" {{ old('is_registered_member') == 'no' ? 'selected' : '' }}>
                                        No</option>
                                </select>
                                <p style="color: red">
                                    {{ $errors->first('is_registered_member') }}</p>
                            </div>


                            <div class="form-group col-md-6">
                                <label for="payer_name">Invoice for (Name): <span class="text-red">(Please use their
                                        username for registered)*</span></label>
                                <input type="text" class="form-control" name="payer_name"
                                    value="{{ old('payer_name') }}" required>
                                <p style="color: red">{{ $errors->first('payer_name') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="due_date">Due Date<span class="text-red">*</span></label>
                                <input type="datetime-local" class="form-control" name="due_date"
                                    value="{{ old('due_date') }}" required>
                                <p style="color: red">{{ $errors->first('due_date') }}</p>
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
