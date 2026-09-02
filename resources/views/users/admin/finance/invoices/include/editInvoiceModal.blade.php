<div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Invoice</h4>
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
                    action="{{ route('updateInvoiceSubmit', ['uuid' => $data['invoice']->uuid]) }}"
                     enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="description">Description: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ $data['invoice']->description }}" required>
                                <p style="color: red">{{ $errors->first('description') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount">Amount (₦)<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="amount"
                                    value="{{ $data['invoice']->amount }}" required>
                                <p style="color: red">{{ $errors->first('amount') }}</p>
                            </div>
                            @if ($data['invoice']->invoiceFor)
                                <div class="form-group col-md-6">
                                    <label for="registered_payer">Invoice for (Member's username): <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="registered_payer"
                                        value="{{ $data['invoice']->registered_payer }}" required>
                                    <p style="color: red">{{ $errors->first('registered_payer') }}</p>
                                </div>
                            @else
                                <div class="form-group col-md-6">
                                    <label for="non_registered_payer">Invoice for (Name): <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="non_registered_payer"
                                        value="{{ $data['invoice']->non_registered_payer }}" required>
                                    <p style="color: red">{{ $errors->first('non_registered_payer') }}</p>
                                </div>
                            @endif



                            <div class="form-group col-md-6">
                                <label for="due_date">Due Date<span class="text-red">*</span></label>
                                <input type="datetime-local" class="form-control" name="due_date"
                                    value="{{ $data['invoice']->due_date }}" required>
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
