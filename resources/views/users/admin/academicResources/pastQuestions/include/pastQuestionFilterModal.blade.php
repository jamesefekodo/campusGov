<div
    class="modal fade"
    id="pastQuestionFilterModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="pastQuestionFilterModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="pastQuestionFilterModalLabel">
                    Filter Past Questions
                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">

                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="GET" action="{{ route('pastQuestionsHome') }}">
                <div class="modal-body">

                    <div class="row">

                        <div class="form-group col-md-3">
                            <label for="past-question-faculty">
                                Faculty
                            </label>

                            <select
                                class="form-select form-control"
                                id="past-question-faculty"
                                name="faculty">

                                <option value="">All Faculties</option>

                                @foreach ($data['activeFaculties'] as $faculty)
                                    <option
                                        value="{{ $faculty->slug }}"
                                        {{ $data['selectedFaculty'] === $faculty->slug ? 'selected' : '' }}>

                                        {{ $faculty->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="past-question-department">
                                Department
                            </label>

                            <select
                                class="form-select form-control"
                                id="past-question-department"
                                name="department"
                                disabled>

                                <option value="">
                                    Select Faculty First
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="past-question-program">
                                Program
                            </label>

                            <select
                                class="form-select form-control"
                                id="past-question-program"
                                name="program">

                                <option value="">All Programs</option>

                                @foreach ($data['activePrograms'] as $program)
                                    <option
                                        value="{{ $program->slug }}"
                                        {{ $data['selectedProgram'] === $program->slug ? 'selected' : '' }}>

                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="past-question-program-type">
                                Program Type
                            </label>

                            <select
                                class="form-select form-control"
                                id="past-question-program-type"
                                name="program_type"
                                disabled>

                                <option value="">
                                    Select Program First
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="past-question-semester">
                                Semester
                            </label>

                            <select
                                class="form-select form-control"
                                id="past-question-semester"
                                name="semester">

                                <option value="">All Semesters</option>

                                <option
                                    value="first"
                                    {{ $data['selectedSemester'] === 'first' ? 'selected' : '' }}>

                                    First Semester
                                </option>

                                <option
                                    value="second"
                                    {{ $data['selectedSemester'] === 'second' ? 'selected' : '' }}>

                                    Second Semester
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="past-question-academic-year">
                                Academic Year
                            </label>

                            <select
                                class="form-select form-control"
                                id="past-question-academic-year"
                                name="academic_year">

                                <option value="">All Academic Years</option>

                                @foreach ($data['academicYears'] as $academicYear)
                                    <option
                                        value="{{ $academicYear }}"
                                        {{ (string) $data['selectedAcademicYear'] === (string) $academicYear ? 'selected' : '' }}>

                                        {{ $academicYear }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="past-question-type">
                                Question Type
                            </label>

                            <select
                                class="form-select form-control"
                                id="past-question-type"
                                name="question_type">

                                <option value="">Exam and Test</option>

                                <option
                                    value="exam"
                                    {{ $data['selectedQuestionType'] === 'exam' ? 'selected' : '' }}>

                                    Examination
                                </option>

                                <option
                                    value="test"
                                    {{ $data['selectedQuestionType'] === 'test' ? 'selected' : '' }}>

                                    Test
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="past-question-search">
                                Course / Keyword
                            </label>

                            <input
                                type="search"
                                class="form-control"
                                id="past-question-search"
                                name="searchKey"
                                value="{{ $data['searchKey'] }}"
                                placeholder="e.g. CSC 101">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <a
                        href="{{ route('pastQuestionsHome') }}"
                        class="btn btn-outline-danger">

                        <i class="fas fa-undo"></i>
                        Clear
                    </a>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-filter"></i>
                        Apply Filter
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const facultySelect = document.getElementById('past-question-faculty');
        const departmentSelect = document.getElementById('past-question-department');

        const programSelect = document.getElementById('past-question-program');
        const programTypeSelect = document.getElementById('past-question-program-type');

        const selectedDepartment = @json($data['selectedDepartment']);
        const selectedProgramType = @json($data['selectedProgramType']);

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value || '';
            return div.innerHTML;
        }

        async function loadDepartments(facultySlug, selected = '') {
            if (!facultySlug) {
                departmentSelect.innerHTML =
                    '<option value="">Select Faculty First</option>';

                departmentSelect.disabled = true;
                return;
            }

            departmentSelect.disabled = true;
            departmentSelect.innerHTML =
                '<option value="">Loading departments...</option>';

            try {
                const response = await fetch(
                    "{{ route('fetchDepartments') }}" +
                    "?faculty_slug=" + encodeURIComponent(facultySlug)
                );

                if (!response.ok) {
                    throw new Error('Could not load departments');
                }

                const result = await response.json();

                departmentSelect.innerHTML =
                    '<option value="">All Departments</option>';

                if (!result.departments || result.departments.length === 0) {
                    departmentSelect.innerHTML +=
                        '<option value="" disabled>No departments found</option>';
                } else {
                    result.departments.forEach(function (department) {
                        const isSelected = selected === department.slug
                            ? ' selected'
                            : '';

                        departmentSelect.innerHTML +=
                            '<option value="' + escapeHtml(department.slug) + '"' +
                            isSelected + '>' +
                            escapeHtml(department.name) +
                            '</option>';
                    });
                }

                departmentSelect.disabled = false;
            } catch (error) {
                departmentSelect.innerHTML =
                    '<option value="">Unable to load departments</option>';

                departmentSelect.disabled = true;
            }
        }

        async function loadProgramTypes(programSlug, selected = '') {
            if (!programSlug) {
                programTypeSelect.innerHTML =
                    '<option value="">Select Program First</option>';

                programTypeSelect.disabled = true;
                return;
            }

            programTypeSelect.disabled = true;
            programTypeSelect.innerHTML =
                '<option value="">Loading program types...</option>';

            try {
                const response = await fetch(
                    "{{ route('fetchProgramTypes') }}" +
                    "?program_slug=" + encodeURIComponent(programSlug)
                );

                if (!response.ok) {
                    throw new Error('Could not load program types');
                }

                const result = await response.json();

                programTypeSelect.innerHTML =
                    '<option value="">All Program Types</option>';

                if (!result.programTypes || result.programTypes.length === 0) {
                    programTypeSelect.innerHTML +=
                        '<option value="" disabled>No program types found</option>';
                } else {
                    result.programTypes.forEach(function (programType) {
                        const isSelected = selected === programType.slug
                            ? ' selected'
                            : '';

                        programTypeSelect.innerHTML +=
                            '<option value="' + escapeHtml(programType.slug) + '"' +
                            isSelected + '>' +
                            escapeHtml(programType.type_name) +
                            '</option>';
                    });
                }

                programTypeSelect.disabled = false;
            } catch (error) {
                programTypeSelect.innerHTML =
                    '<option value="">Unable to load program types</option>';

                programTypeSelect.disabled = true;
            }
        }

        facultySelect.addEventListener('change', function () {
            loadDepartments(this.value);
        });

        programSelect.addEventListener('change', function () {
            loadProgramTypes(this.value);
        });

        if (facultySelect.value) {
            loadDepartments(
                facultySelect.value,
                selectedDepartment
            );
        }

        if (programSelect.value) {
            loadProgramTypes(
                programSelect.value,
                selectedProgramType
            );
        }
    });
</script>