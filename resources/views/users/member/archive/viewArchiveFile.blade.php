@extends('layouts.userLayout')
@section('title', $data['file']->title)
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
                                        {{ $data['file']->title }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">

                                        <div class="col-md-12 row px-3">
                                            <div class="col-md-12 p-1">
                                                <span class="text-black">
                                                    {{ $data['file']->description }}
                                                </span>
                                            </div>
                                            @php
                                                $directory =
                                                    'assets/tenant_' . tenant()->id . '/uploads/archive/files/';
                                                $fileName = $data['file']->file;
                                                $filePath = public_path($directory . $fileName);
                                                $fileUrl = asset($directory . rawurlencode($fileName));
                                                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                                                $previewableFiles = ['pdf', 'csv', 'txt', 'jpg', 'jpeg', 'png', 'webp'];
                                            @endphp

                                            <div class="col-md-12 mb-5">
                                                <div class="card shadow-sm">
                                                    <div
                                                        class="card-header d-flex justify-content-between align-items-center">
                                                        <h5 class="mb-0">
                                                            <i class="fas fa-file-alt me-2"></i>
                                                            {{ $data['file']->title }}
                                                        </h5>

                                                        @if (file_exists($filePath))
                                                            <a href="{{ $fileUrl }}" download="{{ $fileName }}"
                                                                class="btn btn-primary btn-sm">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a>
                                                        @endif
                                                    </div>

                                                    <div class="card-body p-0">
                                                        @if (!empty($fileName) && file_exists($filePath))
                                                            @if ($extension === 'pdf')
                                                                <iframe src="{{ $fileUrl }}#toolbar=1" width="100%"
                                                                    height="700" style="border: none;">
                                                                </iframe>
                                                            @elseif ($extension === 'csv' || $extension === 'txt')
                                                                <div class="p-3"
                                                                    style="max-height: 600px; overflow: auto;">
                                                                    <pre class="mb-0" style="white-space: pre-wrap;">{{ file_get_contents($filePath) }}</pre>
                                                                </div>
                                                            @elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                <div class="text-center p-3">
                                                                    <img src="{{ $fileUrl }}" class="img-fluid"
                                                                        alt="Document preview">
                                                                </div>
                                                            @else
                                                                <div class="text-center p-5">
                                                                    <i class="fas fa-file-word fa-4x text-primary mb-3"></i>

                                                                    <h5>Preview unavailable for this file type</h5>

                                                                    <p class="text-muted">
                                                                        Download the document to read it, or configure a
                                                                        document
                                                                        viewer to preview Word, Excel, and PowerPoint files
                                                                        here.
                                                                    </p>

                                                                    <a href="{{ $fileUrl }}"
                                                                        download="{{ $fileName }}"
                                                                        class="btn btn-outline-primary">
                                                                        <i class="fas fa-download me-1"></i> Download and
                                                                        Read
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="alert alert-warning m-3">
                                                                The file could not be found.
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            @include('users.admin.archive.include.shareArchiveFile')

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Created by:</span>
                                                <span class="text-black">
                                                    @if ($data['file']->created_by)
                                                        @if ($data['file']->createdBy?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['file']->createdBy->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['file']->createdBy->memberPersonalProfile->middle_name
                                                                        ? $data['file']->createdBy->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['file']->createdBy->memberPersonalProfile->last_name,
                                                            ) }}
                                                        @else
                                                            {{ $data['file']->created_by }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $createdAt = \Carbon\Carbon::parse($data['file']->created_at);
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
                                                    @if ($data['file']->last_update_by)
                                                        @if ($data['file']->lastUpdateBy?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['file']->lastUpdateBy->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['file']->lastUpdateBy->memberPersonalProfile->middle_name
                                                                        ? $data['file']->lastUpdateBy->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['file']->lastUpdateBy->memberPersonalProfile->last_name,
                                                            ) }}

                                                            <a href="{{ route('viewMember', ['uuid' => $data['file']->lastUpdateBy->uuid]) }}"
                                                                target="_blank" class="badge bg-secondary text-white"
                                                                title="View Initiator">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                        @else
                                                            {{ $data['file']->last_update_by }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $createdAt = \Carbon\Carbon::parse($data['file']->updated_at);
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
                                <a class="btn btn-primary" href="{{ route('memberArchive') }}"> <i
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
