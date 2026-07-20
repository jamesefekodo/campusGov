@extends('layouts.userLayout')
@section('title', 'View Faculty :: ' . $data['faculty']->name)
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
                                        {{ $data['faculty']->name }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="col-md-12 row">
                                            <div class="col-md-4">
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Faculty:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['faculty']->name }} @if ($data['faculty']->acronym)
                                                            ({{ $data['faculty']->acronym }})
                                                        @endif
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Faculty:
                                                    </span>
                                                    <span class="text-black">
                                                        @switch($data['faculty']->is_active)
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
                                            <div class="col-md-4">
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Departments:
                                                    </span>
                                                    <span class="text-black">
                                                        0
                                                    </span>
                                                </p>
                                            </div>

                                            <p class="m-1 p-0">
                                                <span class="text-mute">
                                                    Description:
                                                </span>
                                                <span class="text-black">
                                                    {!! $data['faculty']->description !!}
                                                </span>
                                            </p>

                                        </div>
                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            @if (Auth::user()->hasPermission('faculty.update'))
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#editFacultyModal">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                            @endif

                                            {{-- @if (Auth::user()->hasPermission('faculty.delete'))
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
                                            <span class="mx-2 text-black">Administrative Head @if ($data['faculty']->administrative_head_title)
                                                    ({{ $data['faculty']->administrative_head_title }})
                                                @endif
                                            </span>
                                            <hr class="flex-grow-1">
                                        </div>

                                        <div class="col-md-12 container">
                                            <div class="col-md-12 align-content-start">
                                                @if (!$data['faculty']->facultyHead)
                                                    <h5 class="text-red text-center">No records found!</h5>
                                                    <br>
                                                    @if (Auth::user()->hasPermission('faculty.update'))
                                                        <div class="d-flex justify-content-center">
                                                            <div class="m-1">
                                                                <button class="btn btn-warning btn-sm table_btn"
                                                                    data-toggle="modal"
                                                                    data-target="#addNewFacultyHeadModal">
                                                                    <i class="fas fa-user-graduate"></i> Create
                                                                    Profile</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @include('users.admin.academicConfig.faculties.include.createFacultyDean')
                                                @else
                                                    <div class="col-md-12 container">
                                                        <div class="row">
                                                            <div class="col-md-6 justify-content-center text-center">
                                                                @if ($data['faculty']->facultyHead->photo && $data['faculty']->facultyHead->photo)
                                                                    <img src="{{ asset('assets/uploads/faculty/head/photo/' . $data['faculty']->facultyHead->photo) }}"
                                                                        alt="{{ $data['faculty']->facultyHead->name }}"
                                                                        style="max-width: 200px">
                                                                @else
                                                                    <img src="{{ asset('system/images/hod_default_pic.webp') }}"
                                                                        alt="Default Picture" style="max-width: 200px">
                                                                @endif
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="text-black my-0">
                                                                    <span class="text-secondary">Name:</span>
                                                                    {{ $data['faculty']->facultyHead->name }}
                                                                    @if ($data['faculty']->facultyHead->certifications)
                                                                        ({{ $data['faculty']->facultyHead->certifications }})
                                                                    @endif
                                                                </p>
                                                                <p class="text-black my-0">
                                                                    <span class="text-secondary">Phone:</span>
                                                                    {{ $data['faculty']->facultyHead->phone }}
                                                                </p>
                                                                <p class="text-black my-0">
                                                                    <span class="text-secondary">Email:</span>
                                                                    {{ $data['faculty']->facultyHead->email }}
                                                                </p>
                                                                <p class="text-black my-0">
                                                                    <span class="text-secondary">Profile:</span>
                                                                    <span class="text-black">{!! $data['faculty']->facultyHead->profile !!}</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-wrap justify-content-end mx-2">
                                                            <div class="m-1">
                                                                <a class="btn btn-warning btn-sm" data-toggle="modal"
                                                                    data-target="#editFacultyHeadModal">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                            </div>
                                                            <div class="m-1">
                                                                <a class="btn btn-danger delete-policy" data-toggle="modal"
                                                                    data-target="#endFacultyHeadTenureModal">
                                                                    <i class="fas fa-calendar-xmark"></i>
                                                                    End Tenure
                                                                </a>
                                                            </div>
                                                            <div class="m-1">
                                                                <a class="btn btn-primary" href="{{ route('pastFacultyHeads', ['uuid' => $data['faculty']->uuid]) }}">
                                                                    <i class="fas fa-address-book"></i> Past Deans
                                                                </a>
                                                            </div>


                                                        </div>
                                                    </div>
                                                    @include('users.admin.academicConfig.faculties.include.editFacultyDeanModal')
                                                    @include('users.admin.academicConfig.faculties.include.endFacultyHeadTenure')
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('faculties') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- modals start --}}
            @include('users.admin.academicConfig.faculties.include.editFacultyModal')
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
