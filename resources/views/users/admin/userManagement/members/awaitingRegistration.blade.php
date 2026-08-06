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

                                            @if ($data['users']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No records found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['users'] as $user)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ ($data['users']->currentPage() - 1) * $data['users']->perPage() + $loop->iteration }}
                                                            </th>
                                                            <td class="text-start">

                                                                <div class="d-flex align-items-center gap-3">
                                                                    {{-- Avatar --}}
                                                                    <div class="flex-shrink-0">
                                                                        @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                    </div>

                                                                    {{-- Name & Code --}}
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p
                                                                            class="fw-bold mb-0 text-truncate text-capitalize">
                                                                            @php
                                                                                $middleInitial = $user
                                                                                    ?->memberPersonalProfile
                                                                                    ?->middle_name
                                                                                    ? strtoupper(
                                                                                            substr(
                                                                                                $user
                                                                                                    ->memberPersonalProfile
                                                                                                    ->middle_name,
                                                                                                0,
                                                                                                1,
                                                                                            ),
                                                                                        ) . '. '
                                                                                    : '';
                                                                            @endphp
                                                                            @if ($user->memberPersonalProfile)
                                                                                {{ strtolower($user?->memberPersonalProfile?->first_name) . ' ' . $middleInitial . strtolower($user?->memberPersonalProfile?->last_name) }}
                                                                            @else
                                                                                {{ $user->username }}
                                                                            @endif
                                                                        </p>
                                                                        <p class="text-muted mb-0 small text-truncate">
                                                                            {{ strtoupper($user?->memberPersonalProfile?->matric_number) }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <p class="fw-bold mb-0 text-truncate">
                                                                    @if ($user->memberAcademicInfo)
                                                                        {{ $user?->memberAcademicInfo?->theProgram?->name }}
                                                                        @if ($user?->memberAcademicInfo?->theProgram?->short_name)
                                                                            ({{ $user?->memberAcademicInfo?->theProgram?->short_name }})
                                                                        @endif
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </p>
                                                                <p class="text-muted mb-0 small text-truncate">
                                                                    {{ $user?->memberAcademicInfo?->programType?->type_name }}
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="fw-bold mb-0 text-truncate">
                                                                    {{ $user?->memberAcademicInfo?->theDepartment?->name }}
                                                                </p>
                                                                <p class="text-muted mb-0 small text-truncate">
                                                                    {{ $user?->memberAcademicInfo?->theFaculty?->name }}
                                                                    @if ($user?->memberAcademicInfo?->theFaculty?->acronym)
                                                                        ({{ $user?->memberAcademicInfo?->theFaculty?->acronym }})
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $academicInfo = $user->memberAcademicInfo;
                                                                @endphp

                                                                <span style="font-weight: bold">
                                                                    @if ($academicInfo?->academic_status == 'student')
                                                                        {{-- Current Level: --}}
                                                                    @elseif($academicInfo?->academic_status == 'alumni')
                                                                        {{-- Graduated: --}}
                                                                    @else
                                                                        {{-- Status: --}}
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
                                                                @switch($user->account_status)
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
                                                                @if ($user->account_status == 3)
                                                                    <a class="btn btn-warning btn-sm me-2 re-enable-btn"
                                                                        data-toggle="modal"
                                                                        data-url="{{ route('reEnableUserAccount', ['uuid' => $user->uuid]) }}"
                                                                        style="cursor: pointer;">
                                                                        <i class="fas fa-user-check me-2"></i>
                                                                        Enable</a>
                                                                @else
                                                                    <a class="btn btn-danger btn-sm me-2 disable-btn"
                                                                        data-toggle="modal"
                                                                        data-url="{{ route('disableUserAccount', ['uuid' => $user->uuid]) }}"
                                                                        style="cursor: pointer;">
                                                                        <i class="fas fa-user-slash me-2"></i>
                                                                        Disable</a>
                                                                @endif

                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewMember', ['uuid' => $user->uuid]) }}">
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
                                            $paginatedArray = $data['users'];
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



            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.disable-btn').forEach(button => {
                        button.addEventListener('click', function() {

                            const url = this.dataset.url;

                            Swal.fire({
                                title: 'Disable User Account?',
                                // text: "Disable User Account.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes!',
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

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.re-enable-btn').forEach(button => {
                        button.addEventListener('click', function() {

                            const url = this.dataset.url;

                            Swal.fire({
                                title: 'Re-enable User Account?',
                                text: "This account will be returned to the registration stage. The user must complete the necessary forms and submit them for validation.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes!',
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
        </div>
    </div><!--//app-content-->
@endsection
