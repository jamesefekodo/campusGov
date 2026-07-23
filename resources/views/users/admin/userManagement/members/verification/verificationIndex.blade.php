@extends('layouts.userLayout')
@section('title', 'Verify ' . ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? ''))
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-3 individual-review-card text-black">
                            @include('users.admin.userManagement.members.verification.include.verificationSidebar')
                        </div>
                        <div class="col-md-9 individual-review-card">

                            <h4 class="block text-white text-center p-1 type-b-page-header text-capitalize">
                                Verify @php
                                    $personal = $data['member']->memberPersonalProfile;
                                    $middleInitial = $personal?->middle_name
                                        ? strtoupper(substr($personal->middle_name, 0, 1)) . '. '
                                        : '';
                                @endphp

                                {{ ($personal?->first_name ?? $data['member']->username) . ' ' . $middleInitial . ($personal?->last_name ?? '') }}
                            </h4>
                            <div class="review-info-card-container">
                                <a href="#" type="button" data-toggle="modal" data-target="#basicAccountInfoModal"
                                    style="text-decoration: none" data-toggle="popover" data-trigger="focus"
                                    data-placement="top" title="Basic Account Info">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fa fa-user-circle"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Basic Acc. Info</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="{{ route('verifyMemberPersonalProfile', ['uuid' => $data['member']->uuid]) }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Personal Profile
                                            </p>
                                            @php
                                                $profile = $data['member']->memberPersonalProfile ?? null;
                                                $isVerified = $profile?->is_verified;
                                            @endphp

                                            <small style="font-size: 10px;"
                                                class="badge {{ $isVerified ? 'bg-success' : 'bg-danger' }}">
                                                @if ($profile)
                                                    @switch($isVerified)
                                                        @case(true)
                                                            Verified
                                                        @break

                                                        @case(false)
                                                            Unverified
                                                        @break

                                                        @default
                                                            N/A
                                                    @endswitch
                                                @else
                                                    N/A
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('verifyMemberAcademicInfo', ['uuid' => $data['member']->uuid]) }}">

                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Academic Info
                                            </p>
                                            @php
                                                $accInfo = $data['member']->memberAcademicInfo ?? null;
                                                $isVerified = $accInfo?->is_verified;
                                            @endphp

                                            <small style="font-size: 10px;"
                                                class="badge {{ $isVerified ? 'bg-success' : 'bg-danger' }}">
                                                @if ($profile)
                                                    @switch($isVerified)
                                                        @case(true)
                                                            Verified
                                                        @break

                                                        @case(false)
                                                            Unverified
                                                        @break

                                                        @default
                                                            N/A
                                                    @endswitch
                                                @else
                                                    N/A
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </a>

                                <a href="#" class="activate-account" uuid="{{ $data['member']->uuid }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fa fa-check-circle"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Activate Account</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="disable-account" uuid="{{ $data['member']->uuid }}">
                                    <div class="review-info-card">
                                        <div class="review-info-icon">
                                            <i class="fa fa-times-circle"></i>
                                        </div>
                                        <div class="review-info-content">
                                            <p class="review-card-info-title">
                                                Disable Account</p>
                                        </div>
                                    </div>
                                </a>

                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('memberAwaitingVerification') }}">
                            <i class="fas fa-chevron-left"></i>
                            Back
                        </a>

                    </div>

                </div>

            </div>
        </div>

        {{-- modals start --}}
        @include('users.admin.userManagement.members.verification.include.updateBasicAccountInfoModal');
        {{-- modals end --}}

        {{-- Account Status Toggle Script --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const confirmAction = function(selector, actionName, urlTemplate) {
                    document.querySelectorAll(selector).forEach(button => {
                        button.addEventListener('click', function(event) {
                            event.preventDefault();
                            const uuid = this.getAttribute('uuid');

                            Swal.fire({
                                title: `${actionName} Account?`,
                                text: '',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'No',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Replaces the placeholder token with the actual element's uuid
                                    window.location.href = urlTemplate.replace(
                                        'PLACEHOLDER', uuid);
                                }
                            });
                        });
                    });
                };

                // Ensure these exact route names exist in your routes/web.php file
                confirmAction(
                    '.activate-account',
                    'Activate',
                    "{{ route('verifyAndactivateStudentAccount', ['uuid' => 'PLACEHOLDER']) }}"
                );

                confirmAction(
                    '.disable-account',
                    'Disable',
                    "{{ route('disableMemberAccount', ['uuid' => 'PLACEHOLDER']) }}"
                );
            });
        </script>
    </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
