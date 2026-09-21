@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.userManagement.members.include.inner_nav') --}}
            </div>


            @if (Auth::user()->hasPermission('manage_admin'))
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        <a href="{{ route('createNewAdmin') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> New Admin</a>
                    </div>
                </div>
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
                                <form type="GET" action="{{ route('administratorsSearch') }}">
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
                                                        Admin
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Account Type
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Position
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Contacts
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Account Status
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['admins']->isEmpty())
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No records found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['admins'] as $admin)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}
                                                            </th>
                                                            <td class="text-start">

                                                                <div class="d-flex align-items-center gap-3">
                                                                    <div class="flex-shrink-0">

                                                                        @if (
                                                                            $admin->photo &&
                                                                                file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/users/admin/photo/' . $admin->photo)))
                                                                            <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/admin/photo/' . $admin->photo) }}"
                                                                                class="rounded-circle border" width="38"
                                                                                height="38" style="object-fit: cover;">
                                                                        @else
                                                                            <img src="{{ asset('system/images/default_profile_pic.webp') }}"
                                                                                class="rounded-circle border" width="38"
                                                                                height="38" style="object-fit: cover;">
                                                                        @endif

                                                                    </div>

                                                                    {{-- Name --}}
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p
                                                                            class="fw-bold mb-0 text-truncate text-capitalize">
                                                                            {{ strtolower($admin?->adminProfile?->name ?? '-') }}
                                                                        </p>
                                                                        <p class="text-muted mb-0 small text-truncate">
                                                                            {{ strtolower($admin->username) }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @switch($admin->account_type)
                                                                    @case(0)
                                                                        <span class="badge bg-red text-white">Root *</span>
                                                                    @break

                                                                    @case(1)
                                                                        <span class="badge bg-blue text-white">Admin</span>
                                                                    @break

                                                                    @default
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <p class="fw-bold mb-0 text-truncate text-capitalize">
                                                                    {{ strtolower($admin?->adminProfile?->name ?? '-') }}
                                                                </p>
                                                                <p class="text-muted mb-0 small text-truncate">
                                                                    {{ strtolower($admin?->adminProfile?->position ?? '-') }}
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="fw-bold mb-0 text-truncate">
                                                                    {{ strtolower($admin->phone ?? '-') }}
                                                                </p>
                                                                <p class="text-muted mb-0 small text-truncate">
                                                                    {{ strtolower($admin->email ?? '-') }}
                                                                </p>
                                                            </td>
                                                            <td>
                                                                @switch($admin->account_status)
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
                                                                    href="{{ route('viewAdmin', ['uuid' => $admin->uuid]) }}">
                                                                    <i class="fas fa-eye me-2"></i>
                                                                    View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    {{-- membersWithAdminPermission start --}}
                                                    @if ($data['membersWithAdminPermission'])
                                                        @foreach ($data['membersWithAdminPermission'] as $member)
                                                            <tr class=" align-middle text-center">
                                                                <th>*
                                                                </th>
                                                                <td class="text-start">

                                                                    <div class="d-flex align-items-center gap-3">
                                                                        {{-- Avatar --}}
                                                                        <div class="flex-shrink-0">
                                                                            @php
                                                                                $user = $member;
                                                                            @endphp
                                                                            @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                        </div>

                                                                        {{-- Name & Code --}}
                                                                        <div class="flex-grow-1 min-width-0 text-center">
                                                                            <p
                                                                                class="fw-bold mb-0 text-truncate text-capitalize">
                                                                                @php
                                                                                    $middleInitial = $member
                                                                                        ->memberPersonalProfile
                                                                                        ->middle_name
                                                                                        ? strtoupper(
                                                                                                substr(
                                                                                                    $member
                                                                                                        ->memberPersonalProfile
                                                                                                        ->middle_name,
                                                                                                    0,
                                                                                                    1,
                                                                                                ),
                                                                                            ) . '. '
                                                                                        : '';
                                                                                @endphp

                                                                                {{ strtolower($member->memberPersonalProfile->first_name) . ' ' . $middleInitial . strtolower($member->memberPersonalProfile->last_name) }}
                                                                            </p>
                                                                            <p class="text-muted mb-0 small text-truncate">
                                                                                {{ strtoupper($member->memberAcademicInfo->matric_number) }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-purple text-white">Member with
                                                                        Admin Access</span>
                                                                </td>
                                                                <td>
                                                                    -
                                                                </td>
                                                                <td>
                                                                    <p class="fw-bold mb-0 text-truncate">
                                                                        {{ strtolower($member->memberPersonalProfile->phone1 ?? '-') }}
                                                                    </p>
                                                                    <p class="text-muted mb-0 small text-truncate">
                                                                        {{ strtolower($member->memberPersonalProfile->email1 ?? '-') }}
                                                                    </p>
                                                                </td>
                                                                <td>
                                                                    @switch($member->account_status)
                                                                        @case('0')
                                                                            <span class="badge bg-yellow text-black">
                                                                                Unverified
                                                                            </span>
                                                                        @break

                                                                        @case('1')
                                                                            <span class="badge bg-blue text-white">
                                                                                Awaiting Verification
                                                                            </span>
                                                                        @break

                                                                        @case('2')
                                                                            <span class="badge bg-green text-white">
                                                                                Active
                                                                            </span>
                                                                        @break

                                                                        @case('3')
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
                                                                        href="{{ route('viewMember', ['uuid' => $member->uuid]) }}">
                                                                        <i class="fas fa-eye me-2"></i>
                                                                        View</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    {{-- membersWithAdminPermission end --}}
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        {{-- @php
                                            $paginatedArray = $data['admins'];
                                        @endphp

                                        @include('layouts.include.pagination') --}}
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            <div class="w-100 justify-content-center text-center mb-4">
                                                @switch($data['requestType'])
                                                    @case('all')
                                                        {{ '' }}
                                                    @break

                                                    @case('search')
                                                        <a class="btn btn-success" href="{{ route('administrators') }}"> <i
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
