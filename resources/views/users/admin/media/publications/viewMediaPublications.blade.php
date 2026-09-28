@extends('layouts.userLayout')
@section('title', $data['publication']->title)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        {{ $data['publication']->title }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        @php
                                            $coverImage = $data['publication']->cover_image ?? null;
                                            $coverPath = $coverImage
                                                ? public_path(
                                                    'assets/tenant_' .
                                                        tenantId() .
                                                        '/uploads/media/publications/' .
                                                        $coverImage,
                                                )
                                                : null;
                                            $hasCoverImage = $coverPath && file_exists($coverPath);
                                        @endphp

                                        <div class="publication-cover-wrapper mb-4">
                                            @if ($hasCoverImage)
                                                <img src="{{ asset('assets/tenant_' . tenantId() . '/uploads/media/publications/' . $coverImage) }}"
                                                    alt="{{ $data['publication']->header ?? 'Publication cover image' }}"
                                                    class="publication-cover-image" loading="lazy">
                                            @else
                                                <div class="publication-cover-placeholder">
                                                    <i class="fas fa-image"></i>
                                                    <span>No cover image available</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-12 row px-3">
                                            <div class="col-md-12 p-1">
                                                <span class="text-black">
                                                    {!! $data['publication']->content !!}
                                                </span>
                                            </div>
                                            <div class="col-md-12 p-1">
                                                <hr>
                                                <span class="text-black">
                                                    @if ($data['publication']->tags)
                                                        @foreach ($data['publication']->tags as $tag)
                                                            <a
                                                                href="{{ route('mediaPublicationWihTag', ['slug' => $tag->slug]) }}">
                                                                <span
                                                                    class="badge rounded-pill bg-secondary text-white px-2 py-1 m-1 shadow-sm">
                                                                    {{ $tag->name }}
                                                                </span>
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </span>
                                            </div>

                                            @include('users.admin.media.publications.include.sharePublication')

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Author:</span>
                                                <span class="text-black">
                                                    @if ($data['publication']->author)
                                                        @if ($data['publication']->authorUser?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['publication']->authorUser->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['publication']->authorUser->memberPersonalProfile->middle_name
                                                                        ? $data['publication']->authorUser->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['publication']->authorUser->memberPersonalProfile->last_name,
                                                            ) }}

                                                            <a href="{{ route('viewMember', ['uuid' => $data['publication']->authorUser->uuid]) }}"
                                                                target="_blank" class="badge bg-secondary text-white"
                                                                title="View Initiator">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                        @else
                                                            {{ $data['publication']->author }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $createdAt = \Carbon\Carbon::parse(
                                                            $data['publication']->created_at,
                                                        );
                                                    @endphp

                                                    @if ($createdAt->isToday())
                                                        Today, {{ $createdAt->format('h:i A') }}
                                                    @elseif ($createdAt->isYesterday())
                                                        Yesterday, {{ $createdAt->format('h:i A') }}
                                                    @else
                                                        {{ $createdAt->format('l, d F, Y h:i A') }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Last Update:</span>
                                                <span class="text-black">
                                                    @if ($data['publication']->last_update_by)
                                                        @if ($data['publication']->lastUpdateUser?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['publication']->lastUpdateUser->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['publication']->lastUpdateUser->memberPersonalProfile->middle_name
                                                                        ? $data['publication']->lastUpdateUser->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['publication']->lastUpdateUser->memberPersonalProfile->last_name,
                                                            ) }}

                                                            <a href="{{ route('viewMember', ['uuid' => $data['publication']->lastUpdateUser->uuid]) }}"
                                                                target="_blank" class="badge bg-secondary text-white"
                                                                title="View Initiator">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                        @else
                                                            {{ $data['publication']->last_update_by }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $updatedAt = \Carbon\Carbon::parse(
                                                            $data['publication']->last_update,
                                                        );
                                                    @endphp

                                                    @if ($updatedAt->isToday())
                                                        Today, {{ $updatedAt->format('h:i A') }}
                                                    @elseif ($updatedAt->isYesterday())
                                                        Yesterday, {{ $updatedAt->format('h:i A') }}
                                                    @else
                                                        {{ $updatedAt->format('l, d F, Y h:i A') }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Status:</span>
                                                <span class="text-black">
                                                    @switch($data['publication']->approval_status)
                                                        @case('draft')
                                                            <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                                        @break

                                                        @case('pending')
                                                            <span class="badge bg-warning-subtle text-warning">Awaiting
                                                                Approval</span>
                                                        @break

                                                        @case('approved')
                                                            <span class="badge bg-success-subtle text-success">Approved</span>
                                                        @break

                                                        @case('declined')
                                                            <span class="badge bg-danger-subtle text-danger">Declined</span>
                                                        @break

                                                        @default
                                                            <span
                                                                class="badge bg-danger-subtle text-danger">{{ $data['publication']->approval_status }}</span>
                                                    @endswitch
                                                    @if ($data['publication']->is_active == true)
                                                        <span class="badge bg-green text-white">Published</span>
                                                    @endif
                                                </span>
                                            </div>
                                            @if ($data['publication']->approved_by)
                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Last Status Updated:</span>
                                                    <span class="text-black">
                                                        @if ($data['publication']->approved_by)
                                                            @if ($data['publication']->approvedByUser?->memberPersonalProfile)
                                                                {{ trim(
                                                                    $data['publication']->approvedByUser->memberPersonalProfile->first_name .
                                                                        ' ' .
                                                                        ($data['publication']->approvedByUser->memberPersonalProfile->middle_name
                                                                            ? $data['publication']->approvedByUser->memberPersonalProfile->middle_name . ' '
                                                                            : '') .
                                                                        $data['publication']->approvedByUser->memberPersonalProfile->last_name,
                                                                ) }}

                                                                <a href="{{ route('viewMember', ['uuid' => $data['publication']->approvedByUser->uuid]) }}"
                                                                    target="_blank" class="badge bg-secondary text-white"
                                                                    title="View Initiator">
                                                                    <i class="fas fa-link"></i>
                                                                </a>
                                                            @else
                                                                {{ $data['publication']->approved_by }}
                                                            @endif
                                                        @endif -
                                                        @php
                                                            $updatedAt = \Carbon\Carbon::parse(
                                                                $data['publication']->last_approval_update,
                                                            );
                                                        @endphp

                                                        @if ($updatedAt->isToday())
                                                            Today, {{ $updatedAt->format('h:i A') }}
                                                        @elseif ($updatedAt->isYesterday())
                                                            Yesterday, {{ $updatedAt->format('h:i A') }}
                                                        @else
                                                            {{ $updatedAt->format('l, d F, Y h:i A') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        </div>


                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            @if (Auth::user()->hasPermission('media_publication.update') || $data['publication']->author == Auth::user()->username)
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#editMediaPublication">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                            @endif
                                            @if (Auth::user()->hasPermission('media_publication.delete') && $data['publication']->approval_status !== 'approved')
                                                <button class="btn btn-danger m-1 d-flex align-items-center delete-confirm "
                                                    data-toggle="modal"
                                                    data-url="{{ route('deleteMediaPublication', ['uuid' => $data['publication']->uuid]) }}">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Delete</span>
                                                </button>

                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {

                                                        document.querySelectorAll('.delete-confirm').forEach(button => {

                                                            button.addEventListener('click', function() {

                                                                const url = this.dataset.url;

                                                                Swal.fire({
                                                                    title: 'Are you sure?',
                                                                    text: 'This media publication and all comments, replies, and reactions associated with it will be permanently deleted.',
                                                                    icon: 'warning',
                                                                    showCancelButton: true,
                                                                    confirmButtonColor: '#d33',
                                                                    cancelButtonColor: '#6c757d',
                                                                    confirmButtonText: 'Yes, delete!',
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
                                            @endif


                                        </div>
                                        @include('users.admin.media.publications.include.editMediaPublication')

                                        @if (Auth::user()->hasPermission('media_publication.update'))
                                            @php
                                                $status = $data['publication']->approval_status;
                                            @endphp

                                            @include('users.admin.media.publications.include.reviewPublicationModal')

                                            @if ($status == 'pending')
                                                <div
                                                    class="alert alert-warning d-flex justify-content-between align-items-center mb-3 mt-3">
                                                    <div>
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        This media publication is waiting for approval. Click the button
                                                        below to review, approve, or decline it.
                                                    </div>
                                                    <a {{-- href="{{ route('admin.media.approval.review', $data['publication']->id) }}" --}} class="btn btn-sm btn-primary"
                                                        data-toggle="modal" data-target="#reviewPublicationModal">
                                                        Review Publication
                                                    </a>
                                                </div>
                                            @elseif ($status == 'approved')
                                                <!-- Action to unpublish/recall back to pending or draft -->
                                                <div
                                                    class="alert alert-info d-flex justify-content-between align-items-center mb-3 mt-3">
                                                    <div>
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        This media publication is currently approved and published. Click
                                                        the button below to revoke approval and unpublish it.
                                                    </div>
                                                    <a {{-- href="{{ route('admin.media.approval.review', $data['publication']->id) }}" --}} class="btn btn-sm btn-primary"
                                                        data-toggle="modal" data-target="#reviewPublicationModal">
                                                        Review Publication
                                                    </a>
                                                </div>
                                            @elseif ($status == 'declined')
                                                <!-- Action to re-approve -->
                                                <div
                                                    class="alert alert-info d-flex justify-content-between align-items-center mb-3 mt-3">
                                                    <div>
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        This publication is currently declined. Click the button below to
                                                        override this decision and approve it.
                                                    </div>
                                                    <a {{-- href="{{ route('admin.media.approval.review', $data['publication']->id) }}" --}} class="btn btn-sm btn-primary"
                                                        data-toggle="modal" data-target="#reviewPublicationModal">
                                                        Review Publication
                                                    </a>
                                                </div>
                                            @endif
                                        @endif

                                        {{-- ######################################################## --}}
                                        @include('users.admin.media.publications.include.mediaPublicationComments')
                                        {{-- ########################################################## --}}
                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('mediaPublications') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
