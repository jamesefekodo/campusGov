@extends('layouts.userLayout')
@section('title', 'View Program :: ' . $data['program']->name)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        {{ $data['program']->name }} ({{ $data['program']->short_name }})
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="col-md-12 row">
                                            <div class="col-md-6">
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Program:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['program']->name }}
                                                        ({{ $data['program']->short_name }})
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Duration:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['program']->duration }} Years
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Award:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['program']->award }}
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Students:
                                                    </span>
                                                    <span class="text-black">
                                                        0
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col-md-6">
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Level Format:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['program']->level_format }}
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Increment:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['program']->increment }}
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Status:
                                                    </span>
                                                    <span class="text-black">
                                                        @switch($data['program']->is_active)
                                                            @case(true)
                                                                <span class="badge bg-blue text-white">
                                                                    Active
                                                                </span>
                                                            @break

                                                            @default
                                                                <span class="badge bg-red text-white">
                                                                    Inactive
                                                                </span>
                                                        @endswitch
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            @if (Auth::user()->hasPermission('program.update'))
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#editProgramModal">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                            @endif

                                            @if (Auth::user()->hasPermission('program.delete'))
                                                <button type="button"
                                                    class="btn btn-danger m-1 d-flex align-items-center delete-role"
                                                    {{-- data-url="{{ route('deleteFaculty', ['slug' => $data['policy']->slug]) }}" --}}>
                                                    <i class="fas fa-trash me-2"></i>
                                                    <span>Delete</span>
                                                </button>
                                            @endif

                                        </div>

                                        {{-- program types starts --}}
                                        <span class="text-black text-decoration-underline">
                                            Program Types :
                                        </span> <span class="small">(full-time/part-time/odl...)</span>
                                        @if ($data['program']->programTypes->isNotEmpty())
                                            <div class="px-3 pt-3">
                                                <table class="table">
                                                    @foreach ($data['program']->programTypes as $type)
                                                        <tbody>
                                                            <tr class="text-black">
                                                                <th class="" scope="row">{{ $type->type_name }}
                                                                    @switch($type->is_active)
                                                                        @case(true)
                                                                            <span class="badge bg-blue text-white ms-2">
                                                                                Active
                                                                            </span>
                                                                        @break

                                                                        @default
                                                                            <span class="badge bg-red text-white ms-2">
                                                                                Inactive
                                                                            </span>
                                                                    @endswitch
                                                                </th>
                                                                <td class="flex-wrap">
                                                                    <span
                                                                        class="badge bg-yellow text-black pe-2 ps-3 py-2 m-1 rounded-3"
                                                                        data-toggle="modal"
                                                                        data-target="#editProgramTypeModal_{{ $type->uuid }}"
                                                                        style="cursor: pointer;">
                                                                        <i class="fas fa-edit me-1"></i>Edit
                                                                    </span>
                                                                    <a href="javascript:void(0)"
                                                                        class="badge bg-red text-white pe-2 ps-3 py-2 rounded-3 delete-confirm m-1"
                                                                        data-toggle="modal"
                                                                        data-target="#changeIsPaidBreakModal"
                                                                        {{-- data-url="{{ route('companyRemoveShiftScheduleStep', ['uuid' => $step->uuid]) }}" --}} style="cursor: pointer;">
                                                                        <i class="fas fa-trash me-1"></i>Remove
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    @endforeach
                                                </table>
                                            </div>
                                        @else
                                            <h3 class="p-1 m-1 text-red w-100 text-center">
                                                No Records Found!
                                                <br>
                                            </h3>
                                        @endif
                                        @if (Auth::user()->hasPermission('program.update'))
                                            <div class="d-flex justify-content-center">
                                                <div class="m-1">
                                                    <button class="btn btn-primary btn-sm table_btn" data-toggle="modal"
                                                        data-target="#addNewProgramTypeModal">
                                                        <i class="fas fa-plus"></i> Create
                                                        New Program Type</button>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- program types endss --}}
                                        <hr>
                                        {{-- program duration overrides starts --}}
                                        <span class="text-black text-decoration-underline mt-3">
                                            Program Duration Overrides :
                                        </span>
                                        @if ($data['program']->durationOverrides->isNotEmpty())
                                            <div class="px-3 pt-3">
                                                <table class="table">
                                                    @foreach ($data['program']->durationOverrides as $duration)
                                                        <tbody>
                                                            <tr class="text-black">
                                                                <th class="" scope="row">
                                                                    {{ optional($duration->department)->name ?? 'All Departments' }} -
                                                                    {{ optional($duration->programType)->type_name ?? 'All Program Types' }}
                                                                    @switch($duration->is_active)
                                                                        @case(true)
                                                                            <span class="badge bg-blue text-white ms-2">
                                                                                Active
                                                                            </span>
                                                                        @break

                                                                        @default
                                                                            <span class="badge bg-red text-white ms-2">
                                                                                Inactive
                                                                            </span>
                                                                    @endswitch
                                                                </th>
                                                                <td class="text-start">
                                                                    {{ $duration->duration }} years
                                                                </td>
                                                                <td class="flex-wrap">
                                                                    <span
                                                                        class="badge bg-yellow text-black pe-2 ps-3 py-2 m-1 rounded-3"
                                                                        data-toggle="modal"
                                                                        data-target="#editProgramTypeModal_{{ $duration->uuid }}"
                                                                        style="cursor: pointer;">
                                                                        <i class="fas fa-edit me-1"></i>Edit
                                                                    </span>
                                                                    <a href="javascript:void(0)"
                                                                        class="badge bg-red text-white pe-2 ps-3 py-2 rounded-3 delete-confirm m-1"
                                                                        data-toggle="modal"
                                                                        data-target="#changeIsPaidBreakModal"
                                                                        {{-- data-url="{{ route('companyRemoveShiftScheduleStep', ['uuid' => $step->uuid]) }}" --}} style="cursor: pointer;">
                                                                        <i class="fas fa-trash me-1"></i>Remove
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    @endforeach
                                                </table>
                                            </div>
                                        @else
                                            <h3 class="p-1 m-1 text-red w-100 text-center">
                                                No Records Found!
                                                <br>
                                            </h3>
                                        @endif
                                        @if (Auth::user()->hasPermission('program.update'))
                                            <div class="d-flex justify-content-center">
                                                <div class="m-1">
                                                    <button class="btn btn-primary btn-sm table_btn" data-toggle="modal"
                                                        data-target="#addNewProgramDurationOverrideModal">
                                                        <i class="fas fa-plus"></i> Create
                                                        Duration Override</button>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- program duration overrides ends --}}
                                        @include('users.admin.academicConfig.programs.include.addNewProgramDurationOverrideModal')

                                        @if ($data['program']->durationOverrides->isNotEmpty())
                                             @foreach ($data['program']->durationOverrides as $duration)
                                                @include('users.admin.academicConfig.programs.include.editProgramTypeModal')
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('programs') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- modals start --}}
            @include('users.admin.academicConfig.programs.include.editProgramModal')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.delete-role').forEach(button => {
                        button.addEventListener('click', function() {

                            const url = this.dataset.url;

                            Swal.fire({
                                title: 'Are you sure?',
                                text: "This policy will be permanently deleted.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = url;
                                }
                            });

                        });
                    });
                });
            </script>
            {{-- modals end --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
