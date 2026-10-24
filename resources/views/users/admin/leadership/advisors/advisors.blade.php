@extends('layouts.userLayout')
@section('title', 'Advisors')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- @include('users.admin.activeAccount.payments.include.inner_nav') --}}
            </div>
            <div class="d-flex justify-content-end me-4">
                <div class="m-1">
                    <button class="btn btn-success btn-sm table_btn" data-toggle="modal" data-target="#addNewAdvisorModal">
                        <i class="fas fa-plus"></i> Add New Advisor</button>
                </div>
            </div>
            @include('users.admin.leadership.advisors.include.addNewAdvisorModal')

            <div class="individual-review-card-container row">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                Advisors
                                <span class="ms-5 badge bg-warning text-black" data-toggle="modal"
                                    data-target="#sessionSelector">
                                    {{ $data['academicSession'] }}/{{ $data['academicSession'] + 1 }}
                                    <i class="fa fa-filter"></i></span>

                            </div>
                        </div>
                    </div>
                    {{-- ###################### --}}
                    @include('users.admin.leadership.studentReps.include.filterStudentRepsByYearModal')
                    {{-- ###################### --}}
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
                                                        Name
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Position
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Period
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Remarks
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['advisors']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No advisors found for the
                                                            {{ $data['academicSession'] }}/{{ (int) $data['academicSession'] + 1 }}
                                                            academic session!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['advisors'] as $advisor)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-3">
                                                                    @php
                                                                        $photoPath =
                                                                            'assets/tenant_' .
                                                                            tenant()->id .
                                                                            '/uploads/leadership/advisor/' .
                                                                            $advisor->photo;

                                                                        $photoUrl =
                                                                            !empty($advisor->photo) &&
                                                                            file_exists(public_path($photoPath))
                                                                                ? asset($photoPath)
                                                                                : asset(
                                                                                    'system/images/default_profile_pic.webp',
                                                                                );
                                                                    @endphp

                                                                    <img src="{{ $photoUrl }}"
                                                                        alt="{{ $advisor->name }}" class="advisor-photo"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#advisorPhotoModal"
                                                                        data-photo="{{ $photoUrl }}"
                                                                        data-name="{{ $advisor->name }}"
                                                                        onerror="this.onerror=null; this.src='{{ asset('system/images/default_profile_pic.webp') }}';">

                                                                    <span>{{ $advisor->name }}</span>
                                                                </div>
                                                            </td>
                                                            <td class="">
                                                                {{ $advisor->position }}
                                                            </td>
                                                            <td class="">
                                                                {{ $advisor->start_date?->format('F Y') ?? 'N/A' }}
                                                                -
                                                                {{ $advisor->end_date?->format('F Y') ?? 'Present' }}
                                                            </td>
                                                            <td>
                                                                {{ $advisor->remarks ?? '-' }}
                                                            </td>
                                                            <td class="">
                                                                <button type="button"
                                                                    class="btn btn-warning btn-sm me-2 d-inline-flex align-items-center m-1"
                                                                    data-toggle="modal"
                                                                    data-target="#editAssociationAdvisor_{{ $advisor->uuid }}">
                                                                    <i class="fas fa-arrows-rotate me-1"></i> Update
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm d-inline-flex align-items-center delete-confirm m-1"
                                                                    data-url="{{ route('deleteAssociationAdvisor', ['uuid' => $advisor->uuid]) }}" >
                                                                    <i class="fas fa-trash me-1"></i> Delete
                                                                </button>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['advisors'];
                                        @endphp

                                        @include('layouts.include.pagination')

                                    </div>


                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>

            @if ($data['advisors']->isNotEmpty())
                @foreach ($data['advisors'] as $advisor)
                    @include('users.admin.leadership.advisors.include.editAssociationAdvisorModal')
                @endforeach
            @endif

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.delete-confirm').forEach(function(button) {
                        button.addEventListener('click', function() {
                            const url = button.dataset.url;

                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'This record will be permanently deleted.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes, delete!',
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

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.advisor-photo-input').forEach(function(fileInput) {
                        fileInput.addEventListener('change', function() {
                            const container = fileInput.closest('.advisor-upload-container');
                            const preview = container.querySelector('.advisor-preview-image');

                            const file = fileInput.files[0];

                            if (!file) {
                                preview.src = '{{ asset('system/images/default_profile_pic.webp') }}';
                                return;
                            }

                            const reader = new FileReader();

                            reader.onload = function(event) {
                                preview.src = event.target.result;
                            };

                            reader.readAsDataURL(file);
                        });
                    });
                });
            </script>

            {{-- ######################################### --}}
            <div class="modal fade" id="advisorPhotoModal" tabindex="-1" aria-labelledby="advisorPhotoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="advisorPhotoModalLabel">
                                Advisor Photo
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-center">
                            <img id="enlargedAdvisorPhoto" src="{{ asset('system/images/default_profile_pic.webp') }}"
                                alt="Advisor photo" class="img-fluid rounded"
                                style="max-height: 70vh; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('advisorPhotoModal');

                    modal.addEventListener('show.bs.modal', function(event) {
                        const trigger = event.relatedTarget;

                        document.getElementById('enlargedAdvisorPhoto').src =
                            trigger.dataset.photo;

                        document.getElementById('advisorPhotoModalLabel').textContent =
                            trigger.dataset.name;
                    });
                });
            </script>

            <style>
                .advisor-photo {
                    width: 55px;
                    height: 55px;
                    object-fit: cover;
                    border-radius: 50%;
                    border: 2px solid #dee2e6;
                    cursor: pointer;
                    transition: transform 0.25s ease, box-shadow 0.25s ease;
                    flex-shrink: 0;
                }

                .advisor-photo:hover {
                    transform: scale(1.25);
                    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
                    z-index: 10;
                    position: relative;
                }
            </style>
            {{-- ############################ --}}

        </div>
    </div><!--//app-content-->
@endsection
