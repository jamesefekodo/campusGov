<div class="modal fade" id="updatePastackReferenceModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Update Paystack Reference Number</h4>
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
                    action="{{ route('updatePaystackRefNumber', ['payment_transaction_id' => $data['transaction']->payment_transaction_id]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <h6 class="text-danger">If the payer was debited and you can confirm the transaction is successful on Paystack, copy the transaction reference from your Paystack dashboard and paste it here.</h6>
                                <label for="paystack_reference">Reference Number <span class="text-red">*</span></label>
                                <input type="text" value="{{ $data['transaction']->paystack_reference }}" class="form-control" name="paystack_reference" required>
                                <p style="color: red">{{ $errors->first('paystack_reference') }}</p>
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
