@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? '') . ' - Academic History')
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
                                    Academic History
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    @forelse($data['member']->memberAcademicHistories as $history)
                                        <div class="col-md-4 mb-3">
                                            <div class="card shadow-sm border-1 h-100">

                                                <div class="card-body p-4">

                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h5 class="mb-1 fw-bold">
                                                                {{ $history->certificate }}
                                                            </h5>

                                                            <small class="text-muted">
                                                                {{ \Carbon\Carbon::parse($history->date)->format('F Y') }}
                                                            </small>
                                                        </div>

                                                        <i class="fas fa-graduation-cap fs-3 text-secondary"></i>
                                                    </div>

                                                    <hr>

                                                    <p class="mb-2">
                                                        <strong>Institution</strong><br>
                                                        {{ $history->institution }}
                                                    </p>

                                                    @if ($history->address)
                                                        <p class="mb-3">
                                                            <strong>Address</strong><br>
                                                            {{ $history->address }}
                                                        </p>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>

                                    @empty

                                        <div class="col-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body text-center py-5">
                                                    <i class="fas fa-graduation-cap fa-3x text-secondary mb-3"></i>
                                                    <h5>No Academic History</h5>
                                                    <p class="text-muted mb-0">
                                                        No academic qualifications have been added yet.
                                                    </p>
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
                        <a class="btn btn-primary" href="{{ route('viewMMember', ['uuid' => $data['member']->uuid]) }}">
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
