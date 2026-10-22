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

                                            $coverUrl = $coverImage
                                                ? asset(
                                                    'assets/tenant_' .
                                                        tenantId() .
                                                        '/uploads/media/publications/' .
                                                        $coverImage,
                                                )
                                                : null;
                                        @endphp

                                        <div class="mb-4 rounded overflow-hidden bg-light text-center">
                                            @if ($hasCoverImage)
                                                <img src="{{ $coverUrl }}"
                                                    alt="{{ $data['publication']->header ?? 'Publication cover image' }}"
                                                    class="img-fluid publication-cover-preview" loading="lazy"
                                                    data-bs-toggle="modal" data-bs-target="#publicationCoverModal">
                                            @else
                                                <div
                                                    class="ratio ratio-16x9 d-flex flex-column align-items-center justify-content-center text-secondary">
                                                    <i class="fas fa-image fs-2"></i>
                                                    <span class="small">No cover image available</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if ($hasCoverImage)
                                            <div class="modal fade" id="publicationCoverModal" tabindex="-1"
                                                aria-labelledby="publicationCoverModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                                    <div class="modal-content bg-transparent border-0">
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title text-white"
                                                                id="publicationCoverModalLabel">
                                                                {{ $data['publication']->title }}
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body p-0 text-center">
                                                            <img src="{{ $coverUrl }}"
                                                                alt="{{ $data['publication']->header ?? 'Publication cover image' }}"
                                                                class="img-fluid rounded shadow">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <style>
                                            .publication-cover-preview {
                                                cursor: zoom-in;
                                                max-height: 420px;
                                                object-fit: contain;
                                            }

                                            @media (max-width: 576px) {
                                                .publication-cover-preview {
                                                    max-height: 260px;
                                                }
                                            }
                                        </style>

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
                                                                href="{{ route('memberMediaPublicationWihTag', ['slug' => $tag->slug]) }}">
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
                                        </div>

                                        {{-- ######################################################## --}}
                                        @include('users.admin.media.publications.include.mediaPublicationComments')
                                        {{-- ########################################################## --}}
                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('memberMediaPublications') }}"> <i
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
