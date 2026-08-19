@extends('layouts.userLayout')
@section('title', 'My Applications | ' . $data['election']->title)

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.member.elections.include.view_election_inner_nav')
            </div>

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                My Applications | {{ $data['election']->title }}
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
                                                        Position
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Application Type
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['applications']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['applications'] as $application)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start"> <span class="nav-link-text">
                                                                    {{ $application->election->title }}
                                                            </td>
                                                            <td class="text-start"> <span class="nav-link-text">
                                                                    {{ $application->position->position }}
                                                            </td>
                                                            <td class="text-start text-capitalize"> <span
                                                                    class="nav-link-text">
                                                                    {{ $application->application_type }}
                                                            </td>
                                                            <td class=""> <span class="nav-link-text">
                                                                    @switch($application->status)
                                                                        @case('draft')
                                                                            <span class="badge bg-primary-subtle text-primary">
                                                                                Draft
                                                                            </span>
                                                                        @break

                                                                        @case('pending_approval')
                                                                            <span class="badge bg-warning-subtle text-warning">
                                                                                Pending Approval
                                                                            </span>
                                                                        @break

                                                                        @case('approved')
                                                                            <span class="badge bg-success-subtle text-success">
                                                                                Approved
                                                                            </span>
                                                                        @break

                                                                        @case('declined')
                                                                            <span class="badge bg-danger-subtle text-danger">
                                                                                Declined
                                                                            </span>
                                                                        @break

                                                                        @default
                                                                            <span class="badge bg-danger-subtle text-danger">
                                                                                {{ $application->status }}
                                                                            </span>
                                                                    @endswitch
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewMyElectionApplication', ['uuid' => $application->uuid]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['applications'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            <a class="btn btn-success"
                                                href="{{ route('memberViewElection', ['uuid' => $data['election']->uuid]) }}">
                                                <i class="fas fa-chevron-left"></i>
                                                Back
                                            </a>
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
