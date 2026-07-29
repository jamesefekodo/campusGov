<div class="row">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h5 class="mb-1">Payment</h5>
                <small class="text-muted">
                    Select how you would like to pay this due.
                </small>
            </div>

            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                aria-expanded="false" aria-controls="{{ $collapseId }}">
                <i class="fas fa-credit-card me-2"></i>
                Pay Now
                <i class="fas fa-chevron-down ms-2"></i>
            </button>

        </div>

        <div class="collapse mt-3" id="{{ $collapseId }}">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <div class="row g-3 justify-content-center align-items-center">
                        @if (manualPaymentConfig()->allow_manual_dues == true)
                            <div class="col-md-6">
                                <div class="card border-warning h-100">
                                    <div class="card-body text-center">

                                        <i class="fas fa-university fa-3x text-warning mb-3"></i>

                                        <h5>Pay Manually</h5>

                                        <p class="text-muted">
                                            Payment requires manual verification before confirmation.
                                        </p>

                                        <button class="btn btn-warning w-100" data-bs-toggle="modal"
                                            data-bs-target="#manualPaymentModal_{{ $due->id }}">
                                            Continue
                                        </button>

                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <div class="card border-success h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-bolt fa-3x text-success mb-3"></i>

                                    <h5>Pay Electronically</h5>

                                    <p class="text-muted">
                                        Payment is confirmed instantly upon successful payment.
                                    </p>

                                    <button class="btn btn-success w-100" data-bs-toggle="modal"
                                        data-bs-target="#electronicPaymentModal-{{ $due->id }}">
                                        Continue
                                    </button>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
{{-- ####################################################### --}}
{{-- the modals --}}
{{-- offline modal start --}}
<div class="modal fade" id="manualPaymentModal_{{ $due->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Pay Now</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('memberPayAssociationDue', ['uuid' => $due->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Select Manual Payment Method
                            </label>

                            <div class="row">

                                @php
                                    $methods = [
                                        'cash' => [
                                            'title' => 'Cash',
                                            'icon' => 'fas fa-money-bill-wave',
                                            'description' => 'Pay directly with cash to the association.',
                                        ],

                                        'bank_transfer' => [
                                            'title' => 'Bank Transfer/Deposit',
                                            'icon' => 'fas fa-university',
                                            'description' => 'Transfer funds into the association bank account.',
                                        ],

                                        'cheque' => [
                                            'title' => 'Cheque',
                                            'icon' => 'fas fa-money-check-alt',
                                            'description' => 'Pay using a cheque issued in favour of the association.',
                                        ],
                                    ];

                                    $config = manualPaymentConfig();
                                @endphp

                                @foreach ($methods as $key => $method)
                                    @if ($config && $config->$key)
                                        <div class="col-lg-4 col-md-6 mb-3 manual-payment-option">

                                            <input type="radio"
                                                id="manual-payment-method-{{ $due->id }}-{{ $key }}"
                                                name="manual_payment_method" value="{{ $key }}"
                                                {{ old('manual_payment_method') == $key ? 'checked' : '' }} required>

                                            <label
                                                for="manual-payment-method-{{ $due->id }}-{{ $key }}">

                                                <div class="manual-payment-icon">
                                                    <i class="{{ $method['icon'] }}"></i>
                                                </div>

                                                <h5>{{ $method['title'] }}</h5>

                                                <p>{{ $method['description'] }}</p>

                                                <span class="manual-payment-selected">
                                                    <i class="fas fa-check"></i>
                                                </span>

                                            </label>

                                        </div>
                                    @endif
                                @endforeach

                            </div>

                            <p class="text-danger">
                                {{ $errors->first('manual_payment_method') }}
                            </p>

                        </div>


                        {{-- ============================
                                Payment Instructions
                            ============================= --}}

                        <div id="manual-payment-instructions-{{ $due->id }}" style="display:none;">

                            {{-- Cash --}}
                            <div class="alert alert-warning payment-method-info" data-method="cash"
                                style="display:none;">

                                <h5 class="mb-2">
                                    <i class="fas fa-money-bill-wave"></i>
                                    Cash Payment
                                </h5>

                                Kindly visit the association's finance office and make a cash payment of

                                <strong class="fw-bold fs-5">
                                    ₦{{ number_format($due->payable_amount, 2) }}
                                </strong>.

                                After payment, obtain a receipt and upload a clear photograph of it below.

                            </div>


                            {{-- Bank Transfer --}}
                            <div class="alert alert-warning payment-method-info" data-method="bank_transfer"
                                style="display:none;">

                                <h5 class="mb-3">
                                    <i class="fas fa-university"></i>
                                    Bank Transfer / Deposit
                                </h5>

                                Please transfer

                                <strong class="fw-bold fs-5">
                                    ₦{{ number_format($due->payable_amount, 2) }}
                                </strong>

                                into any of the following bank accounts.

                                <div class="row mt-3">

                                    @foreach (activeBankAccounts() as $account)
                                        <div class="col-md-6 mb-3">

                                            <div class="card border shadow-sm">

                                                <div class="card-body">

                                                    <h6 class="fw-bold">
                                                        {{ $account->bank }}
                                                    </h6>

                                                    <p class="mb-1">
                                                        <strong>Account Name:</strong> {{ $account->account_name }}
                                                    </p>

                                                    <p class="mb-0">
                                                        <strong>Account Number: </strong><span class="fs-4 fw-bold text-blue">
                                                            {{ $account->account_number }}
                                                        </span>

                                                    </p>

                                                </div>

                                            </div>

                                        </div>
                                    @endforeach

                                </div>

                                After payment, upload your payment receipt or transaction screenshot below.

                            </div>


                            {{-- Cheque --}}
                            <div class="alert alert-secondary payment-method-info" data-method="cheque"
                                style="display:none;">

                                <h5>
                                    <i class="fas fa-money-check-alt"></i>
                                    Cheque Payment
                                </h5>

                                Kindly issue a cheque in favour of the association for

                                <strong class="text-success">
                                    ₦{{ number_format($due->payable_amount, 2) }}
                                </strong>

                                and submit it to the finance office.

                                Afterwards, upload a clear photograph of the cheque or acknowledgement slip.

                            </div>


                            {{-- Upload Proof --}}

                            <div class="mt-4">

                                <label class="form-label fw-bold">
                                    Proof of Payment
                                </label>

                                <input type="file" class="form-control" name="proof"
                                    id="proof-{{ $due->id }}" accept=".jpg,.jpeg,.png,.webp">

                                <small class="text-muted">
                                    Accepted formats: JPG, JPEG, PNG and WEBP.
                                </small>

                                <p class="text-danger">
                                    {{ $errors->first('proof') }}
                                </p>

                                <div class="mt-3 text-center">

                                    <img id="proof-preview-{{ $due->id }}" src=""
                                        style="display:none;max-width:300px;border-radius:10px;"
                                        class="img-thumbnail">

                                </div>

                            </div>

                        </div>


                        <script>
                            document.addEventListener('DOMContentLoaded', function() {

                                const dueId = "{{ $due->id }}";

                                const radios = document.querySelectorAll(
                                    'input[name="manual_payment_method"]'
                                );

                                const wrapper = document.getElementById(
                                    'manual-payment-instructions-' + dueId
                                );

                                const infos = wrapper.querySelectorAll('.payment-method-info');

                                function updateInstructions() {

                                    const checked = document.querySelector(
                                        'input[name="manual_payment_method"]:checked'
                                    );

                                    if (!checked) {

                                        wrapper.style.display = 'none';
                                        return;

                                    }

                                    wrapper.style.display = 'block';

                                    infos.forEach(function(item) {

                                        item.style.display = (
                                            item.dataset.method === checked.value
                                        ) ? 'block' : 'none';

                                    });

                                }

                                radios.forEach(function(radio) {

                                    radio.addEventListener('change', updateInstructions);

                                });

                                updateInstructions();


                                // Preview

                                const proof = document.getElementById('proof-' + dueId);

                                const preview = document.getElementById(
                                    'proof-preview-' + dueId
                                );

                                proof.addEventListener('change', function() {

                                    if (!this.files.length) {

                                        preview.style.display = 'none';
                                        return;

                                    }

                                    preview.src = URL.createObjectURL(this.files[0]);

                                    preview.style.display = 'block';

                                });

                            });
                        </script>

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

{{-- offline modal ends --}}
