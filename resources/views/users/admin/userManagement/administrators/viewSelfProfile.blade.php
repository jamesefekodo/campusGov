@extends('layouts.userLayout')
@section('title', 'Admin Profile')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @include('users.admin.userManagement.administrators.include.adminProfileSidebar')
                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Admin Profile
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Username:</span>
                                        <span class="text-black">
                                            {{ $data['admin']->username }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Full Name:</span>
                                        <span class="text-black">
                                            {{ $data['admin']?->adminProfile?->name ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Position:</span>
                                        <span class="text-black">
                                            {{ $data['admin']?->adminProfile?->position ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black">
                                            {{ $data['admin']?->adminProfile?->description ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Phone:</span>
                                        <span class="text-black">
                                            {{ $data['admin']->phone }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Email:</span>
                                        <span class="text-black">
                                            {{ $data['admin']->email }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Login Mfa:</span>
                                        @if (Auth::user()->hasPermission('manage_admin'))
                                            @livewire('user-mfa-toggle', ['userId' => $data['admin']->id])
                                        @else
                                            @switch($data['admin']->require_login_mfa)
                                                @case(true)
                                                    <span class="badge bg-blue text-white">Active</span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary text-white">In-active</span>
                                            @endswitch
                                        @endif

                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Last Seen:</span>
                                        <span class="text-black">
                                            {{ $data['admin']->last_seen_at
                                                ? (\Carbon\Carbon::parse($data['admin']->last_seen_at)->isToday()
                                                    ? \Carbon\Carbon::parse($data['admin']->last_seen_at)->format('h:i:s A')
                                                    : \Carbon\Carbon::parse($data['admin']->last_seen_at)->format('l, d F Y \a\t h:i:s A'))
                                                : '-' }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Account Status:</span>
                                        <span class="text-black">
                                            @switch($data['admin']->account_status)
                                                @case('0')
                                                    <span class="badge bg-yellow text-black">
                                                        Unverified
                                                    </span>
                                                @break

                                                @case('1')
                                                    <span class="badge bg-blue text-white">
                                                        Awaiting Verification
                                                    </span>
                                                @break

                                                @case('2')
                                                    <span class="badge bg-green text-white">
                                                        Active
                                                    </span>
                                                @break

                                                @case('3')
                                                    <span class="badge bg-red text-white">
                                                        Disabled
                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge bg-red text-white">
                                                        Unknown
                                                    </span>
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <hr class="flex-grow-1">
                                        <span class="mx-2 text-black">Roles</span>
                                        <hr class="flex-grow-1">
                                    </div>
                                    @forelse($data['admin']->roles as $role)
                                        <div class="col-md-4 mb-3">
                                            <div class="card shadow-sm border-1 h-100">

                                                <div class="card-body p-4">

                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h5 class="mb-1 fw-bold">
                                                                {{ $role->name }}
                                                            </h5>
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
                                                                data-url="{{ route('unAssignRole', ['uuid' => $data['admin']->uuid, 'id' => $role->id]) }}">
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
                                                    <h5 class="text-danger">No Role Found!</h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                    <hr>
                                    <div class="account-actions d-flex justify-content-end flex-wrap gap-3">
                                        <a data-toggle="modal" data-target="#changePasswordModal"
                                            class="btn btn-warning btn-lg d-inline-flex align-items-center gap-2 text-decoration-none account-action-btn">
                                            <i class="fas fa-key fs-5 me-1"></i>
                                            <span>Change Password</span>

                                        </a>
                                    </div>
                                    @include('users.member.myProfile.include.changePasswordModal')

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('homepage') }}">
                            <i class="fas fa-home"></i>
                            Back
                        </a>

                    </div>

                </div>

            </div>
        </div>

    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
