{{-- reAdmissionRequest --}}
@extends('layouts.userLayout')
@section('title', 'Apply for Re-Admission')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">
                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Apply for Re-Admission
                                </h4>
                                <div class="card-body">
                                    <p class="text-bold text-black text-decoration-underline fs-4 ms-3 mb-0 pb-0">Current
                                        Record</p>
                                    <div class="p-4 row col-md-12 pt-0">
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Matric Number:</span>
                                            <span class="text-black text-uppercase">
                                                {{ $data['member']?->memberAcademicInfo?->matric_number }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Level:</span>
                                            <span class="text-black">
                                                @php
                                                    $academic = $data['member']?->memberAcademicInfo;
                                                @endphp

                                                <span style="font-weight: bold">
                                                    @if ($academic?->academic_status == 'student')
                                                        {{-- Current Level: --}}
                                                    @elseif($academic?->academic_status == 'alumni')
                                                        Graduated:
                                                    @else
                                                        {{ $academic?->academic_status }}
                                                    @endif
                                                </span>

                                                @if ($academic?->academic_status == 'student')

                                                    {{ currentLevel($academic?->theProgram, studentCurrentLevel($academic)) }}
                                                @elseif($academic?->academic_status == 'alumni')
                                                    {{ $academic->graduation_session
                                                        ? $academic->graduation_session . '/' . ($academic->graduation_session + 1)
                                                        : '-' }}
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $academic?->academic_status ?? '-')) }}

                                                    @if ($academic?->graduation_session)
                                                        ({{ $academic->graduation_session }}/{{ $academic->graduation_session + 1 }})
                                                    @endif

                                                @endif
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Academic Status:</span>
                                            <span class="text-black">
                                                @switch($data['member']?->memberAcademicInfo?->academic_status)
                                                    @case('student')
                                                        Student
                                                    @break

                                                    @case('extra_year')
                                                        Extra Year
                                                    @break

                                                    @case('alumni')
                                                        Alumni
                                                    @break

                                                    @case('withdrawn')
                                                        Withdrawn
                                                    @break

                                                    @case('suspended')
                                                        Suspended
                                                    @break

                                                    @case('expelled')
                                                        Expelled
                                                    @break

                                                    @default
                                                        Unknown
                                                @endswitch
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Program:</span>
                                            <span class="text-black">
                                                {{ $data['member']?->memberAcademicInfo?->theProgram?->name }}
                                                @if ($data['member']?->memberAcademicInfo?->theProgram?->short_name)
                                                    ({{ $data['member']?->memberAcademicInfo?->theProgram?->short_name }})
                                                @endif
                                                - {{ $data['member']?->memberAcademicInfo?->programType?->type_name }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Duration:</span>
                                            <span class="text-black">
                                                @php
                                                    $academic = $data['member']?->memberAcademicInfo;
                                                @endphp

                                                {{ getProgramDuration($academic?->theProgram, $academic?->programType, $academic?->theDepartment) }}
                                                Years
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Faculty:</span>
                                            <span class="text-black">
                                                {{ $data['member']?->memberAcademicInfo?->theFaculty?->name }}
                                                @if ($data['member']?->memberAcademicInfo?->theFaculty?->acronym)
                                                    ({{ $data['member']?->memberAcademicInfo?->theFaculty?->acronym }})
                                                @endif
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Department:</span>
                                            <span class="text-black">
                                                {{ $data['member']?->memberAcademicInfo?->theDepartment?->name }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Admission Session:</span>
                                            <span class="text-black">
                                                {{ $data['member']?->memberAcademicInfo?->admission_session }}/{{ $data['member']?->memberAcademicInfo?->admission_session + 1 }}
                                            </span>
                                        </div>
                                        @if ($data['member']?->memberAcademicInfo?->graduation_session)
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Graduation Session:</span>
                                                <span class="text-black">
                                                    {{ $data['member']?->memberAcademicInfo?->graduation_session }}/{{ $data['member']?->memberAcademicInfo?->graduation_session + 1 }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <hr>
                                    <p class="text-danger m-4 fs-5">
                                        This application is exclusively for members who have completed a program, been
                                        re-admitted to the institution, and remain active members of the association. This
                                        process allows us to safely update your current record while preserving your
                                        historical data. Please do not complete this form if you do not meet these criteria.
                                        Additionally, any applications not submitted within 24 hours of creation will be
                                        automatically deleted.
                                    </p>
                                    <form class="form my_form" method="POST"
                                        action="{{ route('memberApplyForReAdmissionSubmit') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="program-select">
                                                    Program <span class="text-red">*</span>
                                                </label>

                                                <select class="form-select" id="program-select" name="program" required>

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

                                            <div class="form-group col-md-4">
                                                <label for="program-type-select">
                                                    Program Type <span class="text-red">*</span>
                                                </label>

                                                <select class="form-select" id="program-type-select" name="program_type"
                                                    required disabled>

                                                    <option value="">- Select Program First -</option>
                                                </select>

                                                <p class="text-danger">{{ $errors->first('program_type') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="faculty-select">
                                                    Faculty <span class="text-red">*</span>
                                                </label>

                                                <select class="form-select" id="faculty-select" name="faculty" required>

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
                                            <div class="form-group col-md-4">
                                                <label for="department-select">
                                                    Department <span class="text-red">*</span>
                                                </label>

                                                <select class="form-select" id="department-select" name="department"
                                                    required disabled>

                                                    <option value="">- Select Faculty First -</option>

                                                </select>

                                                <p class="text-danger">{{ $errors->first('department') }}</p>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="admission_session">Admission Session<span
                                                        class="text-red">*</span>:</label>
                                                <select name="admission_session" class="form-control">
                                                    <option value="" disabled selected>Select</option>
                                                    @php
                                                        $startYear = currentAcademicYear() - highestProgramDuration();
                                                        $endYear = currentAcademicYear();
                                                    @endphp

                                                    @for ($year = $endYear; $year >= $startYear; $year--)
                                                        <option value="{{ $year }}"
                                                            {{ old('admission_session') == "$year"
                                                                ? 'selected'
                                                                : '' }}>
                                                            {{ $year }}/{{ $year + 1 }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                <p style="color: red">{{ $errors->first('admission_session') }}</p>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="matric_number">Matric Number<span
                                                        class="text-red">*</span>:</label>
                                                <input type="text" class="form-control text-uppercase"
                                                    name="matric_number"
                                                    value="{{ old('matric_number') }}"
                                                    required>
                                                <p style="color: red">{{ $errors->first('matric_number') }}</p>
                                            </div>

                                            <hr>
                                            <button type="submit" class="green-button mt-2">Submit/Next</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('viewMember', ['uuid' => $data['member']->uuid]) }}">
                            <i class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>

                </div>

            </div>
        </div>

        {{-- scripts start --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const programSelect = document.getElementById('program-select');
                const programTypeSelect = document.getElementById('program-type-select');

                if (!programSelect || !programTypeSelect) return;

                const selectedProgramType =
                    "{{ old('program_type') }}";

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
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const facultySelect = document.getElementById('faculty-select');
                const departmentSelect = document.getElementById('department-select');

                if (!facultySelect || !departmentSelect) return;

                const selectedDepartment = "{{ old('department') }}";

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
        {{-- scripts end --}}

    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
