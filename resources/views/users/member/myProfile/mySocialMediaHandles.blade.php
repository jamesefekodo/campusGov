@extends('layouts.userLayout')
@section('title', 'Social Media Handles')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <div class="mb-4">
                <div class="individual-review-card-container">
                    <div class="col-md-12 individual-review-card">
                        <div class="d-flex justify-content-end me-2">
                            <div class="m-1">
                                <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                    data-target="#addSocialMediaModal">
                                    <i class="fas fa-plus"></i> New Record</button>
                            </div>
                        </div>
                        @include('users.member.myProfile.include.addNewSocialMediaHandleModal')
                        <div class="my_form_container row col-md-12 pb-5">
                            <h4 class="block text-center py-2 pt-3 text-uppercase type-b-page-header">
                                Social Media Handles
                            </h4>
                            <div class="row">

                                @forelse($data['member']->memberSocialMediaHandle as $handle)
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm border-1 h-100">

                                            <div class="card-body">

                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h5 class="mb-1 fw-bold">
                                                            {{ $handle->thePlatform->name }}
                                                        </h5>
                                                    </div>

                                                    @if (
                                                        $handle->thePlatform->logo &&
                                                            file_exists(public_path('assets/uploads/users/media/recognizedPlatforms/logo/' . $handle->thePlatform->logo)))
                                                        <img src="{{ asset('assets/uploads/users/media/recognizedPlatforms/logo/' . $handle->thePlatform->logo) }}"
                                                            alt="{{ $handle->thePlatform->name }}" width="50px">
                                                    @else
                                                        <img src="{{ asset('system/images/logo_placeholder.png') }}"
                                                            alt="Default Picture" width="50px">
                                                    @endif
                                                </div>

                                                <hr>

                                                <p class="mb-2">
                                                    <strong>Handle</strong><br>
                                                    @if ($handle->link)
                                                        @php
                                                            $url = \Illuminate\Support\Str::startsWith($handle->link, [
                                                                'http://',
                                                                'https://',
                                                            ])
                                                                ? $handle->link
                                                                : 'https://' . $handle->link;
                                                        @endphp

                                                        <a href="{{ $url }}" target="_blank"
                                                            rel="noopener noreferrer">
                                                            {{ $handle->handle }}
                                                        </a>
                                                    @endif
                                                </p>

                                            </div>

                                            <div class="card-footer bg-white border-0">
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-outline-primary btn-sm table_btn"
                                                        data-toggle="modal"
                                                        data-target="#editSocialMediaHandleModal_{{ $handle->id }}">
                                                        <i class="fas fa-edit"></i> Update
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-outline-danger m-1 d-flex align-items-center delete-role"
                                                        data-url="{{ route('employeeDeleteSocialMediaHandleSubmit', ['id' => $handle->id]) }}">
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
                                            @include('users.member.myProfile.include.editSocialMediaHandleModal')
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
