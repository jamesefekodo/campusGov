<div class="modal fade" id="editPaymentConfigModal_{{ $config->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit {{ $config->paymentMethod->name }} ({{ $config->version }})</h4>
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
                        action="{{ route('updateElectronicPaymentApiConfig', ['id' => $config->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @php
                            $showClientId = false;
                            $publicKey = false;
                            $secretKey = false;
                            $encryptionKey = false;

                            if ($data['method']->slug == 'paystack') {
                                $publicKey = true;
                                $secretKey = true;
                            }
                            if ($data['method']->slug == 'flutterwave' && $config->version == 'v3') {
                                $publicKey = true;
                                $secretKey = true;
                                $encryptionKey = true;
                            }
                            if ($data['method']->slug == 'flutterwave' && $config->version == 'v4') {
                                $showClientId = true;
                                $secretKey = true;
                                $encryptionKey = true;
                            }
                        @endphp
                        <div class="row">
                            @if ($showClientId == true)
                                <div class="form-group col-md-12">
                                    <label for="client_id">Client ID:<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="client_id"
                                        value="{{ $config->client_id }}" required>
                                    <p style="color: red">{{ $errors->first('client_id') }}</p>
                                </div>
                            @endif
                            @if ($publicKey == true)
                                <div class="form-group col-md-12">
                                    <label for="public_key">Public Key:<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="public_key"
                                        value="{{ $config->public_key }}" required>
                                    <p style="color: red">{{ $errors->first('public_key') }}</p>
                                </div>
                            @endif
                            @if ($secretKey == true)
                                <div class="form-group col-md-12">
                                    <label for="secret_key">Secret Key:<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="secret_key"
                                        value="{{ $config->secret_key }}" required>
                                    <p style="color: red">{{ $errors->first('secret_key') }}</p>
                                </div>
                            @endif
                            @if ($encryptionKey == true)
                                <div class="form-group col-md-12">
                                    <label for="encryption_key">Encryption Key:<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="encryption_key"
                                        value="{{ $config->encryption_key }}" required>
                                    <p style="color: red">{{ $errors->first('encryption_key') }}</p>
                                </div>
                            @endif
                            <div class="form-group col-md-6">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $config->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $config->is_active == '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                <p style="color: red">{{ $errors->first('is_active') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="default_status">Change Default Status: <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="default_status" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="default"
                                        {{ $data['method']->defaultApi->id == $config->id ? 'selected' : '' }}>
                                        Default</option>
                                    <option value="not_default"
                                        {{ $data['method']->defaultApi->id !== $config->id ? 'selected' : '' }}>
                                        Not-Default</option>
                                </select>
                                <p style="color: red">{{ $errors->first('default_status') }}</p>
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
