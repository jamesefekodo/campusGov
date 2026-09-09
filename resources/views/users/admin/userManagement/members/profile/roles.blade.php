@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? '') . ' - Roles')
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
                                    Roles
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    @forelse($data['member']->roles as $role)
                                        <div class="col-md-4 mb-3">
                                            <div class="card shadow-sm border-1 h-100">

                                                <div class="card-body p-4">

                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h5 class="mb-1 fw-bold">
                                                                {{ $role->name }}
                                                            </h5>

                                                            {{-- <small class="text-muted">
                                                                since
                                                                {{ \Carbon\Carbon::parse($role->created_at)->format('l, d F, Y h:i A') }}
                                                            </small> --}}
                                                        </div>

                                                        <i class="fas fa-user-shield fs-3 text-secondary"></i>
                                                    </div>
                                                    <hr>

                                                    <p class="mb-2">
                                                        <span class="text-capitalize">
                                                            {{ $role->description }}
                                                        </span>
                                                    </p>

                                                    @if (Auth::user()->hasPermission('roles.assign'))
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button"
                                                                class="btn btn-danger m-1 d-flex align-items-center unassign-role"
                                                                data-url="{{ route('unAssignRole', ['uuid' => $data['member']->uuid, 'id' => $role->id]) }}"
                                                                >
                                                                <i class="fas fa-user-times me-2"></i>
                                                                <span>Unassign</span>
                                                            </button>
                                                        </div>


                                                        <script>
                                                            document.addEventListener('DOMContentLoaded', function() {
                                                                document.querySelectorAll('.unassign-role').forEach(button => {
                                                                    button.addEventListener('click', function() {

                                                                        const url = this.dataset.url;

                                                                        Swal.fire({
                                                                            title: 'Are you sure?',
                                                                            text: "This user will loose all permissions attached to the role.",
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
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    @empty

                                        <div class="col-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body text-center py-5">
                                                    <h5>No Records Found!</h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                    @if (Auth::user()->hasPermission('roles.assign'))
                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                                data-target="#assignUserNewRoleModal">
                                                <i class="fas fa-plus"></i> Assign New Role</button>
                                        </div>


                                        @include('users.admin.userManagement.members.profile.include.assignUserNewRoleModal')
                                    @endif
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

    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
