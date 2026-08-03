@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? '') . ' - Awards')
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
                                    Awards
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    @forelse($data['member']->memberAwards as $award)
                                        <div class="col-md-4 mb-3">
                                            <div class="card shadow-sm border-1 h-100">

                                                <div class="card-body">

                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h5 class="mb-1 fw-bold">
                                                                {{ $award->award }}
                                                            </h5>

                                                            <small class="text-muted">
                                                                {{ \Carbon\Carbon::parse($award->date)->format('F Y') }}
                                                            </small>
                                                        </div>

                                                        <i class="fas fa-award fs-3 text-secondary"></i>
                                                    </div>

                                                    <hr>

                                                    <p class="mb-2">
                                                        {{-- <strong>Institution</strong><br> --}}
                                                        {{ $award->award }}
                                                    </p>
                                                    <p class="mb-2">
                                                        <span class="text-secondary">by: </span> {{ $award->organization }}
                                                    </p>
                                                    <p class="mb-2">
                                                        {{ \Carbon\Carbon::parse($award->date)->format('F Y') }}
                                                    </p>

                                                </div>
                                            </div>
                                        </div>

                                    @empty

                                        <div class="col-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body text-center py-5">
                                                    <h5>No records found.</h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('viewMember', ['uuid' => $data['member']->uuid]) }}">
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
