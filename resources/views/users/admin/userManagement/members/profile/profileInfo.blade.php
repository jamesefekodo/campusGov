@extends('layouts.userLayout')
@section('title', ($data['member']?->memberPersonalProfile?->first_name ?? $data['member']?->username) . ' ' .
    ($data['member']?->memberPersonalProfile?->last_name ?? '') . ' - Personal Profile')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')
                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Personal Profile
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">First Name:</span>
                                        <span class="text-black">
                                            {{ $data['member']?->memberPersonalProfile?->first_name }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Middle Name:</span>
                                        <span class="text-black">
                                            {{ $data['member']?->memberPersonalProfile?->middle_name }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Last Name:</span>
                                        <span class="text-black">
                                            {{ $data['member']?->memberPersonalProfile?->last_name }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Gender:</span>
                                        <span class="text-black">
                                            @switch($data['member']?->memberPersonalProfile?->gender)
                                                @case('male')
                                                    Male
                                                @break

                                                @case('female')
                                                    Female
                                                @break

                                                @default
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Date of Birth:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['member']?->memberPersonalProfile?->dob)->format('l, d F, Y') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Nationality:</span>
                                        <span class="text-black">
                                            {{ $data['member']?->memberPersonalProfile?->nationality }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">State of Origin:</span>
                                        <span class="text-black">
                                            {{ $data['member']?->memberPersonalProfile?->state_of_origin }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">LGA:</span>
                                        <span class="text-black">
                                            {{ $data['member']?->memberPersonalProfile?->lga_of_origin }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Phone:</span>
                                        <span class="text-black">
                                            {{ $data['member']?->memberPersonalProfile?->phone1 }} @if ($data['member']?->memberPersonalProfile?->phone2)
                                                / {{ $data['member']?->memberPersonalProfile?->phone2 }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Email:</span>
                                        <span class="text-black">
                                            {{ $data['member']?->memberPersonalProfile?->email1 }} @if ($data['member']?->memberPersonalProfile?->email2)
                                                / {{ $data['member']?->memberPersonalProfile?->email2 }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Address:</span>
                                        <span class="text-black">
                                            {{ $data['member']?->memberPersonalProfile?->address1 }} @if ($data['member']?->memberPersonalProfile?->address2)
                                                / {{ $data['member']?->memberPersonalProfile?->address2 }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Verification:</span>
                                        <span class="text-black">
                                            @switch($data['member']?->memberPersonalProfile?->is_verified)
                                                @case(true)
                                                    <span class="badge bg-success-subtle text-success">Verified</span>
                                                @break

                                                @default
                                                    <span class="badge bg-danger-subtle text-danger">Not-Verified</span>
                                            @endswitch
                                            @if ($data['member']?->memberPersonalProfile?->verifiedBy?->memberPersonalProfile)
                                                by
                                                <a href="{{ route('viewMember', ['uuid' => $data['member']?->uuid]) }}">
                                                    {{ trim(
                                                        ($data['member']?->memberPersonalProfile?->verifiedBy?->memberPersonalProfile?->first_name ?? '') .
                                                            ' ' .
                                                            ($data['member']?->memberPersonalProfile?->verifiedBy?->memberPersonalProfile?->last_name ?? ''),
                                                    ) }}
                                                </a>
                                            @endif |
                                            {{ \Carbon\Carbon::parse($data['member']?->memberPersonalProfile?->verified_at)->format('l, d F Y \a\t h:i A') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Login Mfa:</span>
                                        @livewire('user-mfa-toggle', ['userId' => $data['member']->id])
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('viewMember', ['uuid' => $data['member']?->uuid]) }}">
                            <i class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>

                </div>

            </div>
        </div>

    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
