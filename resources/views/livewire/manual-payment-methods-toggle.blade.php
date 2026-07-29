<div>
    <h6 class="fw-semibold mb-3">Manual Payment Methods</h6>

    @if ($saved)
        <div class="alert alert-success py-2 mb-3" x-data x-init="setTimeout(() => $el.remove(), 2500)">
            <i class="fas fa-check-circle me-2"></i> Settings saved.
        </div>
    @endif

    <div class="row g-3">

        {{-- Cash --}}
        <div class="col-md-6 flex">
            <div
                class="card rounded-3 p-3 d-flex flex-row align-items-center justify-content-between
                {{ $cash ? 'border-success bg-success bg-opacity-10' : 'border-secondary' }}">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-money-bill-wave fs-4 {{ $cash ? 'text-success' : 'text-muted' }}"></i>
                    <div>
                        <p class="mb-0 fw-semibold">Cash</p>
                        <small class="{{ $cash ? 'text-success' : 'text-muted' }}">
                            {{ $cash ? 'Enabled' : 'Disabled' }}
                        </small>
                    </div>
                </div>
                {{-- Switch button --}}
                <div wire:click="toggle('cash')" style="cursor:pointer">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small {{ $cash ? 'text-success fw-semibold' : 'text-muted' }}">
                            {{ $cash ? 'ON' : 'OFF' }}
                        </span>
                        <div
                            style="
                            width: 48px; height: 26px; border-radius: 13px;
                            background: {{ $cash ? '#198754' : '#adb5bd' }};
                            position: relative; transition: background 0.3s;
                        ">
                            <div
                                style="
                                width: 20px; height: 20px; border-radius: 50%;
                                background: #fff; position: absolute; top: 3px;
                                left: {{ $cash ? '25px' : '3px' }};
                                transition: left 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,.3);
                            ">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bank Transfer --}}
        <div class="col-md-6 flex">
            <div
                class="card rounded-3 p-3 d-flex flex-row align-items-center justify-content-between
                {{ $bank_transfer ? 'border-success bg-success bg-opacity-10' : 'border-secondary' }}">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-university fs-4 {{ $bank_transfer ? 'text-success' : 'text-muted' }}"></i>
                    <div>
                        <p class="mb-0 fw-semibold">Bank Transfer</p>
                        <small class="{{ $bank_transfer ? 'text-success' : 'text-muted' }}">
                            {{ $bank_transfer ? 'Enabled' : 'Disabled' }}
                        </small>
                    </div>
                </div>
                <div wire:click="toggle('bank_transfer')" style="cursor:pointer">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small {{ $bank_transfer ? 'text-success fw-semibold' : 'text-muted' }}">
                            {{ $bank_transfer ? 'ON' : 'OFF' }}
                        </span>
                        <div
                            style="
                            width: 48px; height: 26px; border-radius: 13px;
                            background: {{ $bank_transfer ? '#198754' : '#adb5bd' }};
                            position: relative; transition: background 0.3s;
                        ">
                            <div
                                style="
                                width: 20px; height: 20px; border-radius: 50%;
                                background: #fff; position: absolute; top: 3px;
                                left: {{ $bank_transfer ? '25px' : '3px' }};
                                transition: left 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,.3);
                            ">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cheque --}}
        <div class="col-md-6 flex">
            <div
                class="card rounded-3 p-3 d-flex flex-row align-items-center justify-content-between
                {{ $cheque ? 'border-success bg-success bg-opacity-10' : 'border-secondary' }}">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-file-invoice-dollar fs-4 {{ $cheque ? 'text-success' : 'text-muted' }}"></i>
                    <div>
                        <p class="mb-0 fw-semibold">Cheque</p>
                        <small class="{{ $cheque ? 'text-success' : 'text-muted' }}">
                            {{ $cheque ? 'Enabled' : 'Disabled' }}
                        </small>
                    </div>
                </div>

                <div wire:click="toggle('cheque')" {{-- style="cursor:pointer" --}}
                    style="cursor: not-allowed; pointer-events: none; opacity: .6;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small {{ $cheque ? 'text-success fw-semibold' : 'text-muted' }}">
                            {{ $cheque ? 'ON' : 'OFF' }}
                        </span>
                        <div
                            style="
                            width: 48px; height: 26px; border-radius: 13px;
                            background: {{ $cheque ? '#198754' : '#adb5bd' }};
                            position: relative; transition: background 0.3s;
                        ">
                            <div
                                style="
                                width: 20px; height: 20px; border-radius: 50%;
                                background: #fff; position: absolute; top: 3px;
                                left: {{ $cheque ? '25px' : '3px' }};
                                transition: left 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,.3);
                            ">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Enable manual methods for paying dues --}}
        <div class="col-md-6 flex">
            <div
                class="card rounded-3 p-3 d-flex flex-row align-items-center justify-content-between
                {{ $cheque ? 'border-success bg-success bg-opacity-10' : 'border-secondary' }}">
                <div class="d-flex align-items-center gap-3">
                    <i
                        class="fas fa-file-invoice-dollar fs-4 {{ $allow_manual_dues ? 'text-success' : 'text-muted' }}"></i>
                    <div>
                        <p class="mb-0 fw-semibold">Enable manual methods for paying dues</p>
                        <small class="{{ $allow_manual_dues ? 'text-success' : 'text-muted' }}">
                            {{ $allow_manual_dues ? 'Enabled' : 'Disabled' }}
                        </small>
                    </div>
                </div>
                <div wire:click="toggle('allow_manual_dues')" style="cursor:pointer">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small {{ $allow_manual_dues ? 'text-success fw-semibold' : 'text-muted' }}">
                            {{ $allow_manual_dues ? 'ON' : 'OFF' }}
                        </span>
                        <div
                            style="
                            width: 48px; height: 26px; border-radius: 13px;
                            background: {{ $allow_manual_dues ? '#198754' : '#adb5bd' }};
                            position: relative; transition: background 0.3s;
                        ">
                            <div
                                style="
                                width: 20px; height: 20px; border-radius: 50%;
                                background: #fff; position: absolute; top: 3px;
                                left: {{ $allow_manual_dues ? '25px' : '3px' }};
                                transition: left 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,.3);
                            ">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div wire:loading class="mt-2 text-muted small">
        <i class="fas fa-spinner fa-spin me-1"></i> Saving...
    </div>
</div>
