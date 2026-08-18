@extends('layouts.userLayout')
@section('title', 'Process Re-Admission Request')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @php
                                $data['member'] = $data['request']->userProfile;
                            @endphp
                            @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')

                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Process Re-Admission Request
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Applicant:</span>
                                        <span class="text-black">
                                            {{ trim(($data['request']->personalProfile?->first_name ?? '') . ' ' . ($data['request']->personalProfile?->last_name ?? '')) }}
                                            <a href="{{ route('viewMember', ['uuid' => $data['request']->userProfile->uuid]) }}"
                                                target="_blank">
                                                <span class="badge bg-secondary"><i class="fas fa-link"></i></span>
                                            </a>
                                        </span>
                                    </div>
                                    <p class="text-bold text-black text-decoration-underline fs-4 ms-3 mb-0 pb-0">Previous
                                        Program</p>
                                    <div class="p-4 row col-md-12 pt-0">
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Matric Number:</span>
                                            <span class="text-black text-uppercase">
                                                {{ $data['request']->previous_matric_number }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Academic Status:</span>
                                            <span class="text-black">
                                                @php
                                                    $academicStatus = $data['request']->previous_academic_status;
                                                    if ($academicStatus == 'student') {
                                                        $currentStatus = 'Student';
                                                    } elseif ($academicStatus == 'extra_year') {
                                                        $currentStatus = 'Extra Year';
                                                    } elseif ($academicStatus == 'alumni') {
                                                        $currentStatus = 'Graduated';
                                                    } elseif ($academicStatus == 'withdrawn') {
                                                        $currentStatus = 'Withdrawn';
                                                    } elseif ($academicStatus == 'suspended') {
                                                        $currentStatus = 'Suspended';
                                                    } elseif ($academicStatus == 'expelled') {
                                                        $currentStatus = 'Expelled';
                                                    } else {
                                                        $currentStatus = 'Unknown';
                                                    }
                                                @endphp
                                                {{ $currentStatus }}:
                                                {{ $data['request']->previous_graduation_session . '/' . $data['request']->previous_graduation_session + 1 }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Program:</span>
                                            <span class="text-black">
                                                {{ $data['request']->previousProgram->name }} (
                                                {{ $data['request']->previousProgram->award }} -
                                                {{ $data['request']->previousProgramType->type_name }})
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Faculty:</span>
                                            <span class="text-black">
                                                {{ $data['request']->previousFaculty->name }}
                                                @if ($data['request']->previousFaculty->acronym)
                                                    ({{ $data['request']->previousFaculty->acronym }})
                                                @endif
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Department:</span>
                                            <span class="text-black">
                                                {{ $data['request']->previousDepartment->name }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Admission Session:</span>
                                            <span class="text-black">
                                                {{ $data['request']?->previous_admission_session }}/{{ $data['request']?->previous_admission_session + 1 }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 p-1">
                                            <span style="font-weight: bold">Graduation Session:</span>
                                            <span class="text-black">
                                                {{ $data['request']->previous_graduation_session }}/{{ $data['request']->previous_graduation_session + 1 }}
                                            </span>
                                        </div>

                                        <p class="text-bold text-black text-decoration-underline fs-4 ms-3 mt-3 mb-0 pb-0">
                                            New Program</p>
                                        @if ($data['request']->status == 'pending')
                                            <form class="form my_form" method="POST"
                                                action="{{ route('processReAdmissionRequestSubmit', ['uuid' => $data['request']->uuid]) }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="program-select">
                                                            Program <span class="text-red">*</span>
                                                        </label>

                                                        <select class="form-select" id="program-select" name="new_program"
                                                            required>

                                                            <option value="">- Select -</option>

                                                            @foreach ($data['activeProgram'] as $program)
                                                                <option value="{{ $program->slug }}"
                                                                    {{ $data['request']->new_program == $program->slug ? 'selected' : '' }}>
                                                                    {{ $program->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        <p class="text-danger">{{ $errors->first('new_program') }}</p>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="program-type-select">
                                                            Program Type <span class="text-red">*</span>
                                                        </label>

                                                        <select class="form-select" id="program-type-select"
                                                            name="new_program_type" required disabled>

                                                            <option value="">- Select Program First -</option>
                                                        </select>

                                                        <p class="text-danger">{{ $errors->first('new_program_type') }}</p>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="faculty-select">
                                                            Faculty <span class="text-red">*</span>
                                                        </label>

                                                        <select class="form-select" id="faculty-select" name="new_faculty"
                                                            required>

                                                            <option value="">- Select -</option>

                                                            @foreach ($data['activeFaculties'] as $faculty)
                                                                <option value="{{ $faculty->slug }}"
                                                                    {{ $data['request']->new_faculty == $faculty->slug ? 'selected' : '' }}>
                                                                    {{ $faculty->name }}
                                                                </option>
                                                            @endforeach

                                                        </select>

                                                        <p class="text-danger">{{ $errors->first('new_faculty') }}</p>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="department-select">
                                                            Department <span class="text-red">*</span>
                                                        </label>

                                                        <select class="form-select" id="department-select"
                                                            name="new_department" required disabled>

                                                            <option value="">- Select Faculty First -</option>

                                                        </select>

                                                        <p class="text-danger">{{ $errors->first('new_department') }}</p>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="new_admission_session">Admission Session<span
                                                                class="text-red">*</span>:</label>
                                                        <select name="new_admission_session" class="form-control">
                                                            <option value="" disabled selected>Select</option>
                                                            @php
                                                                $startYear =
                                                                    currentAcademicYear() - highestProgramDuration();
                                                                $endYear = currentAcademicYear();
                                                            @endphp

                                                            @for ($year = $endYear; $year >= $startYear; $year--)
                                                                <option value="{{ $year }}"
                                                                    {{ $data['request']->new_admission_session == "$year" ? 'selected' : '' }}>
                                                                    {{ $year }}/{{ $year + 1 }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                        <p style="color: red">{{ $errors->first('admission_session') }}</p>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="new_matric_number">Matric Number<span
                                                                class="text-red">*</span>:</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            name="new_matric_number"
                                                            value="{{ $data['request']->new_matric_number }}" required>
                                                        <p style="color: red">{{ $errors->first('new_matric_number') }}</p>
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label for="status">Approval Status: <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select" name="status" required>
                                                            <option selected disabled>
                                                                -Select-</option>
                                                            <option value="pending"
                                                                {{ $data['request']->status == 'pending' ? 'selected' : '' }}
                                                                disabled>
                                                                Pending</option>
                                                            <option value="approved"
                                                                {{ $data['request']->status == 'approved' ? 'selected' : '' }}>
                                                                Approved</option>
                                                            <option value="declined"
                                                                {{ $data['request']->status == 'declined' ? 'selected' : '' }}>
                                                                Declined</option>
                                                        </select>
                                                        <p style="color: red">{{ $errors->first('status') }}</p>
                                                    </div>

                                                    <hr>
                                                    <button type="submit" class="green-button mt-2">Submit</button>
                                                </div>

                                            </form>
                                        @else
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Matric Number:</span>
                                                <span class="text-black text-uppercase">
                                                    {{ $data['request']->new_matric_number }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Academic Status:</span>
                                                <span class="text-black">
                                                    Student
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Program:</span>
                                                <span class="text-black">
                                                    {{ $data['request']->newProgram->name }} (
                                                    {{ $data['request']->newProgram->award }} -
                                                    {{ $data['request']->newProgramType->type_name }})
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Faculty:</span>
                                                <span class="text-black">
                                                    {{ $data['request']->newFaculty->name }}
                                                    @if ($data['request']->newFaculty->acronym)
                                                        ({{ $data['request']->newFaculty->acronym }})
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Department:</span>
                                                <span class="text-black">
                                                    {{ $data['request']->newDepartment->name }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Admission Session:</span>
                                                <span class="text-black">
                                                    {{ $data['request']?->new_admission_session }}/{{ $data['request']?->new_admission_session + 1 }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Requested On:</span>
                                                <span class="text-black">
                                                    {{ $data['request']->created_at->format('F j, Y, g:i A') }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Status:</span>
                                                <span class="text-black">
                                                    @switch($data['request']->status)
                                                        @case('approved')
                                                            <span class="badge bg-success-subtle text-success">
                                                                Approved
                                                            </span>
                                                        @break

                                                        @case('pending')
                                                            <span class="badge bg-warning-subtle text-warning">
                                                                Pending
                                                            </span>
                                                        @break

                                                        @case('declined')
                                                            <span class="badge bg-danger-subtle text-danger">
                                                                Declined
                                                            </span>
                                                        @break

                                                        @default
                                                            <span class="badge bg-danger-subtle text-danger">
                                                                {{ $data['request']->status }}
                                                            </span>
                                                    @endswitch
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Processed By:</span>
                                                <span class="text-black">
                                                    {{ $data['request']->processedBy?->memberPersonalProfile
                                                        ? $data['request']->processedBy->memberPersonalProfile->first_name .
                                                            ' ' .
                                                            $data['request']->processedBy->memberPersonalProfile->last_name
                                                        : $data['request']->processed_by }}
                                                    | {{ $data['request']->processed_at->format('F j, Y, g:i A') }}
                                                </span>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('reAdmissionRequests') }}">
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
                    "{{ $data['request']->new_program_type }}";

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

                const selectedDepartment = "{{ $data['request']->new_department }}";

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
