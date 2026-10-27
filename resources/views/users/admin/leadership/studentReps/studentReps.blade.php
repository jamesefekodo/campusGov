@extends('layouts.userLayout')
@section('title', 'Student Representatives')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.activeAccount.payments.include.inner_nav') --}}
            </div>
            <div class="d-flex justify-content-end me-4">
                <div class="m-1">
                    <button class="btn btn-success btn-sm table_btn" data-toggle="modal" data-target="#addNewStudentRepModal">
                        <i class="fas fa-plus"></i> Add New Representative</button>
                </div>
            </div>
            @include('users.admin.leadership.studentReps.include.addNewStudentRepModal')

            <div class="individual-review-card-container row">
                <div class="col-md-1 individual-review-card text-black">
                    {{-- sidde menu --}}
                    @include('users.admin.leadership.studentReps.include.studentRepsSideMenu')
                </div>
                <div class="col-md-11 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                Student Representatives
                                <span class="ms-5 badge bg-warning text-black" data-toggle="modal"
                                    data-target="#sessionSelector">
                                    {{ $data['academicSession'] }}/{{ $data['academicSession'] + 1 }}
                                    <i class="fa fa-filter"></i></span>

                            </div>
                        </div>
                    </div>
                    {{-- ###################### --}}
                    @include('users.admin.leadership.studentReps.include.filterStudentRepsByYearModal')
                    {{-- ###################### --}}
                    <div class="col-md-12 m-auto">
                        <div class="my_form_container">
                            <div class=" pb-4">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered p-3">
                                            <thead class="justify-content-center text-center align-middle">
                                                <tr>
                                                    <th scope="col" style="width: 10px" class="table_header">S/N
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Rep
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Position
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Tenure
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Remarks
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['studentLeaders']->isEmpty())
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No student representative/leader records found for the
                                                            {{ $data['academicSession'] }}/{{ (int) $data['academicSession'] + 1 }}
                                                            academic session!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['studentLeaders'] as $leader)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    {{-- Avatar --}}
                                                                    <div class="flex-shrink-0">
                                                                        @php
                                                                            $user = $leader->theRep;
                                                                        @endphp
                                                                        @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                    </div>

                                                                    {{-- Name & Code --}}
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p
                                                                            class="fw-bold mb-0 text-truncate text-capitalize">
                                                                            @php
                                                                                $middleInitial = $leader->theRep
                                                                                    ->memberPersonalProfile->middle_name
                                                                                    ? strtoupper(
                                                                                            substr(
                                                                                                $leader->theRep
                                                                                                    ->memberPersonalProfile
                                                                                                    ->middle_name,
                                                                                                0,
                                                                                                1,
                                                                                            ),
                                                                                        ) . '. '
                                                                                    : '';
                                                                            @endphp

                                                                            {{ strtolower($leader->theRep->memberPersonalProfile->first_name) . ' ' . $middleInitial . strtolower($leader->theRep->memberPersonalProfile->last_name) }}
                                                                        </p>
                                                                        <p class="text-muted mb-0 small text-truncate">
                                                                            {{ strtoupper($leader->theRep->memberAcademicInfo->matric_number) }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="">
                                                                <div class="flex-grow-1 min-width-0 text-center">
                                                                    <p class="fw-bold mb-0 text-truncate text-capitalize">
                                                                        {{ $leader?->position?->name }} @if ($data['thePresident'] && $data['thePresident']->username == $leader->username)
                                                                            <i class="fas fa-crown text-warning ms-1"
                                                                                title="President"></i>
                                                                        @endif
                                                                    </p>
                                                                    <p class="text-muted mb-0 small text-truncate">
                                                                        @if ($leader->position && $leader->position->name !== $leader->position->description)
                                                                            {{ $leader->position->description }} -
                                                                        @endif
                                                                        {{ $leader?->position?->arm?->name }}
                                                                    </p>
                                                                </div>
                                                            </td>
                                                            <td class="">
                                                                <div class="flex-grow-1 min-width-0 text-center">
                                                                    <p class="fw-bold mb-0 text-truncate text-capitalize">
                                                                        {{ $leader?->tenure?->academic_session }}/{{ $leader?->tenure?->academic_session + 1 }}
                                                                    </p>
                                                                    <p class="text-muted mb-0 small text-truncate">
                                                                        @if ($leader?->tenure?->name && !is_numeric($leader->tenure->name))
                                                                            ({{ $leader->tenure->name }})
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </td>
                                                            <td class="">
                                                                @switch($leader->is_active)
                                                                    @case(true)
                                                                        <span class="badge bg-green text-white">Active</span>
                                                                    @break

                                                                    @default
                                                                        <span class="badge bg-red text-white">In-active</span>
                                                                @endswitch
                                                            </td>
                                                            <td class="">
                                                                {{ $leader?->remarks ?? '-' }}
                                                            </td>
                                                            <td class="">
                                                                <button type="button"
                                                                    class="btn btn-warning btn-sm me-2 d-inline-flex align-items-center m-1"
                                                                    data-toggle="modal"
                                                                    data-target="#editStudentLeadershipRep_{{ $leader->uuid }}">
                                                                    <i class="fas fa-arrows-rotate me-1"></i> Update
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm d-inline-flex align-items-center delete-confirm m-1"
                                                                    data-url="{{ route('deleteStudentLeadershipRep', ['uuid' => $leader->uuid]) }}">
                                                                    <i class="fas fa-trash me-1"></i> Delete
                                                                </button>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['studentLeaders'];
                                        @endphp

                                        @include('layouts.include.pagination')

                                    </div>


                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>

            @if ($data['studentLeaders']->isNotEmpty())
                @foreach ($data['studentLeaders'] as $leader)
                    @include('users.admin.leadership.studentReps.include.editStudentLeadershipRepModal')
                @endforeach
            @endif

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.delete-confirm').forEach(function(button) {
                        button.addEventListener('click', function() {
                            const url = button.dataset.url;

                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'This record will be permanently deleted.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes, delete!',
                                cancelButtonText: 'Cancel',
                                reverseButtons: true
                            }).then(function(result) {
                                if (result.isConfirmed && url) {
                                    window.location.href = url;
                                }
                            });
                        });
                    });
                });
            </script>



        </div>
    </div><!--//app-content-->
@endsection
