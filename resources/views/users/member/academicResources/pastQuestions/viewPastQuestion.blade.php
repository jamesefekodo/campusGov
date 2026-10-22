@extends('layouts.userLayout')
@section('title', $data['pastQuestion']->course_code . ' :: ' . $data['pastQuestion']->academic_year . '/' .
    $data['pastQuestion']->academic_year + 1)
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
                                        {{ $data['pastQuestion']->course_code . ' :: ' . $data['pastQuestion']->academic_year . '/' . $data['pastQuestion']->academic_year + 1 }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">

                                        <div class="col-md-12 row px-3">
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Course:</span>
                                                <span class="text-black">
                                                    {{ $data['pastQuestion']->course_title }}
                                                    ({{ strtoupper($data['pastQuestion']->course_code) }})
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Semester:</span>
                                                <span class="text-black">
                                                    {{ $data['pastQuestion']->semester }} semester

                                                    @if ($data['pastQuestion']->level)
                                                        - Year {{ $data['pastQuestion']->level }}
                                                    @endif
-
                                                    {{ $data['pastQuestion']->academic_year . '/' . $data['pastQuestion']->academic_year + 1 }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Type:</span>
                                                <span class="text-black">
                                                    @if ($data['pastQuestion']->is_exam == true)
                                                        Exam
                                                    @else
                                                        others
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Program:</span>
                                                <span class="text-black">
                                                    {{ $data['pastQuestion']->programRelation->name }}
                                                    ({{ $data['pastQuestion']->programTypeRelation->type_name }})
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Falulty:</span>
                                                <span class="text-black">
                                                    {{ $data['pastQuestion']->facultyRelation->name }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Department:</span>
                                                <span class="text-black">
                                                    {{ $data['pastQuestion']->departmentRelation->name }}
                                                </span>
                                            </div>

                                            @if (!empty($data['pastQuestion']->content))
                                                <div class="col-md-12 p-1">
                                                    {{-- <span style="font-weight: bold">Department:</span> --}}
                                                    <span class="text-black">
                                                        {!! $data['pastQuestion']->content !!}
                                                    </span>
                                                </div>
                                            @endif

                                            @php
                                                $question = $data['pastQuestion'];

                                                $imageBasePath =
                                                    'assets/tenant_' .
                                                    tenant()->id .
                                                    '/uploads/academicResources/pastQuestions';

                                                $questionImages = [];

                                                for ($index = 1; $index <= 6; $index++) {
                                                    $imageColumn = 'image_' . $index;
                                                    $imageFile = $question->{$imageColumn};

                                                    if (!empty($imageFile)) {
                                                        $imagePath = ltrim($imageFile, '/');

                                                        if (!str_starts_with($imagePath, 'assets/')) {
                                                            $imagePath = $imageBasePath . '/' . $imagePath;
                                                        }

                                                        $questionImages[] = [
                                                            'page' => $index,
                                                            'url' => asset($imagePath),
                                                        ];
                                                    }
                                                }
                                            @endphp
                                            <div class="col-md-12 p-1">
                                                @if (count($questionImages))
                                                    <div class="past-question-thumbnail-grid">
                                                        @foreach ($questionImages as $image)
                                                            <button type="button" class="past-question-thumbnail"
                                                                data-toggle="modal" data-target="#pastQuestionImageModal"
                                                                data-image-url="{{ $image['url'] }}"
                                                                data-image-title="Past Question Page {{ $image['page'] }}">

                                                                <img src="{{ $image['url'] }}"
                                                                    alt="Past Question Page {{ $image['page'] }}"
                                                                    loading="lazy">

                                                                <span>
                                                                    Page {{ $image['page'] }}
                                                                    <i class="fas fa-search-plus"></i>
                                                                </span>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="alert alert-light border text-muted">
                                                        No question images available.
                                                    </div>
                                                @endif
                                            </div>

                                            @include('users.admin.academicResources.pastQuestions.include.sharePastQuestion')

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Created:</span>
                                                <span class="text-black">
                                                    @if ($data['pastQuestion']->created_by)
                                                        @if ($data['pastQuestion']->creator?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['pastQuestion']->creator->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['pastQuestion']->creator->memberPersonalProfile->middle_name
                                                                        ? $data['pastQuestion']->creator->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['pastQuestion']->creator->memberPersonalProfile->last_name,
                                                            ) }}

                                                            <a href="{{ route('viewMember', ['uuid' => $data['pastQuestion']->creator->uuid]) }}"
                                                                target="_blank" class="badge bg-secondary text-white"
                                                                title="View Initiator">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                        @else
                                                            {{ $data['pastQuestion']->created_by }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $createdAt = \Carbon\Carbon::parse(
                                                            $data['pastQuestion']->created_at,
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
                                                    @if ($data['pastQuestion']->last_update_by)
                                                        @if ($data['pastQuestion']->lastUpdateBy?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['pastQuestion']->lastUpdateBy->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['pastQuestion']->lastUpdateBy->memberPersonalProfile->middle_name
                                                                        ? $data['pastQuestion']->lastUpdateBy->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['pastQuestion']->lastUpdateBy->memberPersonalProfile->last_name,
                                                            ) }}

                                                            <a href="{{ route('viewMember', ['uuid' => $data['pastQuestion']->lastUpdateBy->uuid]) }}"
                                                                target="_blank" class="badge bg-secondary text-white"
                                                                title="View Initiator">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                        @else
                                                            {{ $data['pastQuestion']->last_update_by }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $updatedAt = \Carbon\Carbon::parse(
                                                            $data['pastQuestion']->last_update,
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


                                        {{--

                                        {{-- ######################################################## --}}
                                        @include('users.admin.academicResources.pastQuestions.include.pastQuestionContributions')
                                        {{-- ########################################################## --}}
                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('memberPastQuestions') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ############################################################ --}}
            <div class="modal fade" id="pastQuestionImageModal" tabindex="-1" role="dialog"
                aria-labelledby="pastQuestionImageModalLabel" aria-hidden="true">

                <div class="modal-dialog modal-xl modal-dialog-centered past-question-modal-dialog" role="document">

                    <div class="modal-content past-question-modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title text-light" id="pastQuestionImageModalLabel">

                                Past Question Image
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body past-question-modal-body">

                            <div class="past-question-controls">
                                <button type="button" class="btn btn-light btn-sm" id="pastQuestionZoomOut"
                                    title="Zoom out">

                                    <i class="fas fa-search-minus"></i>
                                </button>

                                <button type="button" class="btn btn-light btn-sm" id="pastQuestionZoomReset">

                                    <i class="fas fa-sync-alt"></i>
                                    Reset
                                </button>

                                <button type="button" class="btn btn-light btn-sm" id="pastQuestionZoomIn" title="Zoom in">

                                    <i class="fas fa-search-plus"></i>
                                </button>

                                <a href="#" id="pastQuestionDownload" class="btn btn-success btn-sm" download>

                                    <i class="fas fa-download"></i>
                                    Download
                                </a>
                            </div>

                            <div class="past-question-image-stage" id="pastQuestionImageStage">

                                <img src="" id="pastQuestionFullImage" class="past-question-full-image"
                                    alt="Full past question image">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <style>
                .past-question-thumbnail-grid {
                    display: flex;
                    flex-wrap: wrap;
                    align-items: flex-start;
                    gap: 12px;
                }

                .past-question-thumbnail {
                    position: relative;
                    width: 145px;
                    height: 185px;
                    overflow: hidden;
                    padding: 0;
                    border: 1px solid #d8dee2;
                    border-radius: 8px;
                    background: #f4f6f7;
                    cursor: pointer;
                    transition: transform .2s ease, box-shadow .2s ease;
                }

                .past-question-thumbnail:hover {
                    box-shadow: 0 8px 20px rgba(0, 0, 0, .16);
                    transform: translateY(-3px);
                }

                .past-question-thumbnail img {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    background: #f5f6f7;
                }

                .past-question-thumbnail span {
                    position: absolute;
                    right: 6px;
                    bottom: 6px;
                    left: 6px;
                    padding: 5px 7px;
                    border-radius: 5px;
                    background: rgba(0, 0, 0, .70);
                    color: #fff;
                    font-size: 11px;
                }

                /*
                                                                                 * Make the Bootstrap modal almost full screen.
                                                                                 */
                .past-question-modal-dialog {
                    width: calc(100% - 30px);
                    max-width: 1500px;
                    min-height: calc(100vh - 30px);
                    margin: 15px auto;
                }

                .past-question-modal-content {
                    min-height: calc(100vh - 30px);
                    background: #111820;
                    color: #fff;
                }

                .past-question-modal-content .modal-header {
                    border-bottom: 1px solid rgba(255, 255, 255, .15);
                }

                .past-question-modal-content .close {
                    color: #fff;
                    opacity: 1;
                }

                .past-question-modal-body {
                    display: flex;
                    flex-direction: column;
                    min-height: 0;
                    padding: 10px;
                }

                .past-question-controls {
                    position: relative;
                    z-index: 20;
                    display: flex;
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 8px;
                    padding: 5px 0 12px;
                }

                .past-question-image-stage {
                    position: relative;
                    display: flex;
                    flex: 1;
                    align-items: center;
                    justify-content: center;
                    min-height: 70vh;
                    overflow: auto;
                    padding: 20px;
                    border-radius: 6px;
                    background: #05090c;
                    cursor: grab;
                }

                .past-question-image-stage:active {
                    cursor: grabbing;
                }

                .past-question-full-image {
                    display: block;
                    max-width: none;
                    width: auto;
                    height: auto;
                    max-height: 72vh;
                    object-fit: contain;
                    transform: scale(1);
                    transform-origin: center center;
                    user-select: none;
                    transition: transform .18s ease;
                }

                @media (max-width: 767px) {
                    .past-question-modal-dialog {
                        width: 100%;
                        min-height: 100vh;
                        margin: 0;
                    }

                    .past-question-modal-content {
                        min-height: 100vh;
                        border-radius: 0;
                    }

                    .past-question-image-stage {
                        min-height: 72vh;
                        padding: 10px;
                    }

                    .past-question-full-image {
                        max-height: 65vh;
                    }
                }
            </style>
            <script>
                $(document).ready(function() {
                    let zoomLevel = 1;

                    const minimumZoom = 1;
                    const maximumZoom = 5;
                    const zoomStep = 0.25;

                    const fullImage = $('#pastQuestionFullImage');
                    const downloadLink = $('#pastQuestionDownload');
                    const imageTitle = $('#pastQuestionImageModalLabel');
                    const imageStage = $('#pastQuestionImageStage');

                    function applyZoom() {
                        fullImage.css(
                            'transform',
                            'scale(' + zoomLevel + ')'
                        );
                    }

                    function resetZoom() {
                        zoomLevel = 1;
                        applyZoom();

                        imageStage.scrollLeft(0);
                        imageStage.scrollTop(0);
                    }

                    $('.past-question-thumbnail').on('click', function() {
                        const imageUrl = $(this).data('image-url');
                        const title = $(this).data('image-title');

                        fullImage.attr('src', imageUrl);
                        fullImage.attr('alt', title);
                        imageTitle.text(title);

                        downloadLink.attr('href', imageUrl);
                        downloadLink.attr(
                            'download',
                            title.toLowerCase().replace(/\s+/g, '_') + '.jpg'
                        );

                        resetZoom();
                    });

                    $('#pastQuestionZoomIn').on('click', function() {
                        zoomLevel = Math.min(
                            maximumZoom,
                            zoomLevel + zoomStep
                        );

                        applyZoom();
                    });

                    $('#pastQuestionZoomOut').on('click', function() {
                        zoomLevel = Math.max(
                            minimumZoom,
                            zoomLevel - zoomStep
                        );

                        applyZoom();
                    });

                    $('#pastQuestionZoomReset').on('click', function() {
                        resetZoom();
                    });

                    fullImage.on('dblclick', function() {
                        zoomLevel = zoomLevel === 1 ? 2 : 1;
                        applyZoom();
                    });

                    $('#pastQuestionImageModal').on(
                        'hidden.bs.modal',
                        function() {
                            fullImage.attr('src', '');
                            resetZoom();
                        }
                    );

                    /*
                     * Mouse-wheel zoom while the modal is open.
                     */
                    imageStage.on('wheel', function(event) {
                        if (!$('#pastQuestionImageModal').hasClass('show')) {
                            return;
                        }

                        event.preventDefault();

                        if (event.originalEvent.deltaY < 0) {
                            zoomLevel = Math.min(
                                maximumZoom,
                                zoomLevel + zoomStep
                            );
                        } else {
                            zoomLevel = Math.max(
                                minimumZoom,
                                zoomLevel - zoomStep
                            );
                        }

                        applyZoom();
                    });

                    /*
                     * Escape is normally handled by Bootstrap,
                     * but this makes it explicit.
                     */
                    $(document).on('keydown', function(event) {
                        if (event.key === 'Escape') {
                            $('#pastQuestionImageModal').modal('hide');
                        }
                    });
                });
            </script>
            {{-- ################################################################ --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
