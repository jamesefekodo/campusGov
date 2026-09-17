<div class="modal fade" id="editWhatsappLineModal_{{ $line->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Update WhatsApp Support Line</h4>
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
                        action="{{ route('editWhatsAppSupportLineSubmit', ['id' => $line->id]) }}"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="name">Name:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $line->name }}"
                                    required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="number">WhatsApp Number:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="number" value="{{ $line->number }}"
                                    required>
                                <p style="color: red">{{ $errors->first('number') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $line->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $line->is_active == '0' ? 'selected' : '' }}>
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
