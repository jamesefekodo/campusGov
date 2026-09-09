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

                                            <p class="m-1 p-0">
                                                <span class="text-mute">
                                                    User Cap:
                                                </span>
                                                <span class="text-black">
                                                    @if ($data['role']->users->count() > 0)
                                                        {{ $data['role']->users->count() }} /
                                                    @endif

                                                    @if ($data['role']->user_cap == null)
                                                        Unlimited
                                                    @else
                                                        {{ $data['role']->user_cap }}
                                                    @endif (Currently 0)
                                                </span>
                                            </p>
                                            <p class="m-1 p-0">
                                                <span class="text-mute">
                                                    Assignment Scope:
                                                </span>
                                                <span class="text-black">
                                                    @switch($data['role']->assignment_scope)
                                                        @case('permanent')
                                                            Permanent
                                                        @break

                                                        @case('per_session')
                                                            Per Session
                                                        @break

                                                        @default
                                                    @endswitch
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
                                        {{-- user start --}}
                                        @if ($data['role']->users->isNotEmpty())
                                            <div class="px-3 pt-3 table-responsive">
                                                <table class="table">
                                                    @foreach ($data['role']->users as $user)
                                                        <tbody>
                                                            <tr class="text-black">
                                                                <th class="" scope="row">
                                                                    @if ($user->memberPersonalProfile)
                                                                        {{ $user->memberPersonalProfile->first_name }}
                                                                        {{ $user->memberPersonalProfile->last_name }}
                                                                        <a href="{{ route('viewMember', ['uuid' => $user->uuid]) }}"
                                                                            target="_blank"
                                                                            class="badge bg-secondary text-white"
                                                                            title="View Approver">
                                                                            <i class="fas fa-link"></i>
                                                                        </a>
                                                                    @endif
                                                                </th>
                                                                <td class="flex-wrap">
                                                                    <a href="javascript:void(0)"
                                                                        class="badge bg-red text-white pe-2 ps-3 py-2 rounded-3 unassign-role m-1"
                                                                        data-url="{{ route('unAssignRole', ['uuid' => $user->uuid, 'id' => $data['role']->id]) }}"
                                                                        style="cursor: pointer;">
                                                                        <i class="fas fa-trash me-1"></i>Un-assign Role
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
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                document.querySelectorAll('.unassign-role').forEach(button => {
                                                    button.addEventListener('click', function() {
                                                        const url = this.dataset.url;

                                                        Swal.fire({
                                                            title: 'Are you sure?',
                                                            text: "This user will lose all permissions attached to the role.",
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
                                        {{-- user end --}}
                                        <div class="d-flex align-items-center">
                                            <hr class="flex-grow-1">
                                            <span class="mx-2 text-black">Permissions</span>
                                            <hr class="flex-grow-1">
                                        </div>
                                        {{-- permission start --}}
                                        <livewire:role-permission-assignments :role="$data['role']" />

                                        {{-- permission end --}}

                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('userRoles') }}"> <i
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
