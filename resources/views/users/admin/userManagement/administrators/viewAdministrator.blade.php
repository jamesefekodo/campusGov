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
                                        <span style="font-weight: bold">Created:</span>
                                        <span class="text-black">
                                            <span class="text-capitalize">
                                                @if ($data['admin']?->adminProfile?->created_at)
                                                    {{ \Carbon\Carbon::parse($data['admin']?->adminProfile?->created_at)->format('l, d F, Y h:i A') }}
                                                @endif
                                                -
                                                @if ($data['admin']?->adminProfile?->created_by)

                                                    @if ($data['admin']->createdBy?->memberPersonalProfile)
                                                        {{ trim(
                                                            $data['admin']->createdBy->memberPersonalProfile->first_name .
                                                                ' ' .
                                                                ($data['admin']->createdBy->memberPersonalProfile->middle_name
                                                                    ? $data['admin']->createdBy->memberPersonalProfile->middle_name . ' '
                                                                    : '') .
                                                                $data['admin']->createdBy->memberPersonalProfile->last_name,
                                                        ) }}
                                                    @elseif ($data['admin']?->adminProfile?->createdBy)
                                                        {{ $data['admin']?->adminProfile?->createdBy?->adminProfile?->name }}
                                                    @else
                                                        {{ $data['admin']->created_by }}
                                                    @endif

                                                    @if ($data['admin']->createdBy)
                                                        <a href="{{ route('viewMember', ['uuid' => $data['admin']->createdBy->uuid]) }}"
                                                            target="_blank" class="badge bg-secondary text-white"
                                                            title="View Initiator">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif
                                                @endif

                                            </span>
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
                                    @if (Auth::user()->hasPermission('manage_admin'))
                                        <div class="account-actions d-flex justify-content-end flex-wrap gap-3">
                                            {{-- Update Profile --}}
                                            <a data-toggle="modal" data-target="#updateAdminProfileModal"
                                                class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2 text-decoration-none account-action-btn">
                                                <i class="fas fa-user-pen fs-5 me-1"></i>
                                                <span>Update Profile</span>
                                            </a>

                                            {{-- Change Password --}}
                                            <a data-toggle="modal" data-target="#changePasswordModal"
                                                class="btn btn-warning btn-lg d-inline-flex align-items-center gap-2 text-decoration-none account-action-btn">
                                                <i class="fas fa-key fs-5 me-1"></i>
                                                <span>Change Password</span>

                                            </a>

                                            {{-- Assign New Role --}}
                                            @if (Auth::user()->hasPermission('roles.assign') && $data['admin']->account_type !== 0)
                                                <a data-toggle="modal" data-target="#assignUserNewRoleModal"
                                                    class="btn btn-info btn-lg d-inline-flex align-items-center gap-2 text-decoration-none account-action-btn">
                                                    <i class="fas fa-user-tag fs-5 me-1"></i>
                                                    <span>Assign New Role</span>
                                                </a>
                                                @php
                                                    $data['member'] = $data['admin'];
                                                @endphp
                                            @endif


                                            {{-- Disable Account --}}
                                            @if ($data['admin']->account_type !== 0)
                                                @if ($data['admin']->account_status == 2)
                                                    <form id="disable-account-form"
                                                        action="{{ route('toggleAdminAccountStatus', $data['admin']->uuid) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="button"
                                                            class="btn btn-danger btn-lg d-inline-flex align-items-center gap-2 account-action-btn"
                                                            onclick="confirmAccountAction('disable', 'disable-account-form')">
                                                            <i class="fas fa-ban fs-5 me-1"></i>
                                                            <span>Disable Account</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form id="activate-account-form"
                                                        action="{{ route('toggleAdminAccountStatus', $data['admin']->uuid) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="button"
                                                            class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2 account-action-btn"
                                                            onclick="confirmAccountAction('activate', 'activate-account-form')">
                                                            <i class="fas fa-check-circle fs-5 me-1"></i>
                                                            <span>Activate Account</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                            <script>
                                                function confirmAccountAction(action, formId) {
                                                    const isDisable = action === 'disable';

                                                    Swal.fire({
                                                        title: isDisable ? 'Disable this account?' : 'Activate this account?',
                                                        text: isDisable ?
                                                            'The user will no longer be able to log in until reactivated.' :
                                                            'The user will regain access to their account.',
                                                        icon: isDisable ? 'warning' : 'question',
                                                        showCancelButton: true,
                                                        confirmButtonText: isDisable ? 'Yes, disable it' : 'Yes, activate it',
                                                        cancelButtonText: 'Cancel',
                                                        confirmButtonColor: isDisable ? '#dc3545' : '#0d6efd',
                                                        cancelButtonColor: '#6c757d',
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            document.getElementById(formId).submit();
                                                        }
                                                    });
                                                }
                                            </script>
                                        </div>

                                        @include('users.admin.userManagement.administrators.include.updateAdminProfileModal')
                                        @include('users.member.myProfile.include.changePasswordModal')
                                        @if ($data['admin']->account_type !== 0)
                                            @include('users.admin.userManagement.members.profile.include.assignUserNewRoleModal')
                                        @endif
                                        @push('styles')
                                            <style>
                                                .account-action-btn {
                                                    padding: 0.75rem 1.25rem;
                                                    font-weight: 600;
                                                    font-size: 0.95rem;
                                                    border-radius: 0.5rem;
                                                    transition: transform 0.12s ease, box-shadow 0.12s ease, background-color 0.15s ease, border-color 0.15s ease;
                                                }

                                                .account-action-btn:hover {
                                                    transform: translateY(-1px);
                                                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
                                                }

                                                .btn-info {
                                                    color: #000;
                                                }
                                            </style>
                                        @endpush
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('administrators') }}">
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
