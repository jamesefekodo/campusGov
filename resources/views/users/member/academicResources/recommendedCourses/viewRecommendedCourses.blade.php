@extends('layouts.userLayout')
@section('title', $data['course']->title)
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
                                        {{ $data['course']->title }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        @php
                                            $coverImage = $data['course']->cover_image ?? null;
                                            $coverPath = $coverImage
                                                ? public_path(
                                                    'assets/tenant_' .
                                                        tenant()->id .
                                                        '/uploads/academicResources/recommendedCourses/' .
                                                        $coverImage,
                                                )
                                                : null;
                                            $hasCoverImage = $coverPath && file_exists($coverPath);
                                        @endphp

                                        <div class="publication-cover-wrapper mb-4">
                                            @if ($hasCoverImage)
                                                <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedCourses/' . $coverImage) }}"
                                                    alt="{{ $data['course']->title ?? 'Course cover image' }}"
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
                                                    {!! $data['course']->description !!}
                                                </span>
                                            </div>
                                            <div class="col-md-12 p-1">
                                                <hr>
                                                <span class="text-black">
                                                    @if ($data['course']->tags)
                                                        @foreach ($data['course']->tags as $tag)
                                                            <a
                                                                href="{{ route('memberRecommendedCoursesFilter', ['slug' => $tag->slug]) }}">
                                                                <span
                                                                    class="badge rounded-pill bg-secondary text-white px-2 py-1 m-1 shadow-sm">
                                                                    {{ $tag->name }}
                                                                </span>
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </span>
                                            </div>

                                            @include('users.admin.academicResources.recommendedCourses.include.shareRecommendedCourse')

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Created by:</span>
                                                <span class="text-black">
                                                    @if ($data['course']->created_by)
                                                        @if ($data['course']->createdBy?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['course']->createdBy->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['course']->createdBy->memberPersonalProfile->middle_name
                                                                        ? $data['course']->createdBy->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['course']->createdBy->memberPersonalProfile->last_name,
                                                            ) }}

                                                            <a href="{{ route('viewMember', ['uuid' => $data['course']->createdBy->uuid]) }}"
                                                                target="_blank" class="badge bg-secondary text-white"
                                                                title="View Initiator">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                        @else
                                                            {{ $data['course']->created_by }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $createdAt = \Carbon\Carbon::parse($data['course']->created_at);
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
                                <a class="btn btn-primary" href="{{ route('memberRecommendedCourses') }}"> <i
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
