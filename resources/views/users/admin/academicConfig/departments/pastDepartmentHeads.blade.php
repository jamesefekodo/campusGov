@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.activeAccount.payments.include.inner_nav') --}}
            </div>
            <div class="d-flex justify-content-end">
                @if (Auth::user()->hasPermission('department.create'))
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                            data-target="#addPastNewDepartmentHeadModal">
                            <i class="fas fa-plus"></i> Add New</button>
                    </div>
                @endif
                <div class="m-1">
                    <a class="btn btn-danger btn-sm table_btn"
                        href="{{ route('viewDepartment', ['uuid' => $data['department']->uuid]) }}">
                        <i class="fas fa-chevron-left"></i> Department</a>
                </div>
            </div>
            @include('users.admin.academicConfig.departments.include.addPastNewDepartmentHeadModal')

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-8">
                            <div class="table_title">
                                {{ $data['pageTitle'] }} @if ($data['department']->administrative_head_title)
                                    ({{ $data['department']->administrative_head_title }})
                                @endif
                            </div>
                        </div>

                        <div class="table_head_item col-md-4">
                            <div class="custom_search">
                                <form type="GET"
                                    action="{{ route('pastDepartmentHeadsSearch', ['uuid' => $data['department']->uuid]) }}">
                                    @csrf
                                    <div class="input-group-append">
                                        <input type="search" class="table_search my-2 my-sm-0" name="searchKey"
                                            value="{{ $data['searchKey'] }}"><button type="submit"
                                            class="table_search_button btn btn-primary
                                                "><i
                                                class="fas fa-search"></i></button>
                                    </div>
                                </form>
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
                                                        Tenure
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['pastHeads']->isEmpty())
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['pastHeads'] as $head)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start">
                                                                {{ $head->name }}
                                                            </td>
                                                            <td class="text-start text-center">
                                                                @if ($head->assumed_office_on)
                                                                    {{ \Carbon\Carbon::parse($head->assumed_office_on)->format('l, d F, Y') }}
                                                                @else
                                                                    -
                                                                    @endif to @if ($head->tenure_end_on)
                                                                        {{ \Carbon\Carbon::parse($head->tenure_end_on)->format('l, d F, Y') }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                            </td>

                                                            <td>
                                                                <a class="btn btn-primary btn-sm m-1" data-toggle="modal"
                                                                    data-target="#viewPastDepartmentHeadModalModal_{{ $head->uuid }}">
                                                                    <i class="fas fa-eye"></i>
                                                                    View</a>
                                                                <button class="btn btn-warning btn-sm m-1"
                                                                    data-toggle="modal"
                                                                    data-target="#editPastDepartmentHeadModalModal_{{ $head->uuid }}">
                                                                    <i class="fas fa-edit"></i>
                                                                    Update</button>
                                                                @if (Auth::user()->hasPermission('department.delete'))
                                                                    <button class="btn btn-danger btn-sm m-1 delete-head"
                                                                        data-toggle="modal"
                                                                        data-url="{{ route('deletePastDepartmentHead', ['uuid' => $head->uuid]) }}">
                                                                        <i class="fas fa-trash"></i>
                                                                        Delete</button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['pastHeads'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            <div class="w-100 justify-content-center text-center mb-4">
                                                @switch($data['requestType'])
                                                    @case('all')
                                                        {{ '' }}
                                                    @break

                                                    @case('search')
                                                        <a class="btn btn-success"
                                                            href="{{ route('pastDepartmentHeads', ['uuid' => $data['department']->uuid]) }}">
                                                            <i class="fas fa-chevron-left"></i>
                                                            Back
                                                        </a>
                                                    @break

                                                    @default
                                                @endswitch

                                            </div>

                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('click', function(e) {
                    const button = e.target.closest('.delete-head');

                    if (!button) return;

                    const url = button.dataset.url;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This record will be permanently deleted.',
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
            </script>
            {{-- models start --}}
            @if ($data['pastHeads']->isNotEmpty())
                @foreach ($data['pastHeads'] as $head)
                    @include('users.admin.academicConfig.departments.include.viewPastDepartmentHeadModalModal')
                    @include('users.admin.academicConfig.departments.include.editPastDepartmentHeadModalModal')
                @endforeach
            @endif
            {{-- modals end --}}



        </div>
    </div><!--//app-content-->
@endsection
