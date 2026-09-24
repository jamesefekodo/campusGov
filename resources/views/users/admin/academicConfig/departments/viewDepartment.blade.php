@extends('layouts.userLayout')
@section('title', 'View Department :: ' . $data['department']->name)
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
                                        {{ $data['department']->name }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="col-md-12 row">
                                            <div class="col-md-6">
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Department:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['department']->name }}
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Faculty:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['department']->theFaculty->name }} @if ($data['department']->theFaculty->acronym)
                                                            ({{ $data['department']->theFaculty->acronym }})
                                                        @endif
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col-md-6">
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Status:
                                                    </span>
                                                    <span class="text-black">
                                                        @switch($data['department']->is_active)
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
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Students:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['studentMembers'] }} Undergraduates +
                                                        {{ $data['alumniMembers'] }} Alumni
                                                    </span>
                                                </p>
                                            </div>

                                            <p class="m-1 p-0">
                                                <span class="text-mute">
                                                    Description:
                                                </span>
                                                <span class="text-black">
                                                    {!! $data['department']->description !!}
                                                </span>
                                            </p>

                                        </div>
                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            @if (Auth::user()->hasPermission('department.update'))
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#editDepartmentModal">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                            @endif

                                            {{-- @if (Auth::user()->hasPermission('department.delete'))
                                                <button type="button"
                                                    class="btn btn-danger m-1 d-flex align-items-center delete-role"
                                                    data-url="{{ route('deleteFaculty', ['slug' => $data['policy']->slug]) }}">
                                                    <i class="fas fa-trash me-2"></i>
                                                    <span>Delete</span>
                                                </button>
                                            @endif --}}

                                        </div>
                                        <br>
                                        {{-- dean --}}
                                        <div class="d-flex align-items-center">
                                            <hr class="flex-grow-1">
                                            <span class="mx-2 text-black">Administrative Head @if ($data['department']->administrative_head_title)
                                                    ({{ $data['department']->administrative_head_title }})
                                                @endif
                                            </span>
                                            <hr class="flex-grow-1">
                                        </div>

                                        <div class="col-md-12 container">
                                            <div class="col-md-12 align-content-start">
                                                @if (!$data['department']->hod)
                                                    <h5 class="text-red text-center">No records found!</h5>
                                                    <br>
                                                    @if (Auth::user()->hasPermission('department.update'))
                                                        <div class="d-flex justify-content-center">
                                                            <div class="m-1">
                                                                <button class="btn btn-warning btn-sm table_btn"
                                                                    data-toggle="modal"
                                                                    data-target="#addNewDepartmentHeadModal">
                                                                    <i class="fas fa-user-graduate"></i> Create
                                                                    Profile</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @include('users.admin.academicConfig.departments.include.createDepartmentHeadModal')
                                                @else
                                                    <div class="col-md-12 container">
                                                        <div class="row">
                                                            <div class="col-md-6 justify-content-center text-center">
                                                                @if ($data['department']->hod->photo && $data['department']->hod->photo)
                                                                    <img src="{{ asset('assets/uploads/department/head/photo/' . $data['department']->hod->photo) }}"
                                                                        alt="{{ $data['department']->hod->name }}"
                                                                        style="max-width: 200px">
                                                                @else
                                                                    <img src="{{ asset('system/images/hod_default_pic.webp') }}"
                                                                        alt="Default Picture" style="max-width: 200px">
                                                                @endif
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="text-black my-0">
                                                                    <span class="text-secondary">Name:</span>
                                                                    {{ $data['department']->hod->name }}
                                                                    @if ($data['department']->hod->certifications)
                                                                        ({{ $data['department']->hod->certifications }})
                                                                    @endif
                                                                </p>
                                                                <p class="text-black my-0">
                                                                    <span class="text-secondary">Phone:</span>
                                                                    {{ $data['department']->hod->phone }}
                                                                </p>
                                                                <p class="text-black my-0">
                                                                    <span class="text-secondary">Email:</span>
                                                                    {{ $data['department']->hod->email }}
                                                                </p>
                                                                <p class="text-black my-0">
                                                                    <span class="text-secondary">Profile:</span>
                                                                    <span class="text-black">{!! $data['department']->hod->profile !!}</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-wrap justify-content-end mx-2">
                                                            <div class="m-1">
                                                                <a class="btn btn-warning btn-sm" data-toggle="modal"
                                                                    data-target="#editDepartmentHeadModal">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                            </div>
                                                            <div class="m-1">
                                                                <a class="btn btn-danger delete-policy" data-toggle="modal"
                                                                    data-target="#endDepartmentHeadTenureModal">
                                                                    <i class="fas fa-calendar-xmark"></i>
                                                                    End Tenure
                                                                </a>
                                                            </div>
                                                            <div class="m-1">
                                                                <a class="btn btn-primary"
                                                                    href="{{ route('pastDepartmentHeads', ['uuid' => $data['department']->uuid]) }}">
                                                                    <i class="fas fa-address-book"></i> Past Heads
                                                                </a>
                                                            </div>


                                                        </div>
                                                    </div>
                                                    @include('users.admin.academicConfig.departments.include.editDepartmentHeadModal')
                                                    @include('users.admin.academicConfig.departments.include.endDepartmentHeadTenure')
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('departments') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- modals start --}}
            @include('users.admin.academicConfig.departments.include.editDepartmentModal')
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
