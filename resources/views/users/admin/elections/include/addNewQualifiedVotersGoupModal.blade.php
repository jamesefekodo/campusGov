<div class="modal fade" id="addNewQualifiedVotersGoupModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Voter Group</h4>
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
                        action="{{ route('addNewQualifiedVoterGroupSubmit', ['uuid' => $data['election']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="form-group col-md-4">
                                <label for="addVoterGroup-program-select">
                                    Program <span class="text-red">*</span>
                                </label>

                                <select class="form-select" id="addVoterGroup-program-select" name="program"
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
                                <label for="addVoterGroup-program-type-select">
                                    Program Type
                                </label>

                                <select class="form-select" id="addVoterGroup-program-type-select"
                                    name="program_type" disabled>
                                    <option value="">- Select Program First -</option>
                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('program_type') }}
                                </p>
                            </div>


                            <div class="form-group col-md-4">
                                <label for="addVoterGroup-faculty-select">
                                    Faculty
                                </label>

                                <select class="form-select" id="addVoterGroup-faculty-select" name="faculty">
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
                                <label for="addVoterGroup-department-select">
                                    Department
                                </label>

                                <select class="form-select" id="addVoterGroup-department-select" name="department"
                                    disabled>
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
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const addVoterGroupProgramSelect =
                                document.getElementById('addVoterGroup-program-select');

                            const addVoterGroupProgramTypeSelect =
                                document.getElementById('addVoterGroup-program-type-select');

                            if (
                                !addVoterGroupProgramSelect ||
                                !addVoterGroupProgramTypeSelect
                            ) {
                                return;
                            }

                            const addVoterGroupSelectedProgramType =
                                @json(old('program_type'));

                            function addVoterGroupLoadProgramTypes(
                                programSlug,
                                selected = ''
                            ) {

                                if (!programSlug) {

                                    addVoterGroupProgramTypeSelect.innerHTML =
                                        '<option value="">- Select Program First -</option>';

                                    addVoterGroupProgramTypeSelect.disabled = true;

                                    return;
                                }

                                addVoterGroupProgramTypeSelect.disabled = true;

                                addVoterGroupProgramTypeSelect.innerHTML =
                                    '<option value="">Loading...</option>';

                                fetch(
                                        "{{ route('fetchProgramTypes') }}?program_slug=" +
                                        encodeURIComponent(programSlug)
                                    )
                                    .then(res => res.json())
                                    .then(res => {

                                        addVoterGroupProgramTypeSelect.innerHTML =
                                            '<option value="">- Select -</option>';

                                        if (res.programTypes.length === 0) {

                                            addVoterGroupProgramTypeSelect.innerHTML +=
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

                                                addVoterGroupProgramTypeSelect
                                                    .appendChild(option);

                                            });
                                        }

                                        addVoterGroupProgramTypeSelect.disabled = false;

                                    })
                                    .catch(error => {

                                        console.error(
                                            'Error loading candidate program types:',
                                            error
                                        );

                                        addVoterGroupProgramTypeSelect.innerHTML =
                                            '<option value="">Unable to load program types</option>';

                                        addVoterGroupProgramTypeSelect.disabled = false;

                                    });
                            }


                            addVoterGroupProgramSelect.addEventListener(
                                'change',
                                function() {

                                    addVoterGroupLoadProgramTypes(
                                        this.value
                                    );

                                }
                            );


                            // Restore old input after validation failure
                            if (addVoterGroupProgramSelect.value) {

                                addVoterGroupLoadProgramTypes(
                                    addVoterGroupProgramSelect.value,
                                    addVoterGroupSelectedProgramType
                                );

                            }

                        });
                    </script>


                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const addVoterGroupFacultySelect =
                                document.getElementById('addVoterGroup-faculty-select');

                            const addVoterGroupDepartmentSelect =
                                document.getElementById('addVoterGroup-department-select');

                            if (
                                !addVoterGroupFacultySelect ||
                                !addVoterGroupDepartmentSelect
                            ) {
                                return;
                            }

                            const addVoterGroupSelectedDepartment =
                                @json(old('department'));


                            function addVoterGroupLoadDepartments(
                                facultySlug,
                                selected = ''
                            ) {

                                if (!facultySlug) {

                                    addVoterGroupDepartmentSelect.innerHTML =
                                        '<option value="">- Select Faculty First -</option>';

                                    addVoterGroupDepartmentSelect.disabled = true;

                                    return;
                                }

                                addVoterGroupDepartmentSelect.disabled = true;

                                addVoterGroupDepartmentSelect.innerHTML =
                                    '<option value="">Loading...</option>';


                                fetch(
                                        "{{ route('fetchDepartments') }}?faculty_slug=" +
                                        encodeURIComponent(facultySlug)
                                    )
                                    .then(res => res.json())
                                    .then(res => {

                                        addVoterGroupDepartmentSelect.innerHTML =
                                            '<option value="">- Select -</option>';


                                        if (res.departments.length === 0) {

                                            addVoterGroupDepartmentSelect.innerHTML +=
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

                                                addVoterGroupDepartmentSelect
                                                    .appendChild(option);

                                            });

                                        }

                                        addVoterGroupDepartmentSelect.disabled = false;

                                    })
                                    .catch(error => {

                                        console.error(
                                            'Error loading candidate departments:',
                                            error
                                        );

                                        addVoterGroupDepartmentSelect.innerHTML =
                                            '<option value="">Unable to load departments</option>';

                                        addVoterGroupDepartmentSelect.disabled = false;

                                    });

                            }


                            addVoterGroupFacultySelect.addEventListener(
                                'change',
                                function() {

                                    addVoterGroupLoadDepartments(
                                        this.value
                                    );

                                }
                            );


                            // Restore old input after validation failure
                            if (addVoterGroupFacultySelect.value) {

                                addVoterGroupLoadDepartments(
                                    addVoterGroupFacultySelect.value,
                                    addVoterGroupSelectedDepartment
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
