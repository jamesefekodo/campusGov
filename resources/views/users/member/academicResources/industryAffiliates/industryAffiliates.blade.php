@extends('layouts.userLayout')

@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- Navigation --}}
            </div>

            <div class="individual-review-card-container mt-4">
                <div class="individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-8">
                            <div class="table_title">
                                {{ $data['pageTitle'] }}
                            </div>
                        </div>

                        <div class="table_head_item col-md-4">
                            <div class="custom_search">
                                <form method="GET" action="{{ route('memberIndustryAffiliatesSearch') }}">
                                    <div class="input-group-append">
                                        <input type="search" class="table_search my-2 my-sm-0" name="searchKey"
                                            value="{{ $data['searchKey'] }}">

                                        <button type="submit" class="table_search_button btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="my_form_container p-3">
                        <div class="card-body">
                            @if ($data['industryAffiliates']->isEmpty())
                                <div class="text-center py-5">
                                    <h3 class="text-red m-0">
                                        No Records Found!
                                    </h3>
                                </div>
                            @else
                                <div class="row g-4">
                                    @foreach ($data['industryAffiliates'] as $affiliate)
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                            <div class="recommended-book-card h-100">
                                                <a href="{{ route('memberViewRecommendedBook', ['slug' => $affiliate->slug]) }}"
                                                    class="recommended-book-card-image-link">
                                                    @php
                                                        $coverImageDirectory =
                                                            'assets/tenant_' .
                                                            tenant()->id .
                                                            '/uploads/academicResources/industryAffilate';

                                                        $coverImageRelativePath = $affiliate->cover_image
                                                            ? $coverImageDirectory . '/' . $affiliate->cover_image
                                                            : null;

                                                        $coverImageUrl = $coverImageRelativePath
                                                            ? asset($coverImageRelativePath)
                                                            : null;

                                                        $coverImageExists =
                                                            $coverImageRelativePath &&
                                                            file_exists(public_path($coverImageRelativePath));
                                                    @endphp

                                                    @if ($coverImageExists)
                                                        <img src="{{ $coverImageUrl }}" alt="{{ $affiliate->title }} cover"
                                                            class="recommended-book-card-image">
                                                    @else
                                                        <div class="recommended-book-card-image-placeholder">
                                                            <i class="fas fa-book"></i>
                                                            <span>No Cover</span>
                                                        </div>
                                                    @endif
                                                </a>

                                                <div class="recommended-book-card-body">
                                                    <h5 class="recommended-book-card-title">
                                                        {{ $affiliate->title }}
                                                    </h5>

                                                    <div class="recommended-book-card-tags">
                                                        @if ($affiliate->tags && count($affiliate->tags))
                                                            @foreach ($affiliate->tags as $tag)
                                                                <a
                                                                    href="{{ route('memberIndustryAffiliatesFilter', ['slug' => $tag->slug]) }}">
                                                                    <span
                                                                        class="badge rounded-pill bg-secondary text-white">
                                                                        {{ $tag->name }}
                                                                    </span>
                                                                </a>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted small">
                                                                No tags
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="recommended-book-card-footer">
                                                        <span class="badge bg-success-subtle text-success">
                                                            Published
                                                        </span>

                                                        <a class="btn btn-primary btn-sm"
                                                            href="{{ route('memberViewIndustryAffiliate', ['slug' => $affiliate->slug]) }}">
                                                            View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @php
                                    $paginatedArray = $data['industryAffiliates'];
                                @endphp

                                @include('layouts.include.pagination')
                            @endif

                            <div class="w-100 justify-content-center text-center mt-4 mb-4">
                                @if (in_array($data['requestType'], ['search', 'with_tag']))
                                    <a class="btn btn-success" href="{{ route('memberIndustryAffiliates') }}">
                                        <i class="fas fa-chevron-left"></i>
                                        Back
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .recommended-book-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .recommended-book-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .recommended-book-card-image-link {
            display: block;
            text-decoration: none;
        }

        .recommended-book-card-image {
            width: 100%;
            height: 260px;
            object-fit: cover;
            display: block;
        }

        .recommended-book-card-image-placeholder {
            width: 100%;
            height: 260px;
            background: #f4f4f4;
            color: #999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
        }

        .recommended-book-card-image-placeholder i {
            font-size: 32px;
        }

        .recommended-book-card-body {
            padding: 14px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .recommended-book-card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .recommended-book-card-tags {
            margin-bottom: 14px;
        }

        .recommended-book-card-tags .badge {
            font-size: 11px;
            margin: 0 4px 4px 0;
        }

        .recommended-book-card-footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
    </style>
@endsection
