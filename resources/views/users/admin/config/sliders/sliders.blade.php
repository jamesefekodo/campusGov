@extends('layouts.userLayout')
@section('title', 'Sliders')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.config.basic.include.inner_nav')
            </div>
            @if (Auth::user()->hasPermission('basic_system_config.create'))
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        @if ($data['sliders']->count() >= 10)
                            <button class="btn btn-secondary btn-sm table_btn">
                                <i class="fas fa-plus"></i> New Slider</button>
                        @else
                            <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                data-target="#addNewSliderModal">
                                <i class="fas fa-plus"></i> New Slider</button>
                        @endif
                    </div>
                </div>
                @include('users.admin.config.sliders.include.addNewSliderModal')
            @endif

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                Sliders
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
                                                    <th scope="col" style="width: 10px" class="table_header">Order
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Picture
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Header
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Description
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['sliders']->isEmpty())
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['sliders'] as $slider)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td>

                                                                @if (
                                                                    $slider->background_image &&
                                                                        file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/sliders/' . $slider->background_image)))
                                                                    @php
                                                                        $sliderImage = asset(
                                                                            'assets/tenant_' .
                                                                                tenant()->id .
                                                                                '/uploads/sliders/' .
                                                                                $slider->background_image,
                                                                        );
                                                                    @endphp

                                                                    <img src="{{ $sliderImage }}"
                                                                        alt="{{ $slider->background_image }}" width="150"
                                                                        class="img-thumbnail slider-preview"
                                                                        style="cursor: zoom-in;"
                                                                        data-image="{{ $sliderImage }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#imagePreviewModal">
                                                                @else
                                                                    @php
                                                                        $sliderImage = asset(
                                                                            'system/images/background_image.webp',
                                                                        );
                                                                    @endphp

                                                                    <img src="{{ $sliderImage }}" alt="Default Picture"
                                                                        width="150" class="img-thumbnail slider-preview"
                                                                        style="cursor: zoom-in;"
                                                                        data-image="{{ $sliderImage }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#imagePreviewModal">
                                                                @endif
                                                            </td>
                                                            <td class="">
                                                                {{ $slider->header }}
                                                            </td>
                                                            <td class="">
                                                                {{ $slider->description }}
                                                            </td>
                                                            <td>
                                                                @if (Auth::user()->hasPermission('basic_system_config.update'))
                                                                    <a class="btn btn-warning btn-sm" data-toggle="modal"
                                                                        data-target="#editSliderModal_{{ $slider->id }}">Update</a>
                                                                @else
                                                                    <a class="btn btn-secondary">Update</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>

                                    </div>


                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>

            {{-- ############################ --}}
            @if ($data['sliders']->isNotEmpty())
                @foreach ($data['sliders'] as $slider)
                    @include('users.admin.config.sliders.include.editNewSliderModal')
                @endforeach
            @endif


            {{-- ################################################################ --}}
            <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
                aria-hidden="true">

                <div class="modal-dialog modal-xl modal-dialog-centered">

                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="imagePreviewModalLabel">
                                Image Preview
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-center" style="overflow: hidden;">

                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-primary" id="zoomIn">
                                    <i class="fas fa-search-plus"></i>
                                    Zoom In
                                </button>

                                <button type="button" class="btn btn-sm btn-secondary" id="zoomOut">
                                    <i class="fas fa-search-minus"></i>
                                    Zoom Out
                                </button>

                                <button type="button" class="btn btn-sm btn-dark" id="resetZoom">
                                    <i class="fas fa-sync-alt"></i>
                                    Reset
                                </button>
                            </div>

                            <div id="imageZoomContainer"
                                style="
                        width: 100%;
                        height: 70vh;
                        overflow: auto;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    ">

                                <img id="previewImage" src="" alt="Preview"
                                    style="
                            max-width: none;
                            max-height: none;
                            width: auto;
                            height: auto;
                            transform-origin: center center;
                            transition: transform 0.2s ease;
                            cursor: grab;
                        ">
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const previewImage = document.getElementById('previewImage');
                    const imageContainer = document.getElementById('imageZoomContainer');

                    const zoomInButton = document.getElementById('zoomIn');
                    const zoomOutButton = document.getElementById('zoomOut');
                    const resetZoomButton = document.getElementById('resetZoom');

                    let scale = 1;

                    function applyZoom() {
                        previewImage.style.transform = `scale(${scale})`;
                    }

                    function resetZoom() {
                        scale = 1;
                        applyZoom();
                    }

                    // Open image
                    document.querySelectorAll('.slider-preview').forEach(function(image) {

                        image.addEventListener('click', function() {

                            previewImage.src = this.dataset.image;

                            resetZoom();

                        });

                    });

                    // Zoom In
                    zoomInButton.addEventListener('click', function() {

                        scale += 0.25;

                        if (scale > 5) {
                            scale = 5;
                        }

                        applyZoom();

                    });

                    // Zoom Out
                    zoomOutButton.addEventListener('click', function() {

                        scale -= 0.25;

                        if (scale < 0.5) {
                            scale = 0.5;
                        }

                        applyZoom();

                    });

                    // Reset
                    resetZoomButton.addEventListener('click', function() {

                        resetZoom();

                    });

                    // Mouse wheel zoom
                    imageContainer.addEventListener('wheel', function(event) {

                        event.preventDefault();

                        if (event.deltaY < 0) {
                            scale += 0.1;
                        } else {
                            scale -= 0.1;
                        }

                        if (scale < 0.5) {
                            scale = 0.5;
                        }

                        if (scale > 5) {
                            scale = 5;
                        }

                        applyZoom();

                    }, {
                        passive: false
                    });

                });
            </script>

            {{-- ############################################################# --}}



        </div>
    </div><!--//app-content-->
@endsection
