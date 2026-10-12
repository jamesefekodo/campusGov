@extends('layouts.userLayout')
@section('title', $data['book']->title)
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
                                        {{ $data['book']->title }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        @php
                                            $coverImage = $data['book']->cover_image ?? null;
                                            $coverPath = $coverImage
                                                ? public_path(
                                                    'assets/tenant_' .
                                                        tenant()->id .
                                                        '/uploads/academicResources/recommendedBooks/' .
                                                        $coverImage,
                                                )
                                                : null;
                                            $hasCoverImage = $coverPath && file_exists($coverPath);
                                        @endphp

                                        <div class="publication-cover-wrapper mb-4">
                                            @if ($hasCoverImage)
                                                <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedBooks/' . $coverImage) }}"
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
                                                    {!! $data['book']->description !!}
                                                </span>
                                            </div>
                                            <div class="col-md-12 p-1">
                                                <hr>
                                                <span class="text-black">
                                                    @if ($data['book']->tags)
                                                        @foreach ($data['book']->tags as $tag)
                                                            <a
                                                                href="{{ route('recommendedBooksWihTag', ['slug' => $tag->slug]) }}">
                                                                <span
                                                                    class="badge rounded-pill bg-secondary text-white px-2 py-1 m-1 shadow-sm">
                                                                    {{ $tag->name }}
                                                                </span>
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </span>
                                            </div>

                                            @include('users.admin.academicResources.recommendedBooks.include.shareRecommendedBook')

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Created by:</span>
                                                <span class="text-black">
                                                    @if ($data['book']->created_by)
                                                        @if ($data['book']->createdBy?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['book']->createdBy->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['book']->createdBy->memberPersonalProfile->middle_name
                                                                        ? $data['book']->createdBy->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['book']->createdBy->memberPersonalProfile->last_name,
                                                            ) }}

                                                            <a href="{{ route('viewMember', ['uuid' => $data['book']->createdBy->uuid]) }}"
                                                                target="_blank" class="badge bg-secondary text-white"
                                                                title="View Initiator">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                        @else
                                                            {{ $data['book']->created_by }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $createdAt = \Carbon\Carbon::parse($data['book']->created_at);
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
                                        </div>


                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('memberRecommendedBooks') }}"> <i
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
