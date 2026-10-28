@extends('users.guests.themes.matrix.layout.matrixLayout')

@section('title', $data['publication']->title)

@section('content')
    {{-- Meta and OG data starts --}}
@section('og_title', $data['publication']->title)
@section('og_description', $data['publication']->meta_description ?? seoSettings()->meta_description)
@section('og_image', $data['publication']->cover_image ? asset('assets/tenant_' . tenantId() .
    '/uploads/media/publications/' . $data['publication']->cover_image) : associationLogo())
    <meta name="description" content="{{ $data['publication']->meta_description ?? seoSettings()->meta_description }}">

    <meta name="keywords" content="{{ $data['publication']->meta_keywords ?? seoSettings()->meta_keywords }}">
    {{-- Meta and OG data ends --}}



    @php
        $publication = $data['publication'];

        $coverImage = $publication->cover_image ?? null;

        $coverPath = $coverImage
            ? public_path('assets/tenant_' . tenantId() . '/uploads/media/publications/' . $coverImage)
            : null;

        $hasCoverImage = $coverPath && file_exists($coverPath);

        $coverUrl = $hasCoverImage
            ? asset('assets/tenant_' . tenantId() . '/uploads/media/publications/' . $coverImage)
            : null;

        $authorName = $publication->authorUser?->memberPersonalProfile
            ? trim(
                $publication->authorUser->memberPersonalProfile->first_name .
                    ' ' .
                    ($publication->authorUser->memberPersonalProfile->middle_name
                        ? $publication->authorUser->memberPersonalProfile->middle_name . ' '
                        : '') .
                    $publication->authorUser->memberPersonalProfile->last_name,
            )
            : $publication->author ?? 'Association';

        $publishedAt = \Carbon\Carbon::parse($publication->published_at ?? $publication->created_at);

        $lastUpdatedAt = $publication->last_update ? \Carbon\Carbon::parse($publication->last_update) : null;
    @endphp

    <main class="mx-page publication-view pt-5 mt-5">

        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">

                    {{-- Publication header --}}
                    <div class="text-center mb-4">
                        <span class="mx-kicker">Publication</span>

                        <h1 class="publication-title mt-2 mb-3">
                            {{ $publication->title }}
                        </h1>

                        <div class="publication-meta">
                            <span>
                                <i class="fa-solid fa-user-pen"></i>
                                {{ $authorName }}
                            </span>

                            <span>
                                <i class="fa-solid fa-calendar-days"></i>
                                {{ $publishedAt->format('l, d F, Y h:i A') }}
                            </span>

                            <span>
                                <i class="fa-solid fa-eye"></i>
                                {{ number_format($publication->views ?? 0) }} views
                            </span>

                            @if ($lastUpdatedAt)
                                <span>
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    Updated {{ $lastUpdatedAt->format('d M, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Cover image --}}
                    <div class="publication-cover-container mb-4 rounded-3 overflow-hidden bg-light text-center">
                        @if ($hasCoverImage)
                            <img src="{{ $coverUrl }}" alt="{{ $publication->header ?? 'Publication cover image' }}"
                                class="img-fluid publication-cover-preview" loading="lazy" data-bs-toggle="modal"
                                data-bs-target="#publicationCoverModal">
                        @else
                            <div
                                class="ratio ratio-16x9 d-flex flex-column align-items-center justify-content-center text-secondary">
                                <i class="fas fa-image fs-2"></i>
                                <span class="small">No cover image available</span>
                            </div>
                        @endif
                    </div>

                    @if ($hasCoverImage)
                        <div class="modal fade" id="publicationCoverModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content bg-transparent border-0">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title text-white">
                                            {{ $publication->title }}
                                        </h5>

                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close">
                                        </button>
                                    </div>

                                    <div class="modal-body p-0 text-center">
                                        <img src="{{ $coverUrl }}"
                                            alt="{{ $publication->header ?? 'Publication cover image' }}"
                                            class="img-fluid rounded shadow">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Publication body --}}
                    <div class="publication-body mx-auto">
                        <div class="publication-content">
                            {!! $publication->content !!}
                        </div>

                        @include('users.admin.media.publications.include.sharePublication')


                        {{-- Tags --}}
                        @if ($publication->tags->isNotEmpty())
                            <hr class="my-4">

                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($publication->tags as $tag)
                                    <a href="{{ route('newsAndArticlesWihTag', ['slug' => $tag->slug]) }}"
                                        class="text-decoration-none">
                                        <span
                                            class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $tag->name }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        {{-- Author and update --}}
                        <hr class="my-4">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mx-contact-card">
                                    <div class="mx-contact-icon">
                                        <i class="fa-solid fa-user-pen"></i>
                                    </div>

                                    <div>
                                        <h3>Author</h3>
                                        <p>{{ $authorName }}</p>
                                        <p>{{ $publishedAt->format('l, d F, Y h:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mx-contact-card">
                                    <div class="mx-contact-icon">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>

                                    <div>
                                        <h3>Last Update</h3>

                                        @if ($lastUpdatedAt)
                                            <p>{{ $lastUpdatedAt->format('l, d F, Y h:i A') }}</p>
                                        @else
                                            <p>No update recorded.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('users.admin.media.publications.include.mediaPublicationComments')

                    </div>


                    {{-- Actions --}}
                    <div class="mx-actions my-5">
                        <a href="{{ route('newsAndArticles') }}" class="mx-button mx-button-outline">
                            <i class="fa-solid fa-chevron-left"></i>
                            Back to Publications
                        </a>

                        <a href="{{ route('homepage') }}" class="mx-button">
                            <i class="fa-solid fa-house"></i>
                            Back to Home
                        </a>
                    </div>

                </div>
            </div>

        </div>

    </main>

@endsection
