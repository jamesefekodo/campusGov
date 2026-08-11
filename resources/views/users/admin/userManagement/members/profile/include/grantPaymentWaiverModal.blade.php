<div class="modal fade" id="grantPaymentWaiverModal_{{ $due->fee_id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Grant Waiver</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">
                    <h4 class="text-danger p-4">Members who have been granted a fee waiver can no longer make payments
                        for that specific waived due. Additionally, the waiver is only effective after approval, and the
                        administrator who initiates a waiver cannot be the same person who approves it.</h4>
                    <div class="row p-2 col-md-12 fs-2">
                        <div class="col-md-6">
                            {{ $due->title }} | {{ $due->academic_year }}/{{ $due->academic_year + 1 }}
                        </div>
                        <div class="col-md-6 text-success">
                            ₦{{ number_format($due->payable_amount, 2) }}
                        </div>
                    </div>

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST"
                        action="{{ route('initiatePaymentWaiver', ['uuid' => $data['member']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="fee_id" value="{{ $due->fee_id }}">

                            <div class="form-group col-md-12">
                                <label for="reason">Reason for waiver <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="reason" value="{{ old('reason') }}">
                                <p style="color: red">
                                    {{ $errors->first('reason') }}</p>
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
