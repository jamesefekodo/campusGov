@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

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
                                <form type="GET" action="{{ route('studentMembersSearch') }}">
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
                                                        Faculty/Department
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Level
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Account Status
                                                    </th>
                                                    <th scope="col" style="" class="type-b-page-header">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['members']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center">
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
                                                            {{-- <td class="text-start">

                                                                        <div class="d-flex align-items-center gap-3">
                                                                            Avatar
                                                                            <div class="flex-shrink-0">
                                                                                @include('users.company.employees.employees.include.profilePictureAndAvatarDisplay')
                                                                            </div>

                                                                            Name & Code
                                                                            <div
                                                                                class="flex-grow-1 min-width-0 text-center">
                                                                                <p
                                                                                    class="fw-bold mb-0 text-truncate text-capitalize">
                                                                                    @php
                                                                                        $middleInitial = $employee->middle_name
                                                                                            ? strtoupper(
                                                                                                    substr(
                                                                                                        $employee->middle_name,
                                                                                                        0,
                                                                                                        1,
                                                                                                    ),
                                                                                                ) . '. '
                                                                                            : '';
                                                                                    @endphp

                                                                                    {{ strtolower($employee->first_name) . ' ' . $middleInitial . strtolower($employee->last_name) }}
                                                                                </p>
                                                                                <p
                                                                                    class="text-muted mb-0 small text-truncate">
                                                                                    {{ strtoupper($employee->employee_code) }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="">
                                                                        <p class="fw-bold mb-0 text-truncate">
                                                                            @php
                                                                                $locale = 'name_' . currentLocale();

                                                                                $deptName = $employee->department
                                                                                    ? $employee->department->$locale ??
                                                                                        $employee->department->name
                                                                                    : '';
                                                                                $subDeptName = $employee->subDepartment
                                                                                    ? $employee->subDepartment
                                                                                            ->$locale ??
                                                                                        $employee->subDepartment->name
                                                                                    : '';
                                                                            @endphp

                                                                            {{ $deptName }}
                                                                            @if (!empty($subDeptName))
                                                                                <span class="badge text-muted">-
                                                                                    {{ $subDeptName }}</span>
                                                                            @endif

                                                                        </p>
                                                                        <p class="text-muted mb-0 small text-truncate">
                                                                            {{ $employee->position ?? '-' }}
                                                                        </p>
                                                                    </td>
                                                                    <td class="">
                                                                        <p class="fw-bold mb-0 text-truncate">
                                                                            @if ($employee->currentShift)
                                                                                @php
                                                                                    $locale = 'name_' . currentLocale();

                                                                                    $shiftName = $employee->currentShift
                                                                                        ? $employee->currentShift
                                                                                                ->$locale ??
                                                                                            $employee->currentShift
                                                                                                ->name
                                                                                        : '';
                                                                                @endphp
                                                                                {{ $shiftName }}
                                                                            @else
                                                                                -
                                                                            @endif

                                                                        </p>
                                                                        <p class="text-muted mb-0 small text-truncate">
                                                                            @if ($employee->shiftSchedule)
                                                                                @php
                                                                                    $locale = 'name_' . currentLocale();

                                                                                    $sheduleName = $employee->shiftSchedule
                                                                                        ? $employee->shiftSchedule
                                                                                                ->$locale ??
                                                                                            $employee->shiftSchedule
                                                                                                ->name
                                                                                        : '';
                                                                                @endphp
                                                                                {{ $sheduleName }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </p>
                                                                    </td>
                                                                    <td class="text-start">
                                                                        @if ($employee->resumption_date)
                                                                            {{ \Carbon\Carbon::parse($employee->resumption_date)->isoFormat('MMMM Do, YYYY') }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td class="">
                                                                        @switch($employee->employment_status)
                                                                            @case('active')
                                                                                <span
                                                                                    class="badge bg-success text-white">{{ t('app.employees.employmentStatus.active') }}</span>
                                                                            @break

                                                                            @case('probationary')
                                                                                <span
                                                                                    class="badge bg-blue text-white">{{ t('app.employees.employmentStatus.probationary') }}</span>
                                                                            @break

                                                                            @case('on_leave')
                                                                                <span
                                                                                    class="badge bg-warning text-white">{{ t('app.employees.employmentStatus.onLeave') }}</span>
                                                                            @break

                                                                            @case('suspended')
                                                                                <span
                                                                                    class="badge bg-warning text-white">{{ t('app.employees.employmentStatus.suspended') }}</span>
                                                                            @break

                                                                            @case('resigned')
                                                                                <span
                                                                                    class="badge bg-danger text-white">{{ t('app.employees.employmentStatus.resigned') }}</span>
                                                                            @break

                                                                            @case('terminated')
                                                                                <span
                                                                                    class="badge bg-danger text-white">{{ t('app.employees.employmentStatus.terminated') }}</span>
                                                                            @break

                                                                            @case('retired')
                                                                                <span
                                                                                    class="badge bg-danger text-white">{{ t('app.employees.employmentStatus.retired') }}</span>
                                                                            @break

                                                                            @case('end_of_contract')
                                                                                <span
                                                                                    class="badge bg-danger text-white">{{ t('app.employees.employmentStatus.endOfContract') }}</span>
                                                                            @break

                                                                            @case('awol')
                                                                                <span
                                                                                    class="badge bg-danger text-white">{{ t('app.employees.employmentStatus.awol') }}</span>
                                                                            @break

                                                                            @case('deceased')
                                                                                <span
                                                                                    class="badge bg-danger text-white">{{ t('app.employees.employmentStatus.deceased') }}</span>
                                                                            @break

                                                                            @default
                                                                                <span
                                                                                    class="badge bg-warning text-white">{{ $employee->employment_status }}</span>
                                                                        @endswitch
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn btn-primary btn-sm"
                                                                            href="{{ route('companyViewEmployeeProfileIndex', ['username' => $employee->username]) }}">
                                                                            <i class="fas fa-eye me-2"></i>
                                                                            {{ t('app.buttons.view') }}</a>
                                                                    </td> --}}
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
                                                        <a class="btn btn-success" href="{{ route('studentMembers') }}"> <i
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
