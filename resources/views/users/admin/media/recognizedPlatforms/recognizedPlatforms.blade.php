@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.activeAccount.payments.include.inner_nav') --}}
            </div>
            @if (Auth::user()->hasPermission('roles.create'))
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal" data-target="#addPlatformModal">
                            <i class="fas fa-plus"></i> Add New</button>
                    </div>
                </div>
                @include('users.admin.media.recognizedPlatforms.include.addNewPlatformModal')
            @endif

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-8">
                            <div class="table_title">
                                {{ $data['pageTitle'] }}
                            </div>
                        </div>

                        <div class="table_head_item col-md-4">
                            <div class="custom_search">
                                <form type="GET" action="{{ route('recognizedSocialMediaPlatformsSearch') }}">
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
                                                        Platform
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Description
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Logo
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['platforms']->isEmpty())
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['platforms'] as $platform)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start">
                                                                {{ $platform->name }}
                                                            </td>
                                                            <td class="text-start">
                                                                {{ $platform->description }}
                                                            </td>
                                                            <td class="">
                                                                @switch($platform->is_active)
                                                                    @case(true)
                                                                        <span
                                                                            class="badge bg-success-subtle text-success">Active</span>
                                                                    @break

                                                                    @default
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">Inactive</span>
                                                                @endswitch
                                                            </td>
                                                            <td class="text-center">
                                                                @if (
                                                                    $platform->logo &&
                                                                        file_exists(public_path('assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo)))
                                                                    <img src="{{ asset('assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo) }}"
                                                                        alt="{{ $platform->name }}" width="50px">
                                                                @else
                                                                    <img src="{{ asset('system/images/logo_placeholder.png') }}"
                                                                        alt="Default Picture" width="50px">
                                                                @endif
                                                            </td>

                                                            <td>
                                                                <button class="btn btn-warning btn-sm me-2"
                                                                    data-toggle="modal"
                                                                    data-target="#editPlatformModal_{{ $platform->id }}">
                                                                    <i class="fas fa-edit me-2"></i>
                                                                    Update
                                                                </button>

                                                                <button type="button"
                                                                    class="btn btn-danger delete-platform"
                                                                    data-url="{{ route('deletePlatform', ['slug' => $platform->slug]) }}">
                                                                    <i class="fas fa-trash me-2"></i>
                                                                    <span>Delete</span>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>

                                        @if ($data['platforms']->isNotEmpty())
                                            @foreach ($data['platforms'] as $platform)
                                                @include('users.admin.media.recognizedPlatforms.include.editPlatformModal')
                                            @endforeach
                                        @endif
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                document.querySelectorAll('.delete-platform').forEach(button => {
                                                    button.addEventListener('click', function() {

                                                        const url = this.dataset.url;

                                                        Swal.fire({
                                                            title: 'Are you sure?',
                                                            text: "Remove platform.",
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
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['platforms'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            @switch($data['requestType'])
                                                @case('all')
                                                    {{ '' }}
                                                @break

                                                @case('search')
                                                    <a class="btn btn-success"
                                                        href="{{ route('recognizedSocialMediaPlatforms') }}">
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
    </div><!--//app-content-->
@endsection
