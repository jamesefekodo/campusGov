<div class="modal fade" id="editQualifiedCandidateGoupModal_{{ $group->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Candidate Group | {{ $position->position }}</h4>
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
                        action="{{ route('updateQualifiedCandidateGroupSubmit', ['uuid' => $group->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">

                            @php
                                $editCandidateGroupId = $group->id;

                                $selectedProgram = old('program', $group->program);
                                $selectedProgramType = old('program_type', $group->program_type);
                                $selectedFaculty = old('faculty', $group->faculty);
                                $selectedDepartment = old('department', $group->department);
                            @endphp

                            <div class="form-group col-md-4">
                                <label for="editCandidateGroup-program-select-{{ $editCandidateGroupId }}">
                                    Program <span class="text-red">*</span>
                                </label>

                                <select class="form-select"
                                    id="editCandidateGroup-program-select-{{ $editCandidateGroupId }}" name="program"
                                    required>
                                    <option value="">- All -</option>

                                    @foreach ($data['activeProgram'] as $program)
                                        <option value="{{ $program->slug }}"
                                            {{ $selectedProgram == $program->slug ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('program') }}
                                </p>
                            </div>


                            <div class="form-group col-md-4">
                                <label for="editCandidateGroup-program-type-select-{{ $editCandidateGroupId }}">
                                    Program Type
                                </label>

                                <select class="form-select"
                                    id="editCandidateGroup-program-type-select-{{ $editCandidateGroupId }}"
                                    name="program_type" disabled>
                                    <option value="">- Select Program First -</option>
                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('program_type') }}
                                </p>
                            </div>


                            <div class="form-group col-md-4">
                                <label for="editCandidateGroup-faculty-select-{{ $editCandidateGroupId }}">
                                    Faculty
                                </label>

                                <select class="form-select"
                                    id="editCandidateGroup-faculty-select-{{ $editCandidateGroupId }}" name="faculty">
                                    <option value="">- All -</option>

                                    @foreach ($data['activeFaculties'] as $faculty)
                                        <option value="{{ $faculty->slug }}"
                                            {{ $selectedFaculty == $faculty->slug ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('faculty') }}
                                </p>
                            </div>


                            <div class="form-group col-md-4">
                                <label for="editCandidateGroup-department-select-{{ $editCandidateGroupId }}">
                                    Department
                                </label>

                                <select class="form-select"
                                    id="editCandidateGroup-department-select-{{ $editCandidateGroupId }}"
                                    name="department" disabled>
                                    <option value="">- Select Faculty First -</option>
                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('department') }}
                                </p>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="start_level">
                                    Level (From):
                                </label>

                                <select name="start_level" class="form-control">
                                    <option value="" selected>All</option>

                                    @php
                                        $startingLevel = max(
                                            1,
                                            currentAcademicYear() - $data['election']->academic_session + 1,
                                        );

                                        $endingLevel = highestProgramDuration();
                                    @endphp

                                    @for ($level = $startingLevel; $level <= $endingLevel; $level++)
                                        <option value="{{ $level }}"
                                            {{ $group->start_level == $level ? 'selected' : '' }}>
                                            Year {{ $level }}
                                        </option>
                                    @endfor

                                </select>

                                <p style="color: red">
                                    {{ $errors->first('start_level') }}
                                </p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="end_level">
                                    <label for="end_level">
                                        Level (To)
                                        <span class="fs-6 text-muted">
                                            (Leave blank if not applicable)
                                        </span>:
                                    </label>

                                    <select name="end_level" class="form-control">
                                        <option value="" selected>All</option>

                                        @php
                                            $startingLevel = max(
                                                1,
                                                currentAcademicYear() - $data['election']->academic_session + 1,
                                            );

                                            $endingLevel = highestProgramDuration();
                                        @endphp

                                        @for ($level = $startingLevel; $level <= $endingLevel; $level++)
                                            <option value="{{ $level }}"
                                                {{ $group->end_level == $level ? 'selected' : '' }}>
                                                Year {{ $level }}
                                            </option>
                                        @endfor

                                    </select>

                                    <p style="color: red">
                                        {{ $errors->first('end_level') }}
                                    </p>
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


                    {{-- scripts start --}}
                    {{-- Add Candidate Group scripts --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const groupId = "{{ $editCandidateGroupId }}";

                            const programSelect =
                                document.getElementById(
                                    'editCandidateGroup-program-select-' + groupId
                                );

                            const programTypeSelect =
                                document.getElementById(
                                    'editCandidateGroup-program-type-select-' + groupId
                                );

                            const facultySelect =
                                document.getElementById(
                                    'editCandidateGroup-faculty-select-' + groupId
                                );

                            const departmentSelect =
                                document.getElementById(
                                    'editCandidateGroup-department-select-' + groupId
                                );

                            if (
                                !programSelect ||
                                !programTypeSelect ||
                                !facultySelect ||
                                !departmentSelect
                            ) {
                                return;
                            }


                            const selectedProgramType =
                                @json($selectedProgramType);

                            const selectedDepartment =
                                @json($selectedDepartment);


                            // =========================
                            // PROGRAM → PROGRAM TYPE
                            // =========================

                            function editCandidateGroupLoadProgramTypes(
                                programSlug,
                                selected = ''
                            ) {

                                if (!programSlug) {

                                    programTypeSelect.innerHTML =
                                        '<option value="">- Select Program First -</option>';

                                    programTypeSelect.disabled = true;

                                    return;
                                }

                                programTypeSelect.disabled = true;

                                programTypeSelect.innerHTML =
                                    '<option value="">Loading...</option>';


                                fetch(
                                        "{{ route('fetchProgramTypes') }}?program_slug=" +
                                        encodeURIComponent(programSlug)
                                    )
                                    .then(response => response.json())
                                    .then(response => {

                                        programTypeSelect.innerHTML =
                                            '<option value="">- All -</option>';


                                        if (response.programTypes.length === 0) {

                                            programTypeSelect.innerHTML +=
                                                '<option disabled>No program types found</option>';

                                        } else {

                                            response.programTypes.forEach(type => {

                                                const option =
                                                    document.createElement('option');

                                                option.value = type.slug;
                                                option.textContent = type.type_name;

                                                if (selected === type.slug) {
                                                    option.selected = true;
                                                }

                                                programTypeSelect.appendChild(option);

                                            });

                                        }

                                        programTypeSelect.disabled = false;

                                    })
                                    .catch(error => {

                                        console.error(
                                            'Error loading candidate program types:',
                                            error
                                        );

                                        programTypeSelect.innerHTML =
                                            '<option value="">Unable to load program types</option>';

                                        programTypeSelect.disabled = false;

                                    });
                            }


                            programSelect.addEventListener('change', function() {

                                editCandidateGroupLoadProgramTypes(
                                    this.value
                                );

                            });


                            // =========================
                            // FACULTY → DEPARTMENT
                            // =========================

                            function editCandidateGroupLoadDepartments(
                                facultySlug,
                                selected = ''
                            ) {

                                if (!facultySlug) {

                                    departmentSelect.innerHTML =
                                        '<option value="">- Select Faculty First -</option>';

                                    departmentSelect.disabled = true;

                                    return;
                                }

                                departmentSelect.disabled = true;

                                departmentSelect.innerHTML =
                                    '<option value="">Loading...</option>';


                                fetch(
                                        "{{ route('fetchDepartments') }}?faculty_slug=" +
                                        encodeURIComponent(facultySlug)
                                    )
                                    .then(response => response.json())
                                    .then(response => {

                                        departmentSelect.innerHTML =
                                            '<option value="">- All -</option>';


                                        if (response.departments.length === 0) {

                                            departmentSelect.innerHTML +=
                                                '<option disabled>No departments found</option>';

                                        } else {

                                            response.departments.forEach(department => {

                                                const option =
                                                    document.createElement('option');

                                                option.value = department.slug;
                                                option.textContent = department.name;

                                                if (selected === department.slug) {
                                                    option.selected = true;
                                                }

                                                departmentSelect.appendChild(option);

                                            });

                                        }

                                        departmentSelect.disabled = false;

                                    })
                                    .catch(error => {

                                        console.error(
                                            'Error loading candidate departments:',
                                            error
                                        );

                                        departmentSelect.innerHTML =
                                            '<option value="">Unable to load departments</option>';

                                        departmentSelect.disabled = false;

                                    });

                            }


                            facultySelect.addEventListener('change', function() {

                                editCandidateGroupLoadDepartments(
                                    this.value
                                );

                            });


                            // =========================
                            // INITIAL LOAD
                            // =========================

                            if (programSelect.value) {

                                editCandidateGroupLoadProgramTypes(
                                    programSelect.value,
                                    selectedProgramType
                                );

                            }


                            if (facultySelect.value) {

                                editCandidateGroupLoadDepartments(
                                    facultySelect.value,
                                    selectedDepartment
                                );

                            }

                        });
                    </script>
                    {{-- scripts end --}}

                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>
