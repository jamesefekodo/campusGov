@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@php
    $searchRoute = route('activeMisconductCasesSearch');
    $navBackRoute = route('activeMisconductCases');
    $showAddBtn = true;

    if (in_array(Route::currentRouteName(), ['resolvedMisconductCases', 'resolvedMisconductCasesSearch'])) {
        $searchRoute = route('resolvedMisconductCasesSearch');
        $navBackRoute = route('resolvedMisconductCases');
        $showAddBtn = false;
    }
@endphp

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.activeAccount.payments.include.inner_nav') --}}
            </div>
            @if (Auth::user()->hasPermission('misconducts.create') && $showAddBtn == true)
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                            data-target="#addNewMisconductModal">
                            <i class="fas fa-plus"></i> Create New</button>
                    </div>
                </div>
                @include('users.admin.misconducts.include.addNewMisconductModal')
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
                                                        Case ID
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Member
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Type
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Reported On
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['misconducts']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['misconducts'] as $misconduct)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start">
                                                                <span class="text-uppercase me-2">
                                                                    {{ $misconduct->case_id }}
                                                                </span>

                                                                @switch($misconduct->status)
                                                                    @case('pending')
                                                                        <span class="badge bg-gray text-white">Pending</span>
                                                                    @break

                                                                    @case('under_investigation')
                                                                        <span class="badge bg-yellow text-black">Under
                                                                            Investigation</span>
                                                                    @break

                                                                    @case('partially_resolved')
                                                                        <span class="badge bg-blue text-white">Partially
                                                                            Resolved</span>
                                                                    @break

                                                                    @case('resolved')
                                                                        <span class="badge bg-green text-white">Resolved</span>
                                                                    @break

                                                                    @default
                                                                @endswitch
                                                            </td>
                                                            <td class="text-start">
                                                                @if ($misconduct->userProfile)
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        {{-- Avatar --}}
                                                                        <div class="flex-shrink-0">
                                                                            @php
                                                                                $user = $misconduct->userProfile;
                                                                            @endphp
                                                                            @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                        </div>

                                                                        {{-- Name & Code --}}
                                                                        <div class="flex-grow-1 min-width-0 text-center">
                                                                            <p
                                                                                class="fw-bold mb-0 text-truncate text-capitalize">
                                                                                @php
                                                                                    $middleInitial = $misconduct
                                                                                        ->userProfile
                                                                                        ->memberPersonalProfile
                                                                                        ->middle_name
                                                                                        ? strtoupper(
                                                                                                substr(
                                                                                                    $misconduct
                                                                                                        ->userProfile
                                                                                                        ->memberPersonalProfile
                                                                                                        ->middle_name,
                                                                                                    0,
                                                                                                    1,
                                                                                                ),
                                                                                            ) . '. '
                                                                                        : '';
                                                                                @endphp

                                                                                {{ strtolower($misconduct->userProfile->memberPersonalProfile->first_name) . ' ' . $middleInitial . strtolower($misconduct->userProfile->memberPersonalProfile->last_name) }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    {{ $misconduct->username }}
                                                                @endif
                                                            </td>
                                                            <td class="">
                                                                @switch($misconduct->misconduct_type)
                                                                    @case('abuse_of_office')
                                                                        Abuse of Office
                                                                    @break

                                                                    @case('bullying')
                                                                        Bullying
                                                                    @break

                                                                    @case('conflict_of_interest')
                                                                        Conflict of Interest
                                                                    @break

                                                                    @case('cyberbullying')
                                                                        Cyberbullying
                                                                    @break

                                                                    @case('financial_misconduct')
                                                                        Financial Misconduct
                                                                    @break

                                                                    @case('fighting')
                                                                        Fighting
                                                                    @break

                                                                    @case('inciting_unrest')
                                                                        Inciting Unrest
                                                                    @break

                                                                    @case('insubordination')
                                                                        Insubordination
                                                                    @break

                                                                    @case('misinformation')
                                                                        Misinformation
                                                                    @break

                                                                    @case('neglect_of_duties')
                                                                        Neglect of Duties
                                                                    @break

                                                                    @case('substance_abuse')
                                                                        Substance Abuse
                                                                    @break

                                                                    @case('theft')
                                                                        Theft
                                                                    @break

                                                                    @case('unauthorized_meetings')
                                                                        Unauthorized Meetings
                                                                    @break

                                                                    @case('vandalism')
                                                                        Vandalism
                                                                    @break

                                                                    @case('verbal_abuse')
                                                                        Verbal Abuse
                                                                    @break

                                                                    @default
                                                                @endswitch
                                                            </td>
                                                            <td class="text-start">
                                                                {{ \Carbon\Carbon::parse($misconduct->report_date)->format('l, d F, Y') }}
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewMisconductCase', ['uuid' => $misconduct->uuid]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['misconducts'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            @switch($data['requestType'])
                                                @case('all')
                                                    {{ '' }}
                                                @break

                                                @case('search')
                                                    <a class="btn btn-success" href="{{ $navBackRoute }}"> <i
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
