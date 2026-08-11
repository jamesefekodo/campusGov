@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.finance.transactions.include.inner_nav')
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
                                <form type="GET" action="{{ route('paymentWaiverRequestsSearch') }}">
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
                                                        ID
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Due
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Member
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Date
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['paymentWaivers']->isEmpty())
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['paymentWaivers'] as $waiver)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start text-uppercase">
                                                                {{ $waiver->waiver_id }}
                                                            </td>
                                                            <td class="text-start">
                                                                @if ($waiver->due)
                                                                    @php
                                                                        $academic = $waiver->payer->memberAcademicInfo;
                                                                        $level = studentLevelAtDue(
                                                                            $academic,
                                                                            $waiver->due,
                                                                        );
                                                                    @endphp

                                                                    {{ $waiver->due->title }}
                                                                    ({{ $waiver->due->academic_year }}/{{ $waiver->due->academic_year + 1 }}
                                                                    Academic Session)
                                                                    -
                                                                    {{ currentLevel($academic->theProgram, $level) }}
                                                                @endif
                                                            </td>
                                                            <td class="text-start">
                                                                @if ($waiver->payer)
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        {{-- Avatar --}}
                                                                        <div class="flex-shrink-0">
                                                                            @php
                                                                                $user = $waiver->payer;
                                                                            @endphp
                                                                            @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                        </div>

                                                                        {{-- Name & Code --}}
                                                                        <div class="flex-grow-1 min-width-0 text-center">
                                                                            <p
                                                                                class="fw-bold mb-0 text-truncate text-capitalize">
                                                                                @php
                                                                                    $member =
                                                                                        $waiver->payer
                                                                                            ->memberAcademicInfo;
                                                                                    $middleInitial = $member
                                                                                        ->userProfile
                                                                                        ->memberPersonalProfile
                                                                                        ->middle_name
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
                                                                @else
                                                                    {{ $waiver->non_registered_payer }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($waiver->created_at)->format('l, d F, Y h:i A') }}
                                                            </td>
                                                            <td>
                                                                @switch($waiver->approval_status)
                                                                    @case('approved')
                                                                        <span class="badge bg-success text-white">Approved</span>
                                                                    @break

                                                                    @case('pending')
                                                                        <span class="badge bg-yellow text-black">Pending</span>
                                                                    @break

                                                                    @case('declined')
                                                                        <span class="badge bg-red text-white">Declined</span>
                                                                    @break

                                                                    @default
                                                                        <span
                                                                            class="badge bg-red text-white">{{ $waiver->approval_status }}</span>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewPaymentWaiver', ['uuid' => $waiver->uuid]) }}"
                                                                    >
                                                                    View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['paymentWaivers'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            @switch($data['requestType'])
                                                @case('all')
                                                    {{ '' }}
                                                @break

                                                @case('search')
                                                    <a class="btn btn-success" href="{{ route('paymentWaiverRequests') }}">
                                                        <i class="fas fa-chevron-left"></i>
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
