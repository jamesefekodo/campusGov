@extends('layouts.userLayout')
@section('title', 'Student Reps:: Arms/Bodies')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.leadership.tenures.include.inner_nav')
            </div>
            <div class="d-flex justify-content-end me-3">
                <div class="m-1">
                    <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                        data-target="#addNewStudentLeadershipArm">
                        <i class="fas fa-plus"></i> Add New</button>
                </div>
            </div>
            @include('users.admin.leadership.arms.include.addNewStudentLeadershipArm')


            <div class="individual-review-card-container row">
                <div class="col-md-1 individual-review-card text-black">
                    {{-- sidde menu --}}
                    @include('users.admin.leadership.studentReps.include.studentRepsSideMenu')
                </div>
                <div class="col-md-11 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                Student Reps:: Arms/Bodies
                            </div>
                        </div>
                    </div>
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
                                                        Name
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Description
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Leaders
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['arms']->isEmpty())
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['arms'] as $arm)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="">
                                                                {{ $arm->name }}
                                                            </td>
                                                            <td class="">
                                                                {{ $arm->description }}
                                                            </td>
                                                            <td class="">
                                                                {{ $arm->representatives->count() }}
                                                            </td>
                                                            <td class="">
                                                                @switch($arm->is_active)
                                                                    @case(true)
                                                                        <span class="badge bg-green text-white">Active</span>
                                                                    @break

                                                                    @default
                                                                        <span class="badge bg-red text-white">In-active</span>
                                                                @endswitch
                                                            </td>

                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <button type="button"
                                                                        class="btn btn-warning btn-sm me-2 d-inline-flex align-items-center"
                                                                        data-toggle="modal"
                                                                        data-target="#editStudentLeadershipArm_{{ $arm->slug }}">
                                                                        <i class="fas fa-arrows-rotate me-1"></i> Update
                                                                    </button>

                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm d-inline-flex align-items-center delete-confirm"
                                                                        data-url="{{ route('deleteStudentLeadershipArm', ['slug' => $arm->slug]) }}">
                                                                        <i class="fas fa-trash me-1"></i> Delete
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['arms'];
                                        @endphp

                                        @include('layouts.include.pagination')

                                    </div>


                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>

            {{-- ############################## --}}
            @if ($data['arms']->isNotEmpty())
                @foreach ($data['arms'] as $tenure)
                    @include('users.admin.leadership.arms.include.editStudentLeadershipArm')
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
            {{-- ############################## --}}





        </div>
    </div><!--//app-content-->
@endsection
