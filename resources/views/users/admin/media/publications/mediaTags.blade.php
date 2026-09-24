@extends('layouts.userLayout')
@section('title', 'Media Publication :: Tags')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.media.publications.include.inner_nav')
            </div>
            @if (Auth::user()->hasPermission('elections.create'))
                <div class="d-flex justify-content-end me-3">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                            data-target="#addNewPublicationTagModal">
                            <i class="fas fa-plus"></i> Add New</button>
                    </div>
                </div>
                @include('users.admin.media.publications.include.addNewPublicationTagModal')
            @endif

            <div class="individual-review-card-container row">
                <div class="col-md-1 individual-review-card text-black">
                    {{-- sidde menu --}}
                    @include('users.admin.media.publications.include.publicationSideMenu')
                </div>
                <div class="col-md-11 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-8">
                            <div class="table_title">
                                Media Publication :: Tags
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
                                                        Tag
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Publications
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['mediaTags']->isEmpty())
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['mediaTags'] as $tag)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="">
                                                                {{ $tag->name }}
                                                            </td>
                                                            <td>
                                                                number of publications and view button
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-yellow text-black pe-2 ps-3 py-2 m-1 rounded-3"
                                                                    data-toggle="modal"
                                                                    data-target="#editTagModal_{{ $tag->id }}"
                                                                    style="cursor: pointer;">
                                                                    <i class="fas fa-edit me-1"></i>Update
                                                                </span>
                                                                <a href="javascript:void(0)"
                                                                    class="badge bg-red text-white pe-2 ps-3 py-2 rounded-3 delete-confirm m-1"
                                                                    data-toggle="modal" {{-- data-target="#changeIsPaidBreakModal" --}}
                                                                    data-url="{{ route('removePublicationTag', ['slug' => $tag->slug]) }}"
                                                                     style="cursor: pointer;">
                                                                    <i class="fas fa-trash me-1"></i>Remove
                                                                </a>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {

                                                            document.querySelectorAll('.delete-confirm').forEach(button => {

                                                                button.addEventListener('click', function() {

                                                                    const url = this.dataset.url;

                                                                    Swal.fire({
                                                                        title: 'Are you sure?',
                                                                        text: 'This item will be permanently deleted.',
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
                                                </tbody>
                                            @endif
                                        </table>

                                        @if ($data['mediaTags']->isNotEmpty())
                                            @foreach ($data['mediaTags'] as $tag)
                                                @include('users.admin.media.publications.include.editPublicationTagModal')
                                            @endforeach
                                        @endif
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['mediaTags'];
                                        @endphp

                                        @include('layouts.include.pagination')
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
