@extends('layouts.userLayout')
@section('title', $data['pageTitle'])
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            @php
                $publications = $data['publications'];
                $totalPublications = $data['totalPublications'] ?? 0;
                $searchKey = $data['searchKey'] ?? '';
                $pageTitle = $data['pageTitle'] ?? 'Media Publications';
                $sortBy = $data['sortBy'] ?? 'published_at';
                $sortOrder = $data['sortOrder'] ?? 'desc';

                /*
                 * Toggle the current sorting direction.
                 */
                $nextSortOrder = $sortOrder === 'desc' ? 'asc' : 'desc';
            @endphp

            <div class="mb-4">
                <div class="individual-review-card-container">
                    <div class="col-md-12 individual-review-card">
                        <div class="my_form_container row col-md-12 pb-5">
                            <h4 class="block text-center py-2 pt-3 text-uppercase type-b-page-header">
                                Media Publications
                            </h4>
                            @php
                                /*
                                 * When currently viewing search results, sorting should remain
                                 * on the search route and preserve searchKey.
                                 *
                                 * When viewing all publications, sorting should stay on the
                                 * normal publications route.
                                 */
                                $sortingRoute =
                                    $data['requestType'] === 'search'
                                        ? route('memberMediaPublicationsSearch')
                                        : route('memberMediaPublications');
                            @endphp

                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-3 p-md-4">

                                    <div class="row g-3 align-items-end">

                                        {{-- Search form --}}
                                        <div class="col-12 col-lg-7">
                                            <form method="GET" action="{{ route('memberMediaPublicationsSearch') }}">

                                                {{-- Preserve current sort selection when searching --}}
                                                <input type="hidden" name="sort_by"
                                                    value="{{ request('sort_by', 'date_published') }}">

                                                <input type="hidden" name="sort_order"
                                                    value="{{ request('sort_order', 'desc') }}">

                                                {{-- Preserve selected tag, if you later add tag filtering --}}
                                                @if (request()->filled('tag'))
                                                    <input type="hidden" name="tag" value="{{ request('tag') }}">
                                                @endif

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

                                        {{-- Sorting form --}}
                                        <div class="col-12 col-lg-5">
                                            <form method="GET" action="{{ $sortingRoute }}"
                                                class="row g-2 align-items-end">

                                                {{-- Keep active search results when sorting --}}
                                                @if ($data['requestType'] === 'search' && !empty($searchKey))
                                                    <input type="hidden" name="searchKey" value="{{ $searchKey }}">
                                                @endif

                                                {{-- Preserve tag if applicable --}}
                                                @if (request()->filled('tag'))
                                                    <input type="hidden" name="tag" value="{{ request('tag') }}">
                                                @endif

                                                {{-- Sort field --}}
                                                <div class="col-7">
                                                    <label for="sort_by" class="form-label fw-semibold">
                                                        Sort By
                                                    </label>

                                                    <select name="sort_by" id="sort_by" class="form-select">

                                                        <option value="date_published"
                                                            {{ $sortBy === 'published_at' ? 'selected' : '' }}>
                                                            Date Published
                                                        </option>

                                                        {{-- Must be title, because your controller checks title --}}
                                                        <option value="title" {{ $sortBy === 'title' ? 'selected' : '' }}>
                                                            Title
                                                        </option>

                                                        <option value="last_update"
                                                            {{ $sortBy === 'last_update' ? 'selected' : '' }}>
                                                            Last Updated
                                                        </option>

                                                        <option value="views" {{ $sortBy === 'views' ? 'selected' : '' }}>
                                                            Most Viewed
                                                        </option>
                                                    </select>
                                                </div>

                                                {{-- Sort direction --}}
                                                <div class="col-3">
                                                    <label for="sort_order" class="form-label fw-semibold">
                                                        Order
                                                    </label>

                                                    <select name="sort_order" id="sort_order" class="form-select">

                                                        <option value="desc"
                                                            {{ $sortOrder === 'desc' ? 'selected' : '' }}>
                                                            Desc
                                                        </option>

                                                        <option value="asc"
                                                            {{ $sortOrder === 'asc' ? 'selected' : '' }}>
                                                            Asc
                                                        </option>
                                                    </select>
                                                </div>

                                                {{-- Apply sorting --}}
                                                <div class="col-2">
                                                    <button type="submit" class="btn btn-primary w-100"
                                                        title="Apply sorting">

                                                        <i class="fas fa-arrow-down-wide-short"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Active filters --}}
                                    @if ($data['requestType'] === 'search' ||
                                    // request()->filled('tag')
                                    $data['requestType'] === 'byTag'
                                    )
                                        <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">

                                            <span class="small text-muted">
                                                Active filters:
                                            </span>

                                            @if ($data['requestType'] === 'search' && !empty($data['searchKey']))
                                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                                    <i class="fas fa-magnifying-glass me-1 text-primary"></i>
                                                    Search: {{ $data['searchKey'] }}
                                                </span>
                                            @endif

                                            @if (
                                            // request()->filled('tag')
                                            $data['requestType'] === 'byTag'
                                            )
                                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                                    <i class="fas fa-tag me-1 text-primary"></i>
                                                    Tag: {{ request('tag') }}
                                                </span>
                                            @endif

                                            {{-- Clear everything and return to normal publication listing --}}
                                            <a href="{{ route('memberMediaPublications') }}"
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

                                @forelse ($publications as $publication)
                                    @php
                                        /*
                                         * If cover_image is a full external URL, use it directly.
                                         * Otherwise, treat it as a Laravel storage file path.
                                         *
                                         * Example stored value:
                                         * media-publications/cover-images/example.jpg
                                         */
                                        $coverImage = $publication->cover_image ?? null;
                                        $coverPath = $coverImage
                                            ? public_path(
                                                'assets/tenant_' .
                                                    tenantId() .
                                                    '/uploads/media/publications/' .
                                                    $coverImage,
                                            )
                                            : null;
                                        $hasCoverImage = $coverPath && file_exists($coverPath);
                                        $imageToShow = asset('system/images/wide_img_placeholder.jpeg');

                                        if ($hasCoverImage) {
                                            $imageToShow = asset(
                                                'assets/tenant_' .
                                                    tenantId() .
                                                    '/uploads/media/publications/' .
                                                    $coverImage,
                                            );
                                        }

                                        $authorName =
                                            $publication->authorUser->name ?? ($publication->author ?? 'CampusGov');

                                        $publicationDate = $publication->published_at ?? $publication->created_at;

                                        $shortDescription =
                                            $publication->meta_description ?:
                                            \Illuminate\Support\Str::limit(strip_tags($publication->content), 150);
                                    @endphp


                                    <div class="col-12 col-md-6 col-xl-4">
                                        <article
                                            class="card publication-card border-0 shadow-sm h-100 rounded-3 overflow-hidden">

                                            {{-- Cover image or placeholder --}}
                                            <div class="publication-cover position-relative">
                                                <div class="w-100 overflow-hidden bg-light" style="height: 280px;">
                                                    <img src="{{ $imageToShow }}" alt="{{ $publication->title }}"
                                                        class="w-100 h-100 img-fluid">
                                                </div>

                                                {{-- Publication status --}}
                                                @if ($publication->published_at)
                                                    <span
                                                        class="badge bg-success position-absolute top-0 end-0 m-3 shadow-sm">
                                                        <i class="fas fa-circle-check me-1"></i>
                                                        Published
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="card-body d-flex flex-column p-4">

                                                {{-- Date and views --}}
                                                <div class="d-flex align-items-center mb-3">

                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-alt me-1 text-primary"></i>
                                                        {{ $publicationDate ? $publicationDate->format('d M, Y') : 'Date unavailable' }}
                                                    </small>

                                                    <div class="d-flex align-items-center gap-3 ms-auto">

                                                        <small class="text-muted">
                                                            <i class="fas fa-eye me-1 text-primary"></i>
                                                            {{ number_format($publication->views ?? 0) }}
                                                        </small>

                                                        <small class="text-muted">
                                                            <i class="fas fa-share-nodes me-1 text-primary"></i>
                                                            {{ number_format($publication->views ?? 0) }}
                                                        </small>

                                                    </div>

                                                </div>

                                                {{-- Title --}}
                                                <h5 class="fw-bold mb-2 publication-title">
                                                    {{ \Illuminate\Support\Str::limit($publication->title, 75) }}
                                                </h5>

                                                {{-- Short description --}}
                                                <p class="text-muted small publication-description mb-3">
                                                    {{ $shortDescription }}
                                                </p>

                                                {{-- Tags --}}
                                                <div class="publication-tags mb-3">
                                                    @forelse ($publication->tags->take(4) as $tag)
                                                        <a href="{{ route('memberMediaPublicationWihTag', ['slug' => $tag->slug]) }}"
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
                                                        <span
                                                            class="badge rounded-pill bg-light text-muted border px-2 py-1">
                                                            +{{ $publication->tags->count() - 4 }}
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Push lower content to the bottom --}}
                                                <div class="mt-auto">

                                                    <hr class="my-3">

                                                    {{-- Author and interaction information --}}
                                                    <div
                                                        class="d-flex justify-content-between align-items-center gap-2 mb-3">

                                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                                                style="width: 34px; height: 34px; font-size: 13px; font-weight: 700;">

                                                                {{ strtoupper(substr($authorName, 0, 1)) }}
                                                            </div>

                                                            <div class="text-truncate">
                                                                <small class="d-block text-muted">
                                                                    Published by
                                                                </small>

                                                                <small class="fw-semibold text-dark text-truncate d-block">
                                                                    {{ $authorName }}
                                                                </small>
                                                            </div>
                                                        </div>

                                                        <div class="text-end text-nowrap">
                                                            <small class="text-muted d-block">
                                                                <i class="far fa-heart me-1"></i>
                                                                {{ $publication->likes_count ?? $publication->likes->count() }}
                                                            </small>

                                                            <small class="text-muted d-block">
                                                                <i class="far fa-comments me-1"></i>
                                                                {{ $publication->comments_count ?? $publication->comments->count() }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    {{-- View button --}}
                                                    <a href="{{ route('memberViewMediaPublication', ['uuid' => $publication->uuid]) }}"
                                                        class="btn btn-primary w-100 rounded-2">

                                                        Read Publication
                                                        <i class="fas fa-arrow-right ms-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    </div>

                                @empty
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm rounded-3">
                                            <div class="card-body text-center py-5">

                                                <div class="rounded-circle bg-light text-secondary d-inline-flex align-items-center justify-content-center mb-3"
                                                    style="width: 75px; height: 75px;">

                                                    <i class="fas fa-newspaper fa-2x"></i>
                                                </div>

                                                <h5 class="fw-bold">
                                                    No Publications Found
                                                </h5>

                                                <p class="text-muted mb-3">
                                                    @if (request()->filled('search') || request()->filled('tag'))
                                                        No publications match your current search or filter.
                                                    @else
                                                        There are no published media publications at the moment.
                                                    @endif
                                                </p>

                                                @if (request()->filled('search') || request()->filled('tag'))
                                                    <a href="{{ route('mediaPublicationWihTag') }}"
                                                        class="btn btn-outline-primary rounded-2">

                                                        <i class="fas fa-rotate-left me-1"></i>
                                                        View All Publications
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        {{-- Pagination --}}
                        @php
                            $paginatedArray = $data['publications'];
                        @endphp

                        @include('layouts.include.pagination')
                        {{-- Back Home --}}
                        <div class="w-100 justify-content-center text-center mb-4">
                            @switch($data['requestType'])
                                @case('all')
                                    {{ '' }}
                                @break

                                @case('search')
                                @case('byTag')
                                    <a class="btn btn-success" href="{{ route('memberMediaPublications') }}"> <i
                                            class="fas fa-chevron-left"></i>
                                        Back
                                    </a>
                                @break

                                @default
                            @endswitch

                        </div>

                    </div>
                </div>


            </div>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
