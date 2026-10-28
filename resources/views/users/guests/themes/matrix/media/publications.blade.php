@extends('users.guests.themes.matrix.layout.matrixLayout')

@section('title', $data['pageTitle'] ?? 'Publications')

@section('content')

    @php
        $publications = $data['publications'];
        $searchKey = $data['searchKey'] ?? '';
        $pageTitle = $data['pageTitle'] ?? 'Media Publications';
        $sortBy = $data['sortBy'] ?? 'published_at';
        $sortOrder = $data['sortOrder'] ?? 'desc';
    @endphp

    <main class="mx-page pt-2 mt-5">
        {{-- Publications --}}
        <section class="mx-section mx-section-white">
            <div class="container">

                {{-- Search and sorting --}}
                <div class="mx-feature-panel mb-5">
                    <div class="row g-3 align-items-end">

                        <div class="col-12 col-lg-7">
                            <form method="GET" action="{{ route('newsAndArticlesSearch') }}">

                                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'date_published') }}">

                                <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

                                <label for="searchKey" class="form-label fw-semibold">
                                    Search Publications
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-magnifying-glass text-muted"></i>
                                    </span>

                                    <input type="search" name="searchKey" id="searchKey"
                                        class="form-control border-start-0 ps-0" value="{{ $searchKey }}"
                                        placeholder="Search by title, content, or keyword...">

                                    <button type="submit" class="btn btn-primary rounded-end">
                                        <i class="fas fa-magnifying-glass me-1"></i>
                                        Search
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="col-12 col-lg-5">
                            <form method="GET" action="{{ route('newsAndArticles') }}" class="row g-2 align-items-end">

                                @if ($data['requestType'] === 'search' && !empty($searchKey))
                                    <input type="hidden" name="searchKey" value="{{ $searchKey }}">
                                @endif

                                <div class="col-7">
                                    <label for="sort_by" class="form-label fw-semibold">
                                        Sort By
                                    </label>

                                    <select name="sort_by" id="sort_by" class="form-select">
                                        <option value="date_published" {{ $sortBy === 'published_at' ? 'selected' : '' }}>
                                            Date Published
                                        </option>

                                        <option value="title" {{ $sortBy === 'title' ? 'selected' : '' }}>
                                            Title
                                        </option>

                                        <option value="last_update" {{ $sortBy === 'last_update' ? 'selected' : '' }}>
                                            Last Updated
                                        </option>

                                        <option value="views" {{ $sortBy === 'views' ? 'selected' : '' }}>
                                            Most Viewed
                                        </option>
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="sort_order" class="form-label fw-semibold">
                                        Order
                                    </label>

                                    <select name="sort_order" id="sort_order" class="form-select">
                                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>
                                            Desc
                                        </option>

                                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>
                                            Asc
                                        </option>
                                    </select>
                                </div>

                                <div class="col-2">
                                    <button type="submit" class="btn btn-primary w-100" title="Apply sorting">
                                        <i class="fas fa-arrow-down-wide-short"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Active filters --}}
                        @if ($data['requestType'] === 'search' || $data['requestType'] === 'byTag')
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">
                                <span class="small text-muted">Active filters:</span>

                                @if ($data['requestType'] === 'search' && !empty($searchKey))
                                    <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-magnifying-glass me-1 text-primary"></i>
                                        Search: {{ $searchKey }}
                                    </span>
                                @endif

                                @if ($data['requestType'] === 'byTag')
                                    <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-tag me-1 text-primary"></i>
                                        Tag: {{ request('tag') }}
                                    </span>
                                @endif

                                <a href="{{ route('newsAndArticles') }}"
                                    class="btn btn-sm btn-outline-danger rounded-2 ms-sm-auto">
                                    <i class="fas fa-xmark me-1"></i>
                                    Clear Filters
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Publication cards --}}
                <div class="row g-4">

                    @forelse($publications as $publication)
                        @php
                            $coverImage = $publication->cover_image ?? null;

                            $coverPath = $coverImage
                                ? public_path(
                                    'assets/tenant_' . tenantId() . '/uploads/media/publications/' . $coverImage,
                                )
                                : null;

                            $hasCoverImage = $coverPath && file_exists($coverPath);

                            $imageToShow = $hasCoverImage
                                ? asset('assets/tenant_' . tenantId() . '/uploads/media/publications/' . $coverImage)
                                : asset('system/images/wide_img_placeholder.jpeg');

                            $authorName = $publication->authorUser->name ?? ($publication->author ?? 'Association');

                            $publicationDate = $publication->published_at ?? $publication->created_at;

                            $shortDescription =
                                $publication->meta_description ?:
                                \Illuminate\Support\Str::limit(strip_tags($publication->content), 150);
                        @endphp

                        <div class="col-12 col-md-6 col-xl-4">
                            <article class="mx-profile-card">

                                <div class="mx-profile-card-head">
                                    <span class="mx-profile-label">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        {{ $publicationDate?->format('d M, Y') ?? 'Date unavailable' }}
                                    </span>

                                    <span class="mx-profile-number">
                                        <i class="fa-solid fa-eye"></i>
                                        {{ number_format($publication->views ?? 0) }}
                                    </span>
                                </div>

                                <div class="rounded-3 overflow-hidden mb-3" style="height: 220px;">
                                    <img src="{{ $imageToShow }}" alt="{{ $publication->title }}" class="w-100 h-100"
                                        style="object-fit: cover;">
                                </div>

                                <span class="mx-profile-role">
                                    Publication
                                </span>

                                <h3>
                                    {{ \Illuminate\Support\Str::limit($publication->title, 75) }}
                                </h3>

                                <p class="mx-profile-description">
                                    {{ $shortDescription }}
                                </p>

                                {{-- Tags --}}
                                <div class="mb-3">
                                    @forelse($publication->tags->take(4) as $tag)
                                        <a
                                        href="{{ route('newsAndArticlesWihTag', ['slug' => $tag->slug]) }}"
                                            class="text-decoration-none">
                                            <span
                                                class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 me-1 mb-1">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ $tag->name }}
                                            </span>
                                        </a>
                                    @empty
                                        <span class="small text-muted">
                                            <i class="fas fa-tags me-1"></i>
                                            No tags
                                        </span>
                                    @endforelse

                                    @if ($publication->tags->count() > 4)
                                        <span class="badge rounded-pill bg-light text-muted border px-2 py-1">
                                            +{{ $publication->tags->count() - 4 }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mx-profile-footer">
                                    <span>
                                        <i class="fa-solid fa-user"></i>
                                        {{ $authorName }}
                                    </span>

                                    <a
                                    href="{{ route('guestViewNewsAndArticle', ['uuid' => $publication->uuid]) }}"
                                        class="mx-button">
                                        Read
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>

                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="mx-feature-panel text-center">
                                <div class="mx-profile-avatar mx-auto mb-3">
                                    <i class="fa-solid fa-newspaper"></i>
                                </div>

                                <h3>No Publications Found</h3>

                                <p class="mx-profile-description">
                                    @if (request()->filled('searchKey') || request()->filled('tag'))
                                        No publications match your current search or filter.
                                    @else
                                        There are no published media publications at the moment.
                                    @endif
                                </p>

                                @if (request()->filled('searchKey') || request()->filled('tag'))
                                    <div class="mx-actions">
                                        <a href="{{ route('newsAndArticles') }}"
                                            class="mx-button mx-button-outline">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            View All Publications
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @php
                    $paginatedArray = $data['publications'];
                @endphp

                @include('layouts.include.pagination')

                {{-- Back action --}}
                @if (in_array($data['requestType'], ['search', 'byTag']))
                    <div class="mx-actions mt-4">
                        <a href="{{ route('newsAndArticles') }}" class="mx-button mx-button-outline">
                            <i class="fa-solid fa-chevron-left"></i>
                            Back to Publications
                        </a>
                    </div>
                @endif

            </div>
        </section>

    </main>

@endsection
