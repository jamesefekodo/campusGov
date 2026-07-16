@extends('layouts.userLayout')
@section('title', 'View Role :: ' . $data['role']->name)
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
                                        View Role ::
                                        {{ $data['role']->name }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="col-md-12 row">
                                            <p class="m-1 p-0">
                                                <span class="text-mute">
                                                    Name:
                                                </span>
                                                <span class="text-black">
                                                    {{ $data['role']->name }}
                                                </span>
                                            </p>
                                            <p class="m-1 p-0">
                                                <span class="text-mute">
                                                    Description:
                                                </span>
                                                <span class="text-black">
                                                    {{ $data['role']->description }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            @if (Auth::user()->hasPermission('roles.update'))
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#updateRoleModal">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                            @endif

                                            @if (Auth::user()->hasPermission('roles.delete'))
                                                <button type="button"
                                                    class="btn btn-danger m-1 d-flex align-items-center delete-role"
                                                    data-url="{{ route('deleteRole', ['uuid' => $data['role']->uuid]) }}">
                                                    <i class="fas fa-trash me-2"></i>
                                                    <span>Delete</span>
                                                </button>
                                            @endif
                                        </div>


                                        <div class="d-flex align-items-center">
                                            <hr class="flex-grow-1">
                                            <span class="mx-2 text-black">Users</span>
                                            <hr class="flex-grow-1">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <hr class="flex-grow-1">
                                            <span class="mx-2 text-black">Permissions</span>
                                            <hr class="flex-grow-1">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('viewUserRoles') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- modals start --}}
            @include('users.admin.config.accessControl.roles.include.editNewRoleModal')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.delete-role').forEach(button => {
                        button.addEventListener('click', function() {

                            const url = this.dataset.url;

                            Swal.fire({
                                title: 'Are you sure?',
                                text: "This role will be permanently deleted.",
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
