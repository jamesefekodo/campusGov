@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? '') . ' - Leadership Positions')
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
                                    Leadership Positions
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0 fw-bold">
                                                <i class="fas fa-user-tie me-2 text-primary"></i>
                                                Association Leadership Positions
                                            </h5>

                                            @if (Auth::user()->hasPermission('leadership.manage'))
                                                <button type="button"
                                                    class="btn btn-primary btn-sm d-inline-flex align-items-center"
                                                    data-toggle="modal" data-target="#addNewStudentRepModal">
                                                    <i class="fas fa-plus me-1"></i>
                                                    Add Position
                                                </button>
                                            @endif
                                        </div>

                                        @include('users.admin.leadership.studentReps.include.addNewStudentRepModal')


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
                                                                                @endif @if ($leadership?->tenure->name && $leadership?->tenure->name !== $leadership?->tenure->academic_session)
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

                                                                @if (Auth::user()->hasPermission('leadership.manage'))
                                                                    <div class="d-flex justify-content-end">
                                                                        <button type="button"
                                                                            class="btn btn-warning btn-sm me-2"
                                                                            data-toggle="modal"
                                                                            data-target="#editAssociationLeadershipPosition_{{ $leadership->uuid }}">
                                                                            <i class="fas fa-pen me-1"></i>
                                                                            Update
                                                                        </button>

                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm delete-association-leadership"
                                                                            data-url="{{ route('deleteStudentLeadershipRep', $leadership->uuid) }}">
                                                                            <i class="fas fa-trash me-1"></i>
                                                                            Remove
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Update modal for this leadership record --}}
                                                    <div class="modal fade"
                                                        id="editStudentLeadershipRep_{{ $leadership->uuid }}"
                                                        tabindex="-1" aria-hidden="true">

                                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">
                                                                        Update:
                                                                        {{ $leadership->position->name ?? 'Leadership Position' }}
                                                                    </h5>

                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    {{-- Add your update form and fields here --}}
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        Cancel
                                                                    </button>

                                                                    <button type="submit" class="btn btn-primary">
                                                                        Update Position
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- ######################### --}}
                                                    @php
                                                        $leader = $leadership;
                                                    @endphp
                                                    @include('users.admin.leadership.studentReps.include.editStudentLeadershipRepModal')
                                                    {{-- ##################### --}}
                                                @empty
                                                    <div class="col-12">
                                                        <div class="card border-0 shadow-sm">
                                                            <div class="card-body text-center py-5">
                                                                <h5>No Association Leadership Positions Found</h5>

                                                                <p class="text-muted mb-0">
                                                                    This member has not been assigned any association
                                                                    leadership position.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>



                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            document.querySelectorAll('.delete-association-leadership').forEach(function(button) {
                                                button.addEventListener('click', function() {
                                                    const url = button.dataset.url;

                                                    Swal.fire({
                                                        title: 'Remove position?',
                                                        text: 'This association leadership position will be removed.',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#d33',
                                                        cancelButtonColor: '#6c757d',
                                                        confirmButtonText: 'Yes, remove it!',
                                                        cancelButtonText: 'Cancel',
                                                        reverseButtons: true
                                                    }).then(function(result) {
                                                        if (result.isConfirmed && url) {
                                                            window.location.href = url;
                                                        }
                                                    });
                                                });
                                            });
                                        });
                                    </script>


                                    {{-- ####################################### --}}
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
