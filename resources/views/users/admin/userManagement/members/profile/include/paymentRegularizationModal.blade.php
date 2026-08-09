<div class="modal fade" id="paymentRegularizationModal_{{ $due->fee_id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Regularize Due Payment</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">
                    <div class="row p-5 col-md-12 fs-2">
                        <div class="col-md-6">
                            {{ $due->title }} | {{ $due->academic_year }}/{{ $due->academic_year + 1 }}
                        </div>
                        <div class="col-md-6 text-success">
                            ₦{{ number_format($due->payable_amount, 2) }}
                        </div>
                    </div>

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('initiatePaymentRegularization', ['uuid' => $data['member']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="fee_id" value="{{ $due->fee_id }}">

                            <div class="form-group col-md-7">
                                <label for="amount">Amount Paid (₦)<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="amount"
                                    value="{{ $due->payable_amount }}" readonly>
                                <p style="color: red">{{ $errors->first('amount') }}</p>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="payment_date">Date<span class="text-red">*</span></label>
                                <input type="date" class="form-control" name="payment_date"
                                    value="{{ old('payment_date') }}" required>
                                <p style="color: red">{{ $errors->first('payment_date') }}</p>
                            </div>

                            @php
                                $paymentMethodId = 'payment_method_' . $due->id;

                                $manualContainerId = 'manual_payment_container_' . $due->id;
                                $manualMethodId = 'manual_payment_method_' . $due->id;

                                $bankContainerId = 'bank_account_container_' . $due->id;
                                $bankAccountId = 'bank_account_' . $due->id;

                                $electronicContainerId = 'electronic_payment_container_' . $due->id;
                                $electronicMethodId = 'electronic_payment_method_' . $due->id;
                            @endphp


                            {{-- Payment Method --}}
                            <div class="form-group col-md-12">

                                <label for="{{ $paymentMethodId }}">
                                    Payment Method:
                                    <span class="text-red">*</span>
                                </label>

                                <select name="payment_method" id="{{ $paymentMethodId }}" class="form-control"
                                    required>

                                    <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>
                                        Select
                                    </option>

                                    <option value="manual" {{ old('payment_method') == 'manual' ? 'selected' : '' }}>
                                        Manual
                                    </option>

                                    <option value="electronic"
                                        {{ old('payment_method') == 'electronic' ? 'selected' : '' }}>
                                        Electronic
                                    </option>

                                </select>

                                <p style="color: red">
                                    {{ $errors->first('payment_method') }}
                                </p>

                            </div>


                            {{-- ========================================================= --}}
                            {{-- MANUAL PAYMENT METHOD --}}
                            {{-- ========================================================= --}}

                            <div class="form-group col-md-12" id="{{ $manualContainerId }}" style="display: none;">

                                <label for="{{ $manualMethodId }}">
                                    Manual Payment Method:
                                    <span class="text-red">*</span>
                                </label>

                                <select name="manual_payment_method" id="{{ $manualMethodId }}" class="form-control">

                                    <option value="" disabled
                                        {{ old('manual_payment_method') ? '' : 'selected' }}>
                                        Select
                                    </option>

                                    @php
                                        $manualConfig = manualPaymentConfig();
                                    @endphp

                                    @if ($manualConfig?->cash)
                                        <option value="cash"
                                            {{ old('manual_payment_method') == 'cash' ? 'selected' : '' }}>
                                            Cash
                                        </option>
                                    @endif

                                    @if ($manualConfig?->bank_transfer)
                                        <option value="bank_transfer"
                                            {{ old('manual_payment_method') == 'bank_transfer' ? 'selected' : '' }}>
                                            Bank Transfer
                                        </option>
                                    @endif

                                    @if ($manualConfig?->cheque)
                                        <option value="cheque"
                                            {{ old('manual_payment_method') == 'cheque' ? 'selected' : '' }}>
                                            Cheque
                                        </option>
                                    @endif

                                </select>

                                <p style="color: red">
                                    {{ $errors->first('manual_payment_method') }}
                                </p>

                            </div>


                            {{-- ========================================================= --}}
                            {{-- BANK ACCOUNT --}}
                            {{-- ========================================================= --}}

                            <div class="form-group col-md-12" id="{{ $bankContainerId }}" style="display: none;">

                                <label for="{{ $bankAccountId }}">
                                    Bank Account:
                                    <span class="text-red">*</span>
                                </label>

                                <select name="bank_account_id" id="{{ $bankAccountId }}" class="form-control">

                                    <option value="" disabled {{ old('bank_account_id') ? '' : 'selected' }}>
                                        Select Bank Account
                                    </option>

                                    @foreach (activeBankAccounts() as $bankAccount)
                                        <option value="{{ $bankAccount->id }}"
                                            {{ old('bank_account_id') == $bankAccount->id ? 'selected' : '' }}>

                                            {{ $bankAccount->bank }}
                                            -
                                            {{ $bankAccount->account_name }}
                                            ({{ $bankAccount->account_number }})
                                        </option>
                                    @endforeach

                                </select>

                                <p style="color: red">
                                    {{ $errors->first('bank_account_id') }}
                                </p>

                            </div>


                            {{-- ========================================================= --}}
                            {{-- ELECTRONIC PAYMENT --}}
                            {{-- ========================================================= --}}

                            <div class="form-group col-md-12" id="{{ $electronicContainerId }}" style="display: none;">

                                <label for="{{ $electronicMethodId }}">
                                    Electronic Payment Provider:
                                    <span class="text-red">*</span>
                                </label>

                                <select name="electronic_payment_method" id="{{ $electronicMethodId }}"
                                    class="form-control">

                                    <option value="" disabled
                                        {{ old('electronic_payment_method') ? '' : 'selected' }}>
                                        Select Electronic Payment Provider
                                    </option>

                                    @foreach (electronicsPaymentMethods() as $method)
                                        <option value="{{ $method->slug }}"
                                            {{ old('electronic_payment_method') == $method->slug ? 'selected' : '' }}>

                                            {{ $method->name }}

                                        </option>
                                    @endforeach

                                </select>

                                <p style="color: red">
                                    {{ $errors->first('electronic_payment_method') }}
                                </p>

                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ old('description') }}">
                                <p style="color: red">
                                    {{ $errors->first('description') }}</p>
                            </div>
                            @php
                                $proofInputId = 'proof_of_payment_' . $due->id;
                                $proofPreviewId = 'proof_preview_' . $due->id;
                            @endphp

                            <div class="form-group col-md-12">

                                <label for="{{ $proofInputId }}">
                                    Proof of Payment
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="file" class="form-control" name="proof_of_payment"
                                    id="{{ $proofInputId }}"
                                    accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" required>

                                <p style="color: red">
                                    {{ $errors->first('proof_of_payment') }}
                                </p>

                                {{-- Preview --}}
                                <div id="{{ $proofPreviewId }}" class="mt-3" style="display: none;">

                                    <div class="mb-2">
                                        <strong>Payment Proof Preview</strong>
                                    </div>

                                    <img src="" alt="Proof of Payment Preview"
                                        class="img-fluid rounded border"
                                        style="max-width: 400px; max-height: 400px; object-fit: contain;">

                                </div>

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

            {{-- script start --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const paymentMethod = document.getElementById('{{ $paymentMethodId }}');

                    const manualContainer =
                        document.getElementById('{{ $manualContainerId }}');

                    const manualMethod =
                        document.getElementById('{{ $manualMethodId }}');

                    const bankContainer =
                        document.getElementById('{{ $bankContainerId }}');

                    const bankAccount =
                        document.getElementById('{{ $bankAccountId }}');

                    const electronicContainer =
                        document.getElementById('{{ $electronicContainerId }}');

                    const electronicMethod =
                        document.getElementById('{{ $electronicMethodId }}');


                    function updatePaymentMethod() {

                        // Hide everything first
                        manualContainer.style.display = 'none';
                        bankContainer.style.display = 'none';
                        electronicContainer.style.display = 'none';

                        // Remove required
                        manualMethod.required = false;
                        bankAccount.required = false;
                        electronicMethod.required = false;


                        // MANUAL
                        if (paymentMethod.value === 'manual') {

                            manualContainer.style.display = 'block';
                            manualMethod.required = true;

                            updateManualMethod();

                        }


                        // ELECTRONIC
                        if (paymentMethod.value === 'electronic') {

                            electronicContainer.style.display = 'block';
                            electronicMethod.required = true;

                        }
                    }


                    function updateManualMethod() {

                        bankContainer.style.display = 'none';
                        bankAccount.required = false;

                        if (
                            paymentMethod.value === 'manual' &&
                            manualMethod.value === 'bank_transfer'
                        ) {

                            bankContainer.style.display = 'block';
                            bankAccount.required = true;

                        }
                    }


                    paymentMethod.addEventListener('change', updatePaymentMethod);

                    manualMethod.addEventListener('change', updateManualMethod);


                    // Handle old input after validation failure
                    updatePaymentMethod();

                });
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const input = document.getElementById('{{ $proofInputId }}');
                    const previewContainer = document.getElementById('{{ $proofPreviewId }}');
                    const previewImage = previewContainer.querySelector('img');

                    input.addEventListener('change', function() {

                        const file = this.files[0];

                        if (!file) {
                            previewContainer.style.display = 'none';
                            previewImage.src = '';
                            return;
                        }

                        // Make sure it is an image
                        if (!file.type.startsWith('image/')) {
                            previewContainer.style.display = 'none';
                            previewImage.src = '';
                            return;
                        }

                        const reader = new FileReader();

                        reader.onload = function(event) {

                            previewImage.src = event.target.result;
                            previewContainer.style.display = 'block';

                        };

                        reader.readAsDataURL(file);
                    });

                });
            </script>
            {{-- script end --}}
        </div>
    </div>
</div>
