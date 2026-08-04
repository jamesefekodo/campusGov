@extends('layouts.userLayout')
@section('title', 'Update ' . ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? ''))
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')
                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Update Academic Info
                                </h4>
                                <div class="card-body">
                                    <form class="form my_form" method="POST"
                                        action="{{ route('updateMemberAcademicInfoSubmit', ['uuid' => $data['member']->uuid]) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="step" value="{{ $data['step'] }}">

                                        @switch($data['step'])
                                            @case(1)
                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label for="program-select">
                                                            Program <span class="text-red">*</span>
                                                        </label>

                                                        <select class="form-select" id="program-select" name="program" required>

                                                            <option value="">- Select -</option>

                                                            @foreach ($data['activeProgram'] as $program)
                                                                <option value="{{ $program->slug }}"
                                                                    {{ old('program', $data['member']->memberAcademicInfo?->program) == $program->slug ? 'selected' : '' }}>
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
                                                                    {{ old('faculty', $data['member']->memberAcademicInfo?->faculty) == $faculty->slug ? 'selected' : '' }}>
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
                                                                $startYear = schoolFoundingYear();
                                                                $endYear = currentAcademicYear();
                                                            @endphp

                                                            @for ($year = $endYear; $year >= $startYear; $year--)
                                                                <option value="{{ $year }}"
                                                                    {{ isset($data['member']->memberAcademicInfo?->admission_session) &&
                                                                    $data['member']->memberAcademicInfo?->admission_session == "$year"
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
                                                            value="{{ old('matric_number', $data['member']->memberAcademicInfo?->matric_number) }}"
                                                            required>
                                                        <p style="color: red">{{ $errors->first('matric_number') }}</p>
                                                    </div>

                                                    <hr>
                                                    <button type="submit" class="green-button mt-2">Submit/Next</button>
                                                </div>
                                            @break

                                            @case(2)
                                                <div class="form-group col-md-12">

                                                    <label>
                                                        Academic Status
                                                        <span class="text-red">*</span>
                                                    </label>


                                                    <select name="academic_status" class="form-control" required>

                                                        <option value="">
                                                            - Select -
                                                        </option>


                                                        @foreach ($data['academicStatus']['statuses'] as $key => $value)
                                                            <option value="{{ $key }}"
                                                                {{ old('academic_status', $data['member']->memberAcademicInfo?->academic_status) == $key ? 'selected' : '' }}>
                                                                {{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>

                                                @if ($data['academicStatus']['require_graduation_session'])
                                                    <div class="form-group col-md-12">

                                                        <label>
                                                            Graduation Session
                                                            <span class="text-red">*</span>
                                                        </label>


                                                        <select name="graduation_session" class="form-control">

                                                            <option value="">
                                                                - Select -
                                                            </option>


                                                            @for ($year = date('Y'); $year >= schoolFoundingYear(); $year--)
                                                                <option value="{{ $year }}"
                                                                    {{ old('graduation_session', $data['member']->memberAcademicInfo?->graduation_session) == $year ? 'selected' : '' }}>
                                                                    {{ $year }}/{{ $year + 1 }}
                                                                </option>
                                                            @endfor


                                                        </select>
                                                    </div>
                                                @endif
                                                <hr>
                                                <div class="d-flex gap-2 mt-2">
                                                    <a href="{{ route('editMemberAcademicInfo', ['uuid' => $data['member']->uuid]) }}" class="btn btn-danger px-3">
                                                        Back
                                                    </a>

                                                    <button type="submit" class="green-button flex-grow-1">
                                                        Submit
                                                    </button>
                                                </div>
                                            @break

                                            @default
                                        </div>
                                @endswitch

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Back Home --}}
                <div class="w-100 justify-content-center text-center my-4 pt-5">
                    <a class="btn btn-primary"
                        href="{{ route('viewMember', ['uuid' => $data['member']->uuid]) }}">
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
                "{{ old('program_type', $data['member']->memberAcademicInfo?->program_type) }}";

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

            const selectedDepartment = "{{ old('department', $data['member']->memberAcademicInfo?->department) }}";

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
