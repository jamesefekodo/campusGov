@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- ### --}}
            </div>
            @if (Auth::user()->hasPermission('recommended_book.create'))
                <div class="d-flex justify-content-end me-3">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                            data-target="#addNewRecommendedCourseModal">
                            <i class="fas fa-plus"></i> Create New</button>
                    </div>
                </div>
                @include('users.admin.academicResources.recommendedCourses.include.addNewRecommendedCourseModal')
            @endif

            <div class="individual-review-card-container row">
                <div class="col-md-1 individual-review-card text-black">
                    {{-- sidde menu --}}
                    @include('users.admin.academicResources.recommendedCourses.include.recommendedCoursesSideMenu')
                </div>
                <div class="col-md-11 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-8">
                            <div class="table_title">
                                {{ $data['pageTitle'] }}
                            </div>
                        </div>

                        <div class="table_head_item col-md-4">
                            <div class="custom_search">
                                <form type="GET" action="{{ route('recommendedCoursesSearch') }}">
                                    @csrf
                                    <div class="input-group-append">
                                        <input type="search" class="table_search my-2 my-sm-0" name="searchKey"
                                            value="{{ $data['searchKey'] }}"><button type="submit"
                                            class="table_search_button btn btn-primary
                                                "><i
                                                class="fas fa-search"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
                                                        Title
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Cover Image
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Tags
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['recommendedCourses']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['recommendedCourses'] as $course)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start">
                                                                {{ $course->title }}
                                                            </td>
                                                            <td class="">
                                                                @php
                                                                    $coverImageDirectory =
                                                                        'assets/tenant_' .
                                                                        tenant()->id .
                                                                        '/uploads/academicResources/recommendedCourses';

                                                                    $coverImageRelativePath = $course->cover_image
                                                                        ? $coverImageDirectory .
                                                                            '/' .
                                                                            $course->cover_image
                                                                        : null;

                                                                    $coverImageUrl = $coverImageRelativePath
                                                                        ? asset($coverImageRelativePath)
                                                                        : null;

                                                                    $coverImageExists =
                                                                        $coverImageRelativePath &&
                                                                        file_exists(
                                                                            public_path($coverImageRelativePath),
                                                                        );
                                                                @endphp

                                                                @if ($coverImageExists)
                                                                    <img src="{{ $coverImageUrl }}"
                                                                        alt="{{ $course->title }} cover"
                                                                        class="recommended-book-thumbnail"
                                                                        data-image-url="{{ $coverImageUrl }}"
                                                                        data-image-title="{{ $course->title }}"
                                                                        onclick="openImageModal(this)">
                                                                @else
                                                                    <span class="text-muted">No cover image</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($course->tags)
                                                                    @foreach ($course->tags as $tag)
                                                                        <a
                                                                            href="{{ route('recommendedCoursesWihTag', ['slug' => $tag->slug]) }}"
                                                                            >
                                                                            <span
                                                                                class="badge rounded-pill bg-secondary text-white px-2 py-1 m-1 shadow-sm">
                                                                                {{ $tag->name }}
                                                                            </span>
                                                                        </a>
                                                                    @endforeach
                                                                @endif
                                                            </td>

                                                            <td>
                                                                @if($course->is_published == true)
                                                                <span class="badge bg-success-subtle text-success">Published</span>
                                                                @else
                                                                <span class="badge bg-danger-subtle text-danger">Not-Published</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewRecommendedCourse', ['slug' => $course->slug]) }}"
                                                                    >View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['recommendedCourses'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            @switch($data['requestType'])
                                                @case('all')
                                                    {{ '' }}
                                                @break

                                                @case('search')
                                                @case('with_tag')
                                                    <a class="btn btn-success" href="{{ route('recommendedCourses') }}"> <i
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
                        </div>



                    </div>
                </div>
            </div>




            <div id="imageModal" class="image-modal" onclick="closeImageModal(event)">
                <div class="image-modal-content" onclick="event.stopPropagation()">
                    <div class="image-modal-header">
                        <span id="imageModalTitle"></span>

                        <div class="image-modal-actions">
                            <button type="button" onclick="zoomIn()">Zoom In</button>
                            <button type="button" onclick="zoomOut()">Zoom Out</button>
                            <button type="button" onclick="resetZoom()">Reset</button>

                            <a id="imageDownloadLink" href="#" download>
                                Download
                            </a>

                            <button type="button" onclick="closeImageModal(event)">
                                Close
                            </button>
                        </div>
                    </div>

                    <div class="image-modal-body">
                        <img id="imageModalImage" src="" alt="Selected image">
                    </div>
                </div>
            </div>
            <style>
                .recommended-book-thumbnail {
                    width: 45px;
                    height: 60px;
                    object-fit: cover;
                    border-radius: 4px;
                    cursor: zoom-in;
                    border: 1px solid #ddd;
                    transition: transform 0.2s ease;
                }

                .recommended-book-thumbnail:hover {
                    transform: scale(1.05);
                }

                .image-modal {
                    display: none;
                    position: fixed;
                    inset: 0;
                    z-index: 9999;
                    background: rgba(0, 0, 0, 0.85);
                    padding: 20px;
                }

                .image-modal.open {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .image-modal-content {
                    background: #fff;
                    border-radius: 8px;
                    width: min(1000px, 95vw);
                    max-height: 95vh;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }

                .image-modal-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    padding: 10px 14px;
                    border-bottom: 1px solid #e5e5e5;
                }

                .image-modal-actions {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .image-modal-actions button,
                .image-modal-actions a {
                    padding: 6px 10px;
                    font-size: 13px;
                    border: 1px solid #ccc;
                    background: #f8f8f8;
                    border-radius: 4px;
                    cursor: pointer;
                    text-decoration: none;
                    color: #333;
                }

                .image-modal-body {
                    overflow: auto;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #111;
                    min-height: 300px;
                }

                #imageModalImage {
                    max-width: 100%;
                    max-height: 80vh;
                    transform-origin: center center;
                    transition: transform 0.15s ease;
                    cursor: grab;
                }
            </style>
            <script>
                let currentZoom = 1;

                function openImageModal(imageElement) {
                    const imageUrl = imageElement.dataset.imageUrl;
                    const imageTitle = imageElement.dataset.imageTitle || 'Image';

                    const modal = document.getElementById('imageModal');
                    const modalImage = document.getElementById('imageModalImage');
                    const modalTitle = document.getElementById('imageModalTitle');
                    const downloadLink = document.getElementById('imageDownloadLink');

                    if (!modal || !modalImage) {
                        console.error('Image modal HTML is missing.');
                        return;
                    }

                    modalImage.src = imageUrl;
                    modalImage.alt = imageTitle;
                    modalTitle.textContent = imageTitle;

                    downloadLink.href = imageUrl;
                    downloadLink.setAttribute(
                        'download',
                        imageUrl.split('/').pop()
                    );

                    resetZoom();
                    modal.classList.add('open');
                }

                function closeImageModal(event) {
                    const modal = document.getElementById('imageModal');

                    if (
                        event.target.id === 'imageModal' ||
                        event.target.textContent.trim() === 'Close'
                    ) {
                        modal.classList.remove('open');

                        document.getElementById('imageModalImage').src = '';
                    }
                }

                function zoomIn() {
                    currentZoom = Math.min(currentZoom + 0.25, 5);
                    applyZoom();
                }

                function zoomOut() {
                    currentZoom = Math.max(currentZoom - 0.25, 0.25);
                    applyZoom();
                }

                function resetZoom() {
                    currentZoom = 1;
                    applyZoom();
                }

                function applyZoom() {
                    document
                        .getElementById('imageModalImage')
                        .style.transform = `scale(${currentZoom})`;
                }

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        document
                            .getElementById('imageModal')
                            .classList.remove('open');
                    }
                });
            </script>


        </div>
    </div><!--//app-content-->
@endsection
