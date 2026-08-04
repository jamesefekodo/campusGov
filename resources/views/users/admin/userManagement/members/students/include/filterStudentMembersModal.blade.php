<div class="modal fade" id="filterStudentMembersModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Filter Student Members</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="GET" action="{{ route('filterStudentMembers') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="faculty-select">
                                    Faculty
                                </label>

                                <select class="form-select" id="faculty-select" name="faculty">

                                    <option value="">- Select -</option>

                                    @foreach ($data['activeFaculties'] as $faculty)
                                        <option value="{{ $faculty->slug }}"
                                            {{ old('faculty', $data['selectedFaculty']) == $faculty->slug ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach

                                </select>

                                <p class="text-danger">{{ $errors->first('faculty') }}</p>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="department-select">
                                    Department
                                </label>

                                <select class="form-select" id="department-select" name="department" disabled>

                                    <option value="">- Select Faculty First -</option>

                                </select>

                                <p class="text-danger">{{ $errors->first('department') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="program-select">
                                    Program
                                </label>

                                <select class="form-select" id="program-select" name="program">

                                    <option value="">- Select -</option>

                                    @foreach ($data['activePrograms'] as $program)
                                        <option value="{{ $program->slug }}"
                                            {{ old('program', $data['selectedProgram']) == $program->slug ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <p class="text-danger">{{ $errors->first('program') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="program-type-select">
                                    Program Type
                                </label>

                                <select class="form-select" id="program-type-select" name="program_type" disabled>

                                    <option value="">- Select Program First -</option>
                                </select>

                                <p class="text-danger">{{ $errors->first('program_type') }}</p>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="level">Level:</label>
                                <select name="level" class="form-control">
                                    <option value="" selected>Select</option>

                                    @php
                                        $end = highestProgramDuration();
                                    @endphp

                                    @for ($year = 1; $year <= $end; $year++)
                                        <option value="{{ $year }}"
                                            {{ isset($data['selectedLevel']) && $data['selectedLevel'] == $year ? 'selected' : '' }}>
                                            Year {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                <p style="color: red">{{ $errors->first('level') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="gender">Gender</label>
                                <select class="form-select" name="gender">
                                    <option value="" selected>
                                        -Select-</option>
                                    <option value="male"
                                        {{ old('gender', $data['selectedGender']) == 'male' ? 'selected' : '' }}>
                                        Male
                                    </option>
                                    <option value="female"
                                        {{ old('gender', $data['selectedGender']) == 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                </select>
                                <p style="color: red">
                                    {{ $errors->first('gender') }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row g-2">
                            <div
                                class="{{ isset($data['requestType']) && $data['requestType'] === 'filter' ? 'col-md-9' : 'col-12' }}">
                                <button type="submit" class="green-button submit-btn w-100">
                                    <span class="btn-text">
                                        <i class="fas fa-filter me-2"></i>
                                        Apply Filter
                                    </span>

                                    <span class="btn-loading" style="display:none;">
                                        <i class="fas fa-spinner fa-spin"></i> Processing...
                                    </span>
                                </button>
                            </div>

                            @if (isset($data['requestType']) && $data['requestType'] === 'filter')
                                <div class="col-md-3">
                                    <a href="{{ route('studentMembers') }}" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-undo me-2"></i>
                                        Clear Filter
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                    {{-- </div> --}}


                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>





{{-- program select start --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const programSelect = document.getElementById('program-select');
        const programTypeSelect = document.getElementById('program-type-select');

        if (!programSelect || !programTypeSelect) return;

        const selectedProgramType =
            "{{ old('program_type', $data['selectedProgramType']) }}";

        function loadProgramTypes(programSlug, selected = '') {

            if (!programSlug) {
                programTypeSelect.innerHTML = '<option value="">- Select Program First -</option>';
                programTypeSelect.disabled = true;
                return;
            }

            programTypeSelect.disabled = true;
            programTypeSelect.innerHTML = '<option>Loading...</option>';

            fetch("{{ route('fetchProgramTypes') }}?program_slug=" + programSlug)
                .then(res => res.json())
                .then(res => {

                    programTypeSelect.innerHTML = '<option value="">- Select -</option>';

                    if (res.programTypes.length === 0) {

                        programTypeSelect.innerHTML +=
                            '<option disabled>No program types found</option>';

                    } else {

                        res.programTypes.forEach(type => {

                            programTypeSelect.innerHTML += `
                            <option value="${type.slug}"
                                ${selected === type.slug ? 'selected' : ''}>
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

        // Edit Mode
        if (programSelect.value) {
            loadProgramTypes(programSelect.value, selectedProgramType);
        }

    });
</script>
{{-- program select end --}}

{{-- department select start --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const facultySelect = document.getElementById('faculty-select');
        const departmentSelect = document.getElementById('department-select');

        if (!facultySelect || !departmentSelect) return;

        const selectedDepartment = "{{ old('department', $data['selectedDepartment']) }}";

        function loadDepartments(facultySlug, selected = '') {

            if (!facultySlug) {
                departmentSelect.innerHTML = '<option value="">- Select Faculty First -</option>';
                departmentSelect.disabled = true;
                return;
            }

            departmentSelect.disabled = true;
            departmentSelect.innerHTML = '<option>Loading...</option>';

            fetch("{{ route('fetchDepartments') }}?faculty_slug=" + facultySlug)
                .then(res => res.json())
                .then(res => {

                    departmentSelect.innerHTML = '<option value="">- Select -</option>';

                    if (res.departments.length === 0) {

                        departmentSelect.innerHTML +=
                            '<option disabled>No departments found</option>';

                    } else {

                        res.departments.forEach(department => {

                            departmentSelect.innerHTML += `
                            <option value="${department.slug}"
                                ${selected === department.slug ? 'selected' : ''}>
                                ${department.name}
                            </option>`;
                        });

                    }

                    departmentSelect.disabled = false;

                });
        }

        facultySelect.addEventListener('change', function() {
            loadDepartments(this.value);
        });

        // Edit Mode
        if (facultySelect.value) {
            loadDepartments(facultySelect.value, selectedDepartment);
        }

    });
</script>
{{-- department select end --}}
