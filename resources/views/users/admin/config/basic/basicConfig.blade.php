@extends('layouts.userLayout')
@section('title', 'Basic System Configuration')
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
                                        Basic System Configuration
                                    </h4>
                                    <div class="col-md-12 row">
                                        <div class="col-md-6">
                                            <div class="card stretch-full">
                                                <div class="card-body lead-status bg-soft-danger">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <h3 class="w-100 text-center text-danger text-uppercase">
                                                            Others</h3>
                                                    </div>
                                                </div>


                                                <div class="card-body row col-md-12">
                                                    <div class="col-md-6 border p-5">
                                                        <form id="swalForm" method="POST"
                                                            {{-- action="{{ route('systemSettingsUpdateMaintenanceStatus') }}" --}}
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                                <h4 class="mb-0">Maintenance Mode:</h4>

                                                                @php
                                                                    $isOn =
                                                                        $data['basicSystemSetting']
                                                                            ->maintenance_mode === true;
                                                                @endphp

                                                                <p>
                                                                    <span
                                                                        class="badge {{ $isOn ? 'bg-danger' : 'bg-success' }} text-white fs-5 px-4 py-2 rounded-pill">
                                                                        {{ $isOn ? 'Enabled' : 'Disabled' }}
                                                                    </span>
                                                                </p>


                                                            </div>

                                                            <div class="mb-4 mt-4 col-md-12">
                                                                <label class="form-label">Change Status</label>
                                                                <select class="form-select text-center"
                                                                    name="maintenance_mode" required>

                                                                    <option disabled
                                                                        {{ is_null($data['basicSystemSetting']) ? 'selected' : '' }}>
                                                                        - {{ t('app.select') }} -
                                                                    </option>

                                                                    <option value="1" @selected($data['basicSystemSetting']->maintenance_mode === true)>
                                                                        Enabled
                                                                    </option>

                                                                    <option value="0" @selected($data['basicSystemSetting']->maintenance_mode === false)>
                                                                        Disabled
                                                                    </option>

                                                                </select>

                                                                <p class="text-danger">
                                                                    {{ $errors->first('maintenance_mode') }}
                                                                </p>
                                                            </div>

                                                            <button type="submit"
                                                                class="btn btn-danger w-100 mt-2">Update</button>


                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="col-md-12 row">
                                            <p class="m-1 p-0">
                                                <span class="text-mute">
                                                    Name:
                                                </span>
                                                <span class="text-black">
                                                    {{-- {{ $data['role']->name }} --}}
                                                </span>
                                            </p>
                                            <p class="m-1 p-0">
                                                <span class="text-mute">
                                                    Description:
                                                </span>
                                                <span class="text-black">
                                                    {{-- {{ $data['role']->description }} --}}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            {{-- @if (Auth::user()->hasPermission('roles.update')) --}}
                                            <button class="btn btn-warning m-1 d-flex align-items-center"
                                                data-toggle="modal" data-target="#updateRoleModal">
                                                <i class="fas fa-edit me-2"></i>
                                                <span>Update</span>
                                            </button>
                                            {{-- @endif --}}

                                            {{-- @if (Auth::user()->hasPermission('roles.delete')) --}}
                                            <button type="button"
                                                class="btn btn-danger m-1 d-flex align-items-center delete-role"
                                                {{-- data-url="{{ route('deleteRole', ['uuid' => $data['role']->uuid]) }}" --}}>
                                                <i class="fas fa-trash me-2"></i>
                                                <span>Delete</span>
                                            </button>
                                            {{-- @endif --}}
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
            {{-- @include('users.admin.config.accessControl.roles.include.editNewRoleModal')
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
            </script> --}}
            {{-- modals end --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
