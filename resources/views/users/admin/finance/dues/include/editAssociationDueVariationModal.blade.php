<div class="modal fade" id="editAssociationDueVsriationModal_{{ $variation->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Due Variation</h4>
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
                        action="{{ route('editAssociationDueVariation', ['id' => $variation->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="admission_session">Admission Session: <span
                                        class="small text-mute text-danger">(Leave this field blank if it applies to
                                        all.)</span></label>
                                <select name="admission_session" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                    @php
                                        $startYear = $data['due']->academic_year;
                                        $endYear = max(0, $startYear - 10);
                                    @endphp

                                    @for ($year = $startYear; $year >= $endYear; $year--)
                                        <option value="{{ $year }}"
                                            {{ $variation->admission_session == $year ? 'selected' : '' }}>
                                            {{ $year }}/{{ $year + 1 }}
                                            @if ($year == $startYear)
                                                (Freshers)
                                            @endif
                                        </option>
                                    @endfor
                                </select>
                                <p style="color: red">{{ $errors->first('admission_session') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="program-select-{{ $variation->id }}">
                                    Program
                                </label>

                                <select class="form-select" id="program-select-{{ $variation->id }}"
                                    name="program_slug">

                                    <option value="">- Select -</option>

                                    @foreach ($data['activePrograms'] as $program)
                                        <option value="{{ $program->slug }}"
                                            {{ old('program_slug', $variation->program_slug) == $program->slug ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="program-type-select-{{ $variation->id }}">
                                    Program Type
                                </label>

                                <select class="form-select" id="program-type-select-{{ $variation->id }}"
                                    name="program_type_slug" disabled>

                                    <option value="">- Select Program First -</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="amount">Amount (₦)<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="amount"
                                    value="{{ $variation->amount }}" required>
                                <p style="color: red">{{ $errors->first('amount') }}</p>
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


<script>
    document.addEventListener('DOMContentLoaded', function() {

        (function() {

            const variationId = "{{ $variation->id }}";

            const programSelect = document.getElementById(
                'program-select-' + variationId
            );

            const programTypeSelect = document.getElementById(
                'program-type-select-' + variationId
            );

            if (!programSelect || !programTypeSelect) return;

            const selectedProgramType =
                "{{ old('program_type_slug', $variation->program_type_slug) }}";

            function loadProgramTypes(programSlug, selected = '') {

                if (!programSlug) {
                    programTypeSelect.innerHTML =
                        '<option value="">- Select Program First -</option>';
                    programTypeSelect.disabled = true;
                    return;
                }

                programTypeSelect.disabled = true;
                programTypeSelect.innerHTML =
                    '<option>Loading...</option>';

                fetch("{{ route('fetchProgramTypes') }}?program_slug=" + programSlug)
                    .then(response => response.json())
                    .then(response => {

                        programTypeSelect.innerHTML =
                            '<option value="">- Select -</option>';

                        if (response.programTypes.length === 0) {

                            programTypeSelect.innerHTML +=
                                '<option disabled>No program types found</option>';

                        } else {

                            response.programTypes.forEach(type => {

                                programTypeSelect.innerHTML += `
                                <option value="${type.slug}"
                                    ${selected == type.slug ? 'selected' : ''}>
                                    ${type.type_name}
                                </option>`;
                            });
                        }

                        programTypeSelect.disabled = false;
                    });
            }

            programSelect.addEventListener('change', function() {
                loadProgramTypes(this.value);
            });

            if (programSelect.value) {
                loadProgramTypes(programSelect.value, selectedProgramType);
            }

        })();

    });
</script>
