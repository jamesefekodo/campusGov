<div class="modal fade" id="editBankAccountModal_{{ $account->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Bank Account</h4>
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
                        action="{{ route('updateBankAccount', ['id' => $account->id]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="account_number">Account Number:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="account_number"
                                    value="{{ $account->account_number }}" required>
                                <p style="color: red">{{ $errors->first('account_number') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="account_name">Account Name:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="account_name"
                                    value="{{ $account->account_name }}" required>
                                <p style="color: red">{{ $errors->first('account_name') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bank">Bank:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="bank" value="{{ $account->bank }}"
                                    required>
                                <p style="color: red">{{ $errors->first('bank') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $account->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $account->is_active == '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                <p style="color: red">{{ $errors->first('is_active') }}</p>
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
