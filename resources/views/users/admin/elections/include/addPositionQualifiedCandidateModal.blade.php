<div class="modal fade" id="addPositionQualifiedCandidateModal_{{ $position->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Candidate Group | {{ $position->position }}</h4>
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
                        action="{{ route('addNewPositionQualifiedCandidateGroupSubmit', ['uuid' => $position->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            {{-- ########################################################################### --}}
                            <!-- Program -->
                            <div class="form-group col-md-4">
                                <label for="addCandidateGroup-program-select_{{ $position->id }}">
                                    Program <span class="text-red">*</span>
                                </label>

                                <select class="form-select addCandidateGroup-program-select"
                                    data-position-id="{{ $position->id }}" name="program" required>
                                    <option value="">- Select -</option>
                                    @foreach ($data['activeProgram'] as $program)
                                        <option value="{{ $program->slug }}"
                                            {{ old('program') == $program->slug ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <p class="text-danger">{{ $errors->first('program') }}</p>
                            </div>

                            <!-- Program Type -->
                            <div class="form-group col-md-4">
                                <label for="addCandidateGroup-program-type-select_{{ $position->id }}">
                                    Program Type
                                </label>

                                <select class="form-select addCandidateGroup-program-type-select"
                                    data-position-id="{{ $position->id }}" name="program_type" disabled>
                                    <option value="">- Select Program First -</option>
                                </select>

                                <p class="text-danger">{{ $errors->first('program_type') }}</p>
                            </div>

                            <!-- Faculty -->
                            <div class="form-group col-md-4">
                                <label for="addCandidateGroup-faculty-select_{{ $position->id }}">
                                    Faculty
                                </label>

                                <select class="form-select addCandidateGroup-faculty-select"
                                    data-position-id="{{ $position->id }}" name="faculty">
                                    <option value="">- Select -</option>
                                    @foreach ($data['activeFaculties'] as $faculty)
                                        <option value="{{ $faculty->slug }}"
                                            {{ old('faculty') == $faculty->slug ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <p class="text-danger">{{ $errors->first('faculty') }}</p>
                            </div>

                            <!-- Department -->
                            <div class="form-group col-md-4">
                                <label for="addCandidateGroup-department-select_{{ $position->id }}">
                                    Department
                                </label>

                                <select class="form-select addCandidateGroup-department-select"
                                    data-position-id="{{ $position->id }}" name="department" disabled>
                                    <option value="">- Select Faculty First -</option>
                                </select>

                                <p class="text-danger">{{ $errors->first('department') }}</p>
                            </div>

                            {{-- ##################################################################################### --}}

                            {{-- <div class="form-group col-md-4">
                                <label for="addCandidateGroup-program-select">
                                    Program <span class="text-red">*</span>
                                </label>

                                <select class="form-select" id="addCandidateGroup-program-select" name="program"
                                    required>
                                    <option value="">- Select -</option>

                                    @foreach ($data['activeProgram'] as $program)
                                        <option value="{{ $program->slug }}"
                                            {{ old('program') == $program->slug ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('program') }}
                                </p>
                            </div>


                            <div class="form-group col-md-4">
                                <label for="addCandidateGroup-program-type-select">
                                    Program Type
                                </label>

                                <select class="form-select" id="addCandidateGroup-program-type-select"
                                    name="program_type" disabled>
                                    <option value="">- Select Program First -</option>
                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('program_type') }}
                                </p>
                            </div>


                            <div class="form-group col-md-4">
                                <label for="addCandidateGroup-faculty-select">
                                    Faculty
                                </label>

                                <select class="form-select" id="addCandidateGroup-faculty-select" name="faculty">
                                    <option value="">- Select -</option>

                                    @foreach ($data['activeFaculties'] as $faculty)
                                        <option value="{{ $faculty->slug }}"
                                            {{ old('faculty') == $faculty->slug ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach

                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('faculty') }}
                                </p>
                            </div>


                            <div class="form-group col-md-4">
                                <label for="addCandidateGroup-department-select">
                                    Department
                                </label>

                                <select class="form-select" id="addCandidateGroup-department-select" name="department"
                                    disabled>
                                    <option value="">- Select Faculty First -</option>
                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('department') }}
                                </p>
                            </div> --}}

                            <div class="form-group col-md-4">
                                <label for="start_level">
                                    Level (From):
                                </label>

                                <select name="start_level" class="form-control">
                                    <option value="" selected>Select</option>

                                    @php
                                        $startingLevel = max(
                                            1,
                                            currentAcademicYear() - $data['election']->academic_session + 1,
                                        );

                                        $endingLevel = highestProgramDuration();
                                    @endphp

                                    @for ($level = $startingLevel; $level <= $endingLevel; $level++)
                                        <option value="{{ $level }}"
                                            {{ old('start_level') == $level ? 'selected' : '' }}>
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
                                        <option value="" selected>Select</option>

                                        @php
                                            $startingLevel = max(
                                                1,
                                                currentAcademicYear() - $data['election']->academic_session + 1,
                                            );

                                            $endingLevel = highestProgramDuration();
                                        @endphp

                                        @for ($level = $startingLevel; $level <= $endingLevel; $level++)
                                            <option value="{{ $level }}"
                                                {{ old('end_level') == $level ? 'selected' : '' }}>
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
                    {{-- <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const addCandidateGroupProgramSelect =
                                document.getElementById('addCandidateGroup-program-select');

                            const addCandidateGroupProgramTypeSelect =
                                document.getElementById('addCandidateGroup-program-type-select');

                            if (
                                !addCandidateGroupProgramSelect ||
                                !addCandidateGroupProgramTypeSelect
                            ) {
                                return;
                            }

                            const addCandidateGroupSelectedProgramType =
                                @json(old('program_type'));

                            function addCandidateGroupLoadProgramTypes(
                                programSlug,
                                selected = ''
                            ) {

                                if (!programSlug) {

                                    addCandidateGroupProgramTypeSelect.innerHTML =
                                        '<option value="">- Select Program First -</option>';

                                    addCandidateGroupProgramTypeSelect.disabled = true;

                                    return;
                                }

                                addCandidateGroupProgramTypeSelect.disabled = true;

                                addCandidateGroupProgramTypeSelect.innerHTML =
                                    '<option value="">Loading...</option>';

                                fetch(
                                        "{{ route('fetchProgramTypes') }}?program_slug=" +
                                        encodeURIComponent(programSlug)
                                    )
                                    .then(res => res.json())
                                    .then(res => {

                                        addCandidateGroupProgramTypeSelect.innerHTML =
                                            '<option value="">- Select -</option>';

                                        if (res.programTypes.length === 0) {

                                            addCandidateGroupProgramTypeSelect.innerHTML +=
                                                '<option disabled>No program types found</option>';

                                        } else {

                                            res.programTypes.forEach(type => {

                                                const option =
                                                    document.createElement('option');

                                                option.value = type.slug;
                                                option.textContent = type.type_name;

                                                if (selected === type.slug) {
                                                    option.selected = true;
                                                }

                                                addCandidateGroupProgramTypeSelect
                                                    .appendChild(option);

                                            });
                                        }

                                        addCandidateGroupProgramTypeSelect.disabled = false;

                                    })
                                    .catch(error => {

                                        console.error(
                                            'Error loading candidate program types:',
                                            error
                                        );

                                        addCandidateGroupProgramTypeSelect.innerHTML =
                                            '<option value="">Unable to load program types</option>';

                                        addCandidateGroupProgramTypeSelect.disabled = false;

                                    });
                            }


                            addCandidateGroupProgramSelect.addEventListener(
                                'change',
                                function() {

                                    addCandidateGroupLoadProgramTypes(
                                        this.value
                                    );

                                }
                            );


                            // Restore old input after validation failure
                            if (addCandidateGroupProgramSelect.value) {

                                addCandidateGroupLoadProgramTypes(
                                    addCandidateGroupProgramSelect.value,
                                    addCandidateGroupSelectedProgramType
                                );

                            }

                        });
                    </script>


                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const addCandidateGroupFacultySelect =
                                document.getElementById('addCandidateGroup-faculty-select');

                            const addCandidateGroupDepartmentSelect =
                                document.getElementById('addCandidateGroup-department-select');

                            if (
                                !addCandidateGroupFacultySelect ||
                                !addCandidateGroupDepartmentSelect
                            ) {
                                return;
                            }

                            const addCandidateGroupSelectedDepartment =
                                @json(old('department'));


                            function addCandidateGroupLoadDepartments(
                                facultySlug,
                                selected = ''
                            ) {

                                if (!facultySlug) {

                                    addCandidateGroupDepartmentSelect.innerHTML =
                                        '<option value="">- Select Faculty First -</option>';

                                    addCandidateGroupDepartmentSelect.disabled = true;

                                    return;
                                }

                                addCandidateGroupDepartmentSelect.disabled = true;

                                addCandidateGroupDepartmentSelect.innerHTML =
                                    '<option value="">Loading...</option>';


                                fetch(
                                        "{{ route('fetchDepartments') }}?faculty_slug=" +
                                        encodeURIComponent(facultySlug)
                                    )
                                    .then(res => res.json())
                                    .then(res => {

                                        addCandidateGroupDepartmentSelect.innerHTML =
                                            '<option value="">- Select -</option>';


                                        if (res.departments.length === 0) {

                                            addCandidateGroupDepartmentSelect.innerHTML +=
                                                '<option disabled>No departments found</option>';

                                        } else {

                                            res.departments.forEach(department => {

                                                const option =
                                                    document.createElement('option');

                                                option.value = department.slug;
                                                option.textContent = department.name;

                                                if (selected === department.slug) {
                                                    option.selected = true;
                                                }

                                                addCandidateGroupDepartmentSelect
                                                    .appendChild(option);

                                            });

                                        }

                                        addCandidateGroupDepartmentSelect.disabled = false;

                                    })
                                    .catch(error => {

                                        console.error(
                                            'Error loading candidate departments:',
                                            error
                                        );

                                        addCandidateGroupDepartmentSelect.innerHTML =
                                            '<option value="">Unable to load departments</option>';

                                        addCandidateGroupDepartmentSelect.disabled = false;

                                    });

                            }


                            addCandidateGroupFacultySelect.addEventListener(
                                'change',
                                function() {

                                    addCandidateGroupLoadDepartments(
                                        this.value
                                    );

                                }
                            );


                            // Restore old input after validation failure
                            if (addCandidateGroupFacultySelect.value) {

                                addCandidateGroupLoadDepartments(
                                    addCandidateGroupFacultySelect.value,
                                    addCandidateGroupSelectedDepartment
                                );

                            }

                        });
                    </script> --}}
                    {{-- scripts end --}}

                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>
