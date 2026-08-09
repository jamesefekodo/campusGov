@forelse(
    $data['member']->memberAcademicInfo->pastProgramsAssociationDues()
    as $reAdmissionUuid => $dues
)

    @php
        $reAdmission = $dues->first()?->reAdmissionApplication;
    @endphp

    {{-- Past Programme Heading --}}
    @if ($reAdmission)
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h5 class="mb-2">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ $reAdmission->previousProgram?->name ?? $reAdmission->previous_program }}
                    </h5>

                    <div class="text-muted">
                        <strong>Admission:</strong>
                        {{ $reAdmission->previous_admission_session }}/{{ $reAdmission->previous_admission_session + 1 }}

                        <span class="mx-2">|</span>

                        <strong>Graduation:</strong>
                        {{ $reAdmission->previous_graduation_session }}/{{ $reAdmission->previous_graduation_session + 1 }}
                    </div>

                </div>
            </div>
        </div>
    @endif


    {{-- Dues for this past programme --}}
    @forelse($dues as $due)
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-0">

                {{-- Header --}}
                <div class="card-header bg-blue text-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0 text-white">
                        {{ $due->title }}
                    </h5>

                    <span class="badge bg-light text-dark">
                        {{ $due->academic_year }}/{{ $due->academic_year + 1 }}
                    </span>

                </div>


                {{-- Body --}}
                <div class="card-body">

                    @if ($due->description)
                        <p class="text-muted mb-4">
                            {{ $due->description }}
                        </p>
                    @endif


                    <div class="row">

                        {{-- Payable Amount --}}
                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Amount Payable
                            </small>

                            <h4 class="mb-0 text-success">
                                ₦{{ number_format($due->payable_amount, 2) }}
                            </h4>

                        </div>


                        {{-- Default Amount --}}
                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Default Amount
                            </small>

                            ₦{{ number_format($due->amount, 2) }}

                        </div>


                        {{-- Variation --}}
                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Variation Applied
                            </small>

                            @if ($due->matchedVariation)
                                <span class="badge bg-success">
                                    Yes
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    No
                                </span>
                            @endif

                        </div>

                    </div>


                    {{-- Applied Variation --}}
                    @if ($due->matchedVariation)
                        <hr>

                        <h6 class="mb-3">
                            Applied Variation
                        </h6>

                        <div class="row">

                            {{-- Admission Session --}}
                            <div class="col-md-4">

                                <strong>Admission Session</strong><br>

                                @if ($due->matchedVariation?->admission_session)
                                    {{ $due->matchedVariation->admission_session }}/{{ $due->matchedVariation->admission_session + 1 }}

                                    @php
                                        $year = $due->academic_year - $due->matchedVariation->admission_session + 1;
                                    @endphp

                                    -

                                    @if ($year == 1)
                                        (Freshers)
                                    @else
                                        (Year {{ $year }})
                                    @endif
                                @else
                                    All
                                @endif

                            </div>


                            {{-- Programme --}}
                            <div class="col-md-4">

                                <strong>Program</strong><br>

                                {{ $due->matchedVariation->program?->name ?? 'All' }}

                            </div>


                            {{-- Programme Type --}}
                            <div class="col-md-4">

                                <strong>Program Type</strong><br>

                                {{ $due->matchedVariation->programType?->type_name ?? 'All' }}

                            </div>

                        </div>
                    @endif


                    <hr>


                    {{-- Actions --}}
                    <div class="row">

                        <div class="col-12">

                            @if ($data['member']->successfulPaymentForDue($due->fee_id))
                                butttn to print reciept
                            @elseif($data['member']->pendingPaymentForDue($due->fee_id))
                                show button to view payment transaction and process
                            @else
                                <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">

                                    <button type="button" class="btn btn-warning me-3">
                                        <i class="fas fa-user-shield me-1"></i>
                                        Grant Exemption
                                    </button>

                                    <span class="fw-semibold text-muted">
                                        Already Paid?
                                    </span>

                                    <button type="button" class="btn btn-primary">
                                        <i class="fas fa-receipt me-1"></i>
                                        Regularize Payment
                                    </button>

                                </div>
                            @endif

                        </div>

                    </div>

                </div>

            </div>
        </div>

    @empty

        <div class="col-12">
            <div class="card border-0 shadow-sm">

                <div class="card-body text-center py-5">

                    <p class="text-muted mb-0">
                        No dues were applicable during this programme period.
                    </p>

                </div>

            </div>
        </div>
    @endforelse

@empty

    <div class="col-12">
        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <p class="text-muted mb-0">
                    No previous programme dues are available.
                </p>

            </div>

        </div>
    </div>

@endforelse
