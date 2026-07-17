@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.activeAccount.payments.include.inner_nav') --}}
            </div>
            @if (Auth::user()->hasPermission('policy.create'))
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                            data-target="#addNewPermissionModal">
                            <i class="fas fa-plus"></i> New Policy</button>
                    </div>
                </div>
                @include('users.admin.config.systemPolicy.include.addNewPolicyModal')
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
                                <form type="GET" action="{{ route('systemPoliciesSearch') }}">
                                    @csrf
                                    <div class="input-group-append">
                                        <input type="search" class="table_search my-2 my-sm-0" name="searchKey"
                                            value="{{ $data['searchKey'] }}"><button
                                            type="submit"
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
                                                        Policy
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Applicable to
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['systemPolicies']->isEmpty())
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['systemPolicies'] as $policy)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start">
                                                                {{ $policy->title }}
                                                            </td>
                                                            <td class="text-start text-center">
                                                                @if ($policy->applicable_to_member === true)
                                                                    <span
                                                                        class="badge bg-blue text-white me-2">member</span>
                                                                @endif
                                                                @if ($policy->applicable_to_administrator === true)
                                                                    <span class="badge bg-blue text-white me-2">admin</span>
                                                                @endif
                                                            </td>

                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewPolicy', ['slug' => $policy->slug]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['systemPolicies'];
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
                                                        <a class="btn btn-success" href="{{ route('systemPolicies') }}"> <i
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





        </div>
    </div><!--//app-content-->
@endsection
