@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? '') . ' - Social Media Handles')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @include('users.admin.userManagement.members.profile.include.memberProfileSidebar')
                        </div>
                        <div class="col-md-9 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Social Media Handles
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    @forelse($data['member']->memberSocialMediaHandle as $handle)
                                        <div class="col-md-3 mb-3">
                                            <div class="card shadow-sm border-1 h-100">

                                                <div class="card-body">

                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h5 class="mb-1 fw-bold">
                                                                {{ $handle->thePlatform->name }}
                                                            </h5>
                                                        </div>

                                                        @if (
                                                            $handle->thePlatform->logo &&
                                                                file_exists(public_path('assets/uploads/users/media/recognizedPlatforms/logo/' . $handle->thePlatform->logo)))
                                                            <img src="{{ asset('assets/uploads/users/media/recognizedPlatforms/logo/' . $handle->thePlatform->logo) }}"
                                                                alt="{{ $handle->thePlatform->name }}" width="50px">
                                                        @else
                                                            <img src="{{ asset('system/images/logo_placeholder.png') }}"
                                                                alt="Default Picture" width="50px">
                                                        @endif
                                                    </div>

                                                    <hr>

                                                    <p class="mb-2">
                                                        <strong>Handle</strong><br>
                                                        @if ($handle->link)
                                                            @php
                                                                $url = \Illuminate\Support\Str::startsWith(
                                                                    $handle->link,
                                                                    ['http://', 'https://'],
                                                                )
                                                                    ? $handle->link
                                                                    : 'https://' . $handle->link;
                                                            @endphp

                                                            <a href="{{ $url }}" target="_blank"
                                                                rel="noopener noreferrer">
                                                                {{ $handle->handle }}
                                                            </a>
                                                        @endif
                                                    </p>

                                                </div>
                                            </div>
                                        </div>

                                    @empty

                                        <div class="col-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body text-center py-5">
                                                    <h5>No Records Found</h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('viewMMember', ['uuid' => $data['member']->uuid]) }}">
                            <i class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>

                </div>

            </div>
        </div>

    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
