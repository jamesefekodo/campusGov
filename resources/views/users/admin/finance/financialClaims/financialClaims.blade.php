@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@php
    if (request()->routeIs('financialClaimsPendingApproval', 'financialClaimsPendingApprovalSearch')) {
        $searchRoute = route('financialClaimsPendingApprovalSearch');
        $backRoute = route('financialClaimsPendingApproval');
        $showAddNew = false;
    } else {
        $searchRoute = route('financialClaimsSearch');
        $backRoute = route('financialClaims');
        $showAddNew = true;
    }
@endphp

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @if (financialClaimPendingApproval() > 0)
                    @include('users.admin.finance.financialClaims.include.inner_nav')
                @endif
            </div>
            @if (Auth::user()->hasPermission('financial_claim.create') && $showAddNew == true)
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                            data-target="#addNewFinancialClaimModal">
                            <i class="fas fa-plus"></i> New Record</button>
                    </div>
                </div>
                @include('users.admin.finance.financialClaims.include.addNewFinancialClaims')
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
                                                        Type
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Amount
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Description
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Date
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Submitted
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['financialClaims']->isEmpty())
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['financialClaims'] as $claim)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td>
                                                                @switch($claim->claim_type)
                                                                    @case('credit')
                                                                        <span
                                                                            class="badge bg-success-subtle text-success">Credit</span>
                                                                    @break

                                                                    @case('debit')
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">Debit</span>
                                                                    @break

                                                                    @default
                                                                        <span
                                                                            class="badge bg-warning-subtle text-warning">{{ $claim->claim_type }}</span>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                ₦{{ number_format($claim->amount, 2) }}
                                                            </td>
                                                            <td>
                                                                {{ $claim->description }}
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($claim->date)->format('l, d F, Y') }}
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($claim->created_at)->format('l, d F, Y h:i A') }}
                                                            </td>
                                                            <td>
                                                                @switch($claim->approval_status)
                                                                    @case('approved')
                                                                        <span
                                                                            class="badge bg-success-subtle text-success">Approved</span>
                                                                    @break

                                                                    @case('declined')
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">Declined</span>
                                                                    @break

                                                                    @case('pending')
                                                                        <span
                                                                            class="badge bg-warning-subtle text-warning">Pending</span>
                                                                    @break

                                                                    @default
                                                                        <span
                                                                            class="badge bg-warning-subtle text-warning">{{ $claim->approval_status }}</span>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewFinancialClaim', ['uuid' => $claim->uuid]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['financialClaims'];
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
