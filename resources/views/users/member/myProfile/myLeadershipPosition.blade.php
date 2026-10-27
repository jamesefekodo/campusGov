@extends('layouts.userLayout')
@section('title', 'Leadership Positions')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <div class="mb-4">
                <div class="individual-review-card-container">
                    <div class="col-md-12 individual-review-card">
                        <div class="d-flex justify-content-end me-2">
                            <div class="m-1">
                                <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                    data-target="#addNewLeadershipPositionModal">
                                    <i class="fas fa-plus"></i> New Record</button>
                            </div>
                        </div>
                        @include('users.member.myProfile.include.addNewLeadershipPositionModal')
                        <div class="my_form_container row col-md-12 pb-5">
                            <h4 class="block text-center py-2 pt-3 text-uppercase type-b-page-header">
                                Leadership Positions
                            </h4>
                            <div class="p-4">

                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-bold">
                                            <i class="fas fa-user-tie me-2 text-primary"></i>
                                            Association Leadership Positions
                                        </h5>
                                    </div>



                                    <div class="card-body p-4">
                                        <div class="row">
                                            @forelse($data['member']->leadershipPositionsInAssociation as $leadership)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card shadow-sm h-100">
                                                        <div class="card-body">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <h5 class="mb-1 fw-bold">
                                                                        {{ $leadership->position->name ?? 'N/A' }}
                                                                    </h5>

                                                                    <small class="text-muted">

                                                                        @if ($leadership->tenure?->start_date)
                                                                            <br>
                                                                            {{ \Carbon\Carbon::parse($leadership->tenure->start_date)->format('F Y') }}
                                                                        @endif

                                                                        @if ($leadership->tenure?->end_date)
                                                                            -
                                                                            {{ \Carbon\Carbon::parse($leadership->tenure->end_date)->format('F Y') }}
                                                                        @endif
                                                                        @if ($leadership?->tenure?->name && !is_numeric($leadership->tenure->name))
                                                                            ({{ $leadership->tenure->name }})
                                                                        @endif
                                                                    </small>
                                                                </div>

                                                                <i class="fas fa-award fs-3 text-primary"></i>
                                                            </div>

                                                            <hr>

                                                            <p class="mb-2">
                                                                <span class="text-muted">Arm/Body:</span>
                                                                {{ $leadership->position->arm->name ?? 'N/A' }}
                                                            </p>

                                                            <p class="mb-2">
                                                                <span class="text-muted">Position:</span>
                                                                {{ $leadership->position->name ?? 'N/A' }}
                                                            </p>

                                                            <p class="mb-2">
                                                                <span class="text-muted">Tenure:</span>
                                                                {{ $leadership->tenure->academic_session }}/{{ $leadership->tenure->academic_session + 1 }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body text-center py-5">
                                                            <h5>No Association Leadership Positions Found</h5>

                                                            <p class="text-muted mb-0">
                                                                You are not assigned any association
                                                                leadership position.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0 fw-bold">
                                            <i class="fas fa-briefcase me-2 text-secondary"></i>
                                            Others
                                        </h5>
                                    </div>

                                    <div class="card-body p-4">
                                        <div class="row">
                                            @forelse($data['member']->memberLeadershipPosition as $position)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card shadow-sm border-1 h-100">

                                                        <div class="card-body">

                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <h5 class="mb-1 fw-bold">
                                                                        {{ $position->position }}
                                                                    </h5>

                                                                    <small class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($position->start_date)->format('F Y') }}
                                                                        @if ($position->end_date)
                                                                            -
                                                                            {{ \Carbon\Carbon::parse($position->end_date)->format('F Y') }}
                                                                        @endif
                                                                    </small>
                                                                </div>

                                                                <i class="fas fa-chess-king fs-3 text-secondary"></i>
                                                            </div>

                                                            <hr>

                                                            <p class="mb-2">
                                                                <strong>Position</strong><br>
                                                                {{ $position->position }}
                                                            </p>

                                                            <p class="mb-2">
                                                                <strong>Organization</strong><br>
                                                                {{ $position->business }}
                                                            </p>
                                                            <p class="mb-2">
                                                                <strong>Address</strong><br>
                                                                {{ $position->address }}
                                                            </p>

                                                        </div>

                                                        <div class="card-footer bg-white border-0">
                                                            <div class="d-flex justify-content-end">
                                                                <button class="btn btn-outline-primary btn-sm table_btn"
                                                                    data-toggle="modal"
                                                                    data-target="#editLeadershipPositionModal_{{ $position->id }}">
                                                                    <i class="fas fa-edit"></i> Update
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-outline-danger m-1 d-flex align-items-center delete-role"
                                                                    data-url="{{ route('deleteLeadershipPositionSubmit', ['id' => $position->id]) }}">
                                                                    <i class="fas fa-trash me-2"></i>
                                                                    <span>Delete</span>
                                                                </button>
                                                                <script>
                                                                    document.addEventListener('DOMContentLoaded', function() {
                                                                        document.querySelectorAll('.delete-role').forEach(button => {
                                                                            button.addEventListener('click', function() {

                                                                                const url = this.dataset.url;

                                                                                Swal.fire({
                                                                                    title: 'Are you sure?',
                                                                                    text: "Delete this record.",
                                                                                    icon: 'warning',
                                                                                    showCancelButton: true,
                                                                                    confirmButtonColor: '#d33',
                                                                                    cancelButtonColor: '#6c757d',
                                                                                    confirmButtonText: 'Yes, delete it!',
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
                                                        </div>
                                                        @include('users.member.myProfile.include.editLeadershipPositionModal')
                                                    </div>
                                                </div>

                                            @empty

                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body text-center py-5">
                                                            <h5>No Records Found</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforelse

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        {{-- Back Home --}}
                        <div class="w-100 justify-content-center text-center my-4">
                            <a class="btn btn-primary" href="{{ route('memberViewSelfProfile') }}"> <i
                                    class="fas fa-chevron-left"></i>
                                Back
                            </a>

                        </div>


                    </div>
                </div>


            </div>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
