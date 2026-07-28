<div class="modal fade" id="updateTaxModal_{{ $tax->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Update Tax :: {{ $tax->tax_name }}</h4>
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
                        action="{{ route('editTaxSubmit', ['uuid' => $tax->uuid]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="tax_name">Tax Name <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="tax_name" value="{{ $tax->tax_name }}"
                                    required>
                                <p style="color: red">{{ $errors->first('tax_name') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tax_method">Method <span class="text-red">*</span></label>
                                <select class="form-select" name="tax_method" required>
                                    <option disabled selected>Select Tax
                                        Type
                                    </option>
                                    <option value="fixed_amount"
                                        {{ $tax->tax_method == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                                    <option value="percentage" {{ $tax->tax_method == 'percentage' ? 'selected' : '' }}>
                                        Percentage</option>
                                </select>
                                <p style="color: red">{{ $errors->first('tax_method') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tax_rate">Tax Rate <span class="text-red">*</span></label>
                                <input type="number" class="form-control" placeholder="" value="{{ $tax->tax_rate }}"
                                    name="tax_rate" step="any" required>
                                <p style="color: red">{{ $errors->first('tax_rate') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $tax->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $tax->is_active == '0' ? 'selected' : '' }}>
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
