@extends('layouts.userLayout')
@section('title', $data['pageTitle'])


@php
    if (request()->routeIs('mediaPublicationsPendingApproval', 'mediaPublicationsPendingApprovalSearch')) {
        $searchRoute = route('mediaPublicationsPendingApprovalSearch');
        $backRoute = route('mediaPublicationsPendingApproval');
    } elseif (request()->routeIs('mediaPublicationsDraft', 'mediaPublicationsDraftSearch')) {
        $searchRoute = route('mediaPublicationsDraftSearch');
        $backRoute = route('mediaPublicationsDraft');
    } else {
        $searchRoute = route('mediaPublicationsSearch');
        $backRoute = route('mediaPublications');
    }
@endphp

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
                            data-target="#addNewMediaPublicationModal">
                            <i class="fas fa-plus"></i> Create New</button>
                    </div>
                </div>
                @include('users.admin.media.publications.include.addNewMediaPublicationModal')
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
                                {{ $data['pageTitle'] }}
                            </div>
                        </div>

                        <div class="table_head_item col-md-4">
                            <div class="custom_search">
                                <form type="GET" action="{{ $searchRoute }}">
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
                                                        Tags
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Author
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Created at
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['publications']->isEmpty())
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['publications'] as $publication)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td>
                                                                {{ $publication->title }}
                                                            </td>
                                                            <td>
                                                                {!! implode(' ', array_slice(explode(' ', strip_tags($publication->content)), 0, 10)) .
                                                                    (str_word_count(strip_tags($publication->content)) > 10 ? ' ...' : '') !!}
                                                            </td>
                                                            <td>
                                                                @if ($publication->tags)
                                                                    @foreach ($publication->tags as $tag)
                                                                        <span
                                                                            class="badge rounded-pill bg-secondary text-white px-2 py-1 m-1 shadow-sm">
                                                                            {{ $tag->name }}
                                                                        </span>
                                                                    @endforeach
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($publication->author)
                                                                    @if ($publication->authorUser?->memberPersonalProfile)
                                                                        {{ trim(
                                                                            $publication->authorUser->memberPersonalProfile->first_name .
                                                                                ' ' .
                                                                                ($publication->authorUser->memberPersonalProfile->middle_name
                                                                                    ? $publication->authorUser->memberPersonalProfile->middle_name . ' '
                                                                                    : '') .
                                                                                $publication->authorUser->memberPersonalProfile->last_name,
                                                                        ) }}

                                                                        <a href="{{ route('viewMember', ['uuid' => $publication->authorUser->uuid]) }}"
                                                                            target="_blank"
                                                                            class="badge bg-secondary text-white"
                                                                            title="View Initiator">
                                                                            <i class="fas fa-link"></i>
                                                                        </a>
                                                                    @else
                                                                        {{ $publication->author }}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($publication->created_at)->format('l, d F, Y h:i A') }}
                                                            </td>
                                                            <td>
                                                                @switch($publication->approval_status)
                                                                    @case('draft')
                                                                        <span
                                                                            class="badge bg-secondary-subtle text-secondary">Draft</span>
                                                                    @break

                                                                    @case('pending')
                                                                        <span class="badge bg-warning-subtle text-warning">Awaiting
                                                                            Approval</span>
                                                                    @break

                                                                    @case('approved')
                                                                        <span
                                                                            class="badge bg-success-subtle text-success">Approved</span>
                                                                    @break

                                                                    @case('declined')
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">Declined</span>
                                                                    @break

                                                                    @default
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">{{ $publication->approval_status }}</span>
                                                                @endswitch
                                                                @if ($publication->is_active == true)
                                                                    <span class="badge bg-green text-white">Published</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewMediaPublication', ['uuid' => $publication->uuid]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['publications'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            @switch($data['requestType'])
                                                @case('all')
                                                    {{ '' }}
                                                @break

                                                @case('search')
                                                    <a class="btn btn-success" href="{{ $backRoute }}"> <i
                                                            class="fas fa-chevron-left"></i>
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
