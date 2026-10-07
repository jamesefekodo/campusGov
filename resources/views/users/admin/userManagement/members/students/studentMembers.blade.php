@extends('layouts.userLayout')
@section('title', $data['pageTitle'])


@php
    if (request()->routeIs('nonStudentMembers', 'nonStudentMembersSearch', 'filterNonStudentMembers')) {
        $searchRoute = route('nonStudentMembersSearch');
        $filterRoute = route('filterNonStudentMembers');
        $backRoute = route('nonStudentMembers');
        $filterModalSelector = 'filterNonStudentMembersModal';
    } else {
        $searchRoute = route('studentMembersSearch');
        $filterRoute = route('filterStudentMembers');
        $backRoute = route('studentMembers');
        $filterModalSelector = 'filterStudentMembersModal';
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

                                <span class="ms-5 text-white-60 fs-7"><i class="fa fa-filter" data-toggle="modal"
                                        data-target="#{{ $filterModalSelector }}"></i></span>
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


                    {{-- filter modal start --}}
                    @if ($filterModalSelector == 'filterNonStudentMembersModal')
                        @include('users.admin.userManagement.members.students.include.filterNonStudentMembersModal')
                    @else
                        @include('users.admin.userManagement.members.students.include.filterStudentMembersModal')
                    @endif
                    {{-- filter modal end --}}
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
                                                        Program
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Department/Faculty
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Level
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Account Status
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['members']->isEmpty())
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No records found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['members'] as $member)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ ($data['members']->currentPage() - 1) * $data['members']->perPage() + $loop->iteration }}
                                                            </th>
                                                            <td class="text-start">

                                                                <div class="d-flex align-items-center gap-3">
                                                                    {{-- Avatar --}}
                                                                    <div class="flex-shrink-0">
                                                                        @php
                                                                            $user = $member->userProfile;
                                                                        @endphp
                                                                        @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                    </div>

                                                                    {{-- Name & Code --}}
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p
                                                                            class="fw-bold mb-0 text-truncate text-capitalize">
                                                                            @php
                                                                                $middleInitial = $member->userProfile
                                                                                    ->memberPersonalProfile->middle_name
                                                                                    ? strtoupper(
                                                                                            substr(
                                                                                                $member->userProfile
                                                                                                    ->memberPersonalProfile
                                                                                                    ->middle_name,
                                                                                                0,
                                                                                                1,
                                                                                            ),
                                                                                        ) . '. '
                                                                                    : '';
                                                                            @endphp

                                                                            {{ strtolower($member->userProfile->memberPersonalProfile->first_name) . ' ' . $middleInitial . strtolower($member->userProfile->memberPersonalProfile->last_name) }}
                                                                        </p>
                                                                        <p class="text-muted mb-0 small text-truncate">
                                                                            {{ strtoupper($member->matric_number) }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <p class="fw-bold mb-0 text-truncate">
                                                                    {{ $member->theProgram?->name }} @if ($member->theProgram?->short_name)
                                                                        ({{ $member->theProgram?->short_name }})
                                                                    @endif
                                                                </p>
                                                                <p class="text-muted mb-0 small text-truncate">
                                                                    {{ $member->programType?->type_name }}
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="fw-bold mb-0 text-truncate">
                                                                    {{ $member->theDepartment?->name }}
                                                                </p>
                                                                <p class="text-muted mb-0 small text-truncate">
                                                                    {{ $member->theFaculty?->name }} @if ($member->theFaculty?->acronym)
                                                                        ({{ $member->theFaculty?->acronym }})
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $academicInfo = $member;
                                                                @endphp

                                                                <span style="font-weight: bold">
                                                                    @if ($academicInfo?->academic_status == 'student')
                                                                        {{-- Current Level: --}}
                                                                    @elseif($academicInfo?->academic_status == 'alumni')
                                                                        Graduated:
                                                                    @else
                                                                        Status:
                                                                    @endif
                                                                </span>

                                                                @if ($academicInfo?->academic_status == 'student')
                                                                    {{ currentLevel($academicInfo->theProgram, studentCurrentLevel($academicInfo)) }}
                                                                @elseif($academicInfo?->academic_status == 'alumni')
                                                                    {{ $academicInfo->graduation_session
                                                                        ? $academicInfo->graduation_session . '/' . ($academicInfo->graduation_session + 1)
                                                                        : '-' }}
                                                                @else
                                                                    {{ ucfirst(str_replace('_', ' ', $academicInfo?->academic_status ?? '-')) }}

                                                                    @if ($academicInfo?->graduation_session)
                                                                        ({{ $academicInfo->graduation_session }}/{{ $academicInfo->graduation_session + 1 }})
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @switch($member->userProfile->account_status)
                                                                    @case(0)
                                                                        <span class="badge bg-yellow text-black">
                                                                            Unverified
                                                                        </span>
                                                                    @break

                                                                    @case(1)
                                                                        <span class="badge bg-blue text-white">
                                                                            Awaiting Verification
                                                                        </span>
                                                                    @break

                                                                    @case(2)
                                                                        <span class="badge bg-green text-white">
                                                                            Active
                                                                        </span>
                                                                    @break

                                                                    @case(3)
                                                                        <span class="badge bg-red text-white">
                                                                            Disabled
                                                                        </span>
                                                                    @break

                                                                    @default
                                                                        <span class="badge bg-red text-white">
                                                                            Unknown
                                                                        </span>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewMember', ['uuid' => $member->userProfile->uuid]) }}">
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
                                            $paginatedArray = $data['members'];
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
