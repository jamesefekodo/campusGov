@extends('layouts.userLayout')
@section('title', $data['pageTitle'])


@php
    if (request()->routeIs('inActiveUserAccounts', 'inActiveUserAccountsSearch')) {
        $searchRoute = route('inActiveUserAccountsSearch');
        $backRoute = route('inActiveUserAccounts');
    } else {
        $searchRoute = route('memberAwaitingRegistrationSearch');
        $backRoute = route('memberAwaitingRegistration');
    }
@endphp

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.userManagement.members.include.inner_nav')
            </div>

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
                                                    <th scope="col" style="width: 10px" class="type-b-page-header">
                                                        S/N
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Member
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Previous Programme
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        New Programme
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Submitted on
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Account Status
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['reAdmissionRequests']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No records found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['reAdmissionRequests'] as $request)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ ($data['reAdmissionRequests']->currentPage() - 1) * $data['reAdmissionRequests']->perPage() + $loop->iteration }}
                                                            </th>
                                                            <td class="text-start">

                                                                <div class="d-flex align-items-center gap-3">
                                                                    {{-- Avatar --}}
                                                                    <div class="flex-shrink-0">
                                                                        @php
                                                                            $user = $request;
                                                                        @endphp
                                                                        @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                    </div>

                                                                    {{-- Name & Code --}}
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p
                                                                            class="fw-bold mb-0 text-truncate text-capitalize">
                                                                            @php
                                                                                $middleInitial = $request
                                                                                    ?->personalProfile?->middle_name
                                                                                    ? strtoupper(
                                                                                            substr(
                                                                                                $request
                                                                                                    ->personalProfile
                                                                                                    ->middle_name,
                                                                                                0,
                                                                                                1,
                                                                                            ),
                                                                                        ) . '. '
                                                                                    : '';
                                                                            @endphp
                                                                            @if ($request->personalProfile)
                                                                                {{ strtolower($request?->personalProfile?->first_name) . ' ' . $middleInitial . strtolower($request?->personalProfile?->last_name) }}
                                                                            @else
                                                                                {{ $request->username }}
                                                                            @endif
                                                                        </p>
                                                                        <p
                                                                            class="text-muted mb-0 small text-truncate text-capitalize">
                                                                            {{ $request?->personalProfile?->gender }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <p class="fw-bold mb-0 text-truncate">
                                                                    @php
                                                                        $academicStatus =
                                                                            $request->previous_academic_status;
                                                                        if ($academicStatus == 'student') {
                                                                            $currentStatus = 'Student';
                                                                        } elseif ($academicStatus == 'extra_year') {
                                                                            $currentStatus = 'Extra Year';
                                                                        } elseif ($academicStatus == 'alumni') {
                                                                            $currentStatus = 'Graduated';
                                                                        } elseif ($academicStatus == 'withdrawn') {
                                                                            $currentStatus = 'Withdrawn';
                                                                        } elseif ($academicStatus == 'suspended') {
                                                                            $currentStatus = 'Suspended';
                                                                        } elseif ($academicStatus == 'expelled') {
                                                                            $currentStatus = 'Expelled';
                                                                        } else {
                                                                            $currentStatus = 'Unknown';
                                                                        }
                                                                    @endphp
                                                                    {{ $request->previousDepartment->name }} -
                                                                    {{ $currentStatus }}
                                                                    {{ $request->previous_graduation_session . '/' . $request->previous_graduation_session + 1 }}
                                                                </p>
                                                                <p class="text-muted mb-0 small text-truncate">
                                                                    {{ $request?->previousProgram?->name }} (
                                                                    {{ $request?->previousProgram?->award }} -
                                                                    {{ $request?->previousProgramType?->type_name }})
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="fw-bold mb-0 text-truncate">
                                                                    {{ $request->newDepartment->name }}
                                                                    -
                                                                    {{ $request->new_admission_session . '/' . $request->new_admission_session + 1 }}
                                                                </p>
                                                                <p class="text-muted mb-0 small text-truncate">
                                                                    {{ $request?->newProgram?->name }} (
                                                                    {{ $request?->newProgram?->award }} -
                                                                    {{ $request?->newProgramType?->type_name }})
                                                                </p>
                                                            </td>
                                                            <td>
                                                                @switch($request->status)
                                                                    @case('pending')
                                                                        <span class="badge bg-yellow text-black">
                                                                            Pending
                                                                        </span>
                                                                    @break

                                                                    @case('approved')
                                                                        <span class="badge bg-green text-white">
                                                                            Approved
                                                                        </span>
                                                                    @break

                                                                    @case('declined')
                                                                        <span class="badge bg-red text-white">
                                                                            Declined
                                                                        </span>
                                                                    @break

                                                                    @default
                                                                        <span class="badge bg-red text-white">
                                                                            {{ $request->status }}
                                                                        </span>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                {{ $request->created_at->format('F j, Y, g:i A') }}
                                                            </td>

                                                            <td>
                                                                <a class="btn btn-primary btn-sm" {{-- href="{{ route('viewMember', ['uuid' => $request->uuid]) }}" --}}>
                                                                    <i class="fas fa-eye me-2"></i>
                                                                    View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['reAdmissionRequests'];
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

        </div>
    </div><!--//app-content-->
@endsection
