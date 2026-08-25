@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.elections.include.inner_nav')
            </div>
            @if (Auth::user()->hasPermission('elections.create'))
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                            data-target="#addNewElectionModal">
                            <i class="fas fa-plus"></i> Create New</button>
                    </div>
                </div>
                @include('users.admin.elections.include.addNewElectionModal')
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
                                <form type="GET" action="{{ route('allElectionsSearch') }}">
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
                                                        Election
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Description
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Voting Method
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Application Period
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Voting Period
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['elections']->isEmpty())
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['elections'] as $election)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start"> <span class="nav-link-text">
                                                                    {{ $election->title }}
                                                                    {{ $election->academic_session . '/' . $election->academic_session + 1 }}
                                                                    &nbsp;
                                                                    @if ($election->status == 'awaiting_approval')
                                                                        <span
                                                                            class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                                                                            style="font-size: 11px"> <i
                                                                                class="fas fa-info p-1"></i>
                                                                    @endif
                                                            </td>
                                                            <td class="text-start">
                                                                {{ $election->description }}
                                                            </td>
                                                            <td class="text-start text-capitalize">
                                                                {{ $election->voting_method }}
                                                            </td>
                                                            <td class="text-start">
                                                                @php
                                                                    $formSalesStarts = \Carbon\Carbon::parse(
                                                                        $election->form_sales_starts,
                                                                    );
                                                                    $formSalesEnds = \Carbon\Carbon::parse(
                                                                        $election->form_sales_ends,
                                                                    );
                                                                @endphp

                                                                {{ $formSalesStarts->format('l, d F, Y') }}

                                                                @if ($formSalesStarts->isSameDay($formSalesEnds))
                                                                    {{ $formSalesStarts->format('g:i A') }} to
                                                                    {{ $formSalesEnds->format('g:i A') }}
                                                                @else
                                                                    to {{ $formSalesEnds->format('l, d F, Y') }}
                                                                @endif
                                                            </td>
                                                            <td class="text-start">
                                                                @php
                                                                    $votingStarts = \Carbon\Carbon::parse(
                                                                        $election->voting_starts,
                                                                    );
                                                                    $votingEnds = \Carbon\Carbon::parse(
                                                                        $election->voting_ends,
                                                                    );
                                                                @endphp

                                                                {{ $votingStarts->format('l, d F, Y') }}

                                                                @if ($votingStarts->isSameDay($votingEnds))
                                                                    {{ $votingStarts->format('g:i A') }} to
                                                                    {{ $votingEnds->format('g:i A') }}
                                                                @else
                                                                    to {{ $votingEnds->format('l, d F, Y') }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @switch($election->status)
                                                                    @case('draft')
                                                                        <span
                                                                            class="badge bg-secondary-subtle text-secondary">Draft</span>
                                                                    @break

                                                                    @case('awaiting_approval')
                                                                        <span class="badge bg-warning-subtle text-warning">Awaiting
                                                                            Approval</span>
                                                                    @break

                                                                    @case('new')
                                                                        <span
                                                                            class="badge bg-primary-subtle text-primary">New</span>
                                                                    @break

                                                                    @case('registration')
                                                                        <span
                                                                            class="badge bg-primary-subtle text-primary">Registration</span>
                                                                    @break

                                                                    @case('screening')
                                                                        <span
                                                                            class="badge bg-warning-subtle text-warning">Screening</span>
                                                                    @break

                                                                    @case('campaign')
                                                                        <span
                                                                            class="badge bg-warning-subtle text-warning">Campaign</span>
                                                                    @break

                                                                    @case('voting')
                                                                        <span class="badge bg-blue text-white">Ongoing</span>
                                                                    @break

                                                                    @case('completed')
                                                                        <span
                                                                            class="badge bg-success-subtle text-success">Completed</span>
                                                                    @break

                                                                    @case('cancelled')
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">Cancelled</span>
                                                                    @break

                                                                    @case('suspended')
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">Suspended</span>
                                                                    @break

                                                                    @default
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">{{ $election->status }}</span>
                                                                @endswitch
                                                                @if ($election->status == 'voting')
                                                                    <span class="badge bg-danger text-white"><i class="fas fa-exclamation-circle"></i></span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewElection', ['uuid' => $election->uuid]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['elections'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            @switch($data['requestType'])
                                                @case('all')
                                                    {{ '' }}
                                                @break

                                                @case('search')
                                                    <a class="btn btn-success" href="{{ route('allElections') }}"> <i
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
