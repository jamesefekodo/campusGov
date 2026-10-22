@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.activeAccount.payments.include.inner_nav') --}}
            </div>
            @if (Auth::user()->hasPermission('archive.create'))
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                            data-target="#addNewArchiveFileModal">
                            <i class="fas fa-plus"></i> New File</button>
                    </div>
                </div>
                {{-- @include('users.admin.archive.include.addNewArchiveFileModal') --}}
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
                                <form type="GET" {{-- action="{{ route('archiveSearch') }}" --}}>
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
                                                        Title
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Description
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Member Access
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Last Update
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>

                                            {{-- @if ($data['archiveFiles']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['archiveFiles'] as $file)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="">
                                                                {{ $file->title }}
                                                            </td>
                                                            <td class="">
                                                                {{ \Illuminate\Support\Str::words($file->description, 10, '...') }}
                                                            </td>
                                                            <td class="">
                                                                @switch($file->members_can_see)
                                                                    @case(true)
                                                                        <span class="badge bg-warning text-black">
                                                                            Visible to both members and administrators
                                                                        </span>
                                                                    @break

                                                                    @default
                                                                        <span class="badge bg-green text-white">
                                                                            Visible to only administrators
                                                                        </span>
                                                                @endswitch
                                                            </td>
                                                            <td class="text-start">
                                                                {{ \Carbon\Carbon::parse($file->updated_at)->format('l, d F, Y') }}
                                                            </td>
                                                            <td class="">
                                                                <a href="{{ asset('assets/tenant_' . tenant()->id . '/uploads/archive/files/' . $file->file) }}"
                                                                    download class="btn btn-warning btn-sm me-2">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewArchiveFile', ['uuid' => $file->uuid]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif --}}
                                        </table>
                                        {{-- Pagination --}}
                                        {{-- @php
                                            $paginatedArray = $data['archiveFiles'];
                                        @endphp

                                        @include('layouts.include.pagination') --}}

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
