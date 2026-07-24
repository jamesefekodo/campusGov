@extends('layouts.userLayout')
@section('title', 'Skills')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <div class="mb-4">
                <div class="individual-review-card-container">
                    <div class="col-md-12 individual-review-card">
                        <div class="d-flex justify-content-end me-2">
                            <div class="m-1">
                                <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                    data-target="#addNewSkillModal">
                                    <i class="fas fa-plus"></i> Add New</button>
                            </div>
                        </div>
                        @include('users.member.myProfile.include.addNewSkillModal')
                        <div class="my_form_container row col-md-12 pb-5">
                            <h4 class="block text-center py-2 pt-3 text-uppercase type-b-page-header">
                                Skills
                            </h4>
                            <div class="row">

                                @forelse($data['member']->memberSkills as $skill)
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm border-1 h-100">

                                            <div class="card-body">

                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h5 class="mb-1 fw-bold">
                                                            {{ $skill->skill }}
                                                        </h5>
                                                    </div>

                                                    <i class="fas fa-cog fs-3 text-secondary"></i>
                                                </div>

                                                <hr>

                                                <p class="mb-2">
                                                    {{-- <strong>Institution</strong><br> --}}
                                                    {{ $skill->skill }}
                                                </p>

                                                <p class="mb-3">
                                                    {{-- <strong>Address</strong><br> --}}
                                                    {{ $skill->description }}
                                                </p>

                                            </div>

                                            <div class="card-footer bg-white border-0">
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-outline-primary btn-sm table_btn"
                                                        data-toggle="modal"
                                                        data-target="#editSkillModal_{{ $skill->id }}">
                                                        <i class="fas fa-edit"></i> Update
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-outline-danger m-1 d-flex align-items-center delete-role"
                                                        data-url="{{ route('memberSelfDeleteSkill', ['id' => $skill->id]) }}">
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
                                            @include('users.member.myProfile.include.editSkillModal')
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
