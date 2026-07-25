@extends('layouts.userLayout')
@section('title', 'Employments And Businesses')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <div class="mb-4">
                <div class="individual-review-card-container">
                    <div class="col-md-12 individual-review-card">
                        <div class="d-flex justify-content-end me-2">
                            <div class="m-1">
                                <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                    data-target="#addNewEmploymentAndBusinessModal">
                                    <i class="fas fa-plus"></i> Add New</button>
                            </div>
                        </div>
                        @include('users.member.myProfile.include.addNewEmploymentAndBusinessModal')
                        <div class="my_form_container row col-md-12 pb-5">
                            <h4 class="block text-center py-2 pt-3 text-uppercase type-b-page-header">
                                Employments And Businesses
                            </h4>
                            <div class="row">

                                @forelse($data['member']->memberEmploymentAndBusiness as $record)
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm border-1 h-100">

                                            <div class="card-body">

                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h5 class="mb-1 fw-bold">
                                                            {{ $record->business }}
                                                        </h5>

                                                        <small class="text-muted">
                                                            {{ $record->position }}
                                                        </small>
                                                    </div>

                                                    <i class="fas fa-briefcase fs-3 text-secondary"></i>
                                                </div>

                                                <hr>

                                                <p class="mb-2">
                                                    {{-- <strong>Institution</strong><br> --}}
                                                    {{ \Carbon\Carbon::parse($record->start_date)->format('F Y') }}
                                                    @if ($record->end_date)
                                                        - {{ \Carbon\Carbon::parse($record->end_date)->format('F Y') }}
                                                    @endif

                                                </p>

                                            </div>

                                            <div class="card-footer bg-white border-0">
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-outline-primary btn-sm table_btn"
                                                        data-toggle="modal"
                                                        data-target="#editEmploymentAndBusinessModal_{{ $record->id }}">
                                                        <i class="fas fa-edit"></i> Update
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-outline-danger m-1 d-flex align-items-center delete-role"
                                                        data-url="{{ route('deleteBusinessRecordSubmit', ['id' => $record->id]) }}">
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
                                            @include('users.member.myProfile.include.editEmploymentAndBusinessModal')
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
