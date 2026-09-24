@extends('layouts.userLayout')
@section('title', $data['publication']->title)
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
                                        {{ $data['publication']->title }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        @php
                                            $coverImage = $data['publication']->cover_image ?? null;
                                            $coverPath = $coverImage
                                                ? public_path(
                                                    'assets/tenant_' .
                                                        tenantId() .
                                                        '/uploads/media/publications/' .
                                                        $coverImage,
                                                )
                                                : null;
                                            $hasCoverImage = $coverPath && file_exists($coverPath);
                                        @endphp

                                        <div class="publication-cover-wrapper mb-4">
                                            @if ($hasCoverImage)
                                                <img src="{{ asset('assets/tenant_' . tenantId() . '/uploads/media/publications/' . $coverImage) }}"
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
                                            <div class="col-md-6 p-1">
                                                <span class="text-black">
                                                    {!! $data['publication']->content !!}
                                                </span>
                                            </div>

                                            @include('users.admin.media.publications.include.sharePublication')

                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Author:</span>
                                                <span class="text-black">
                                                    @if ($data['publication']->author)
                                                        @if ($data['publication']->authorUser?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['publication']->authorUser->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['publication']->authorUser->memberPersonalProfile->middle_name
                                                                        ? $data['publication']->authorUser->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['publication']->authorUser->memberPersonalProfile->last_name,
                                                            ) }}

                                                            <a href="{{ route('viewMember', ['uuid' => $data['publication']->authorUser->uuid]) }}"
                                                                target="_blank" class="badge bg-secondary text-white"
                                                                title="View Initiator">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                        @else
                                                            {{ $data['publication']->author }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $createdAt = \Carbon\Carbon::parse(
                                                            $data['publication']->created_at,
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
                                                    @if ($data['publication']->last_update_by)
                                                        @if ($data['publication']->lastUpdateUser?->memberPersonalProfile)
                                                            {{ trim(
                                                                $data['publication']->lastUpdateUser->memberPersonalProfile->first_name .
                                                                    ' ' .
                                                                    ($data['publication']->lastUpdateUser->memberPersonalProfile->middle_name
                                                                        ? $data['publication']->lastUpdateUser->memberPersonalProfile->middle_name . ' '
                                                                        : '') .
                                                                    $data['publication']->lastUpdateUser->memberPersonalProfile->last_name,
                                                            ) }}

                                                            <a href="{{ route('viewMember', ['uuid' => $data['publication']->lastUpdateUser->uuid]) }}"
                                                                target="_blank" class="badge bg-secondary text-white"
                                                                title="View Initiator">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                        @else
                                                            {{ $data['publication']->last_update_by }}
                                                        @endif
                                                    @endif -
                                                    @php
                                                        $updatedAt = \Carbon\Carbon::parse(
                                                            $data['publication']->last_update,
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
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Status:</span>
                                                <span class="text-black">
                                                    @switch($data['publication']->approval_status)
                                                        @case('draft')
                                                            <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                                        @break

                                                        @case('pending')
                                                            <span class="badge bg-warning-subtle text-warning">Awaiting
                                                                Approval</span>
                                                        @break

                                                        @case('approved')
                                                            <span class="badge bg-success-subtle text-success">Approved</span>
                                                        @break

                                                        @case('declined')
                                                            <span class="badge bg-danger-subtle text-danger">Declined</span>
                                                        @break

                                                        @default
                                                            <span
                                                                class="badge bg-danger-subtle text-danger">{{ $data['publication']->approval_status }}</span>
                                                    @endswitch
                                                    @if ($data['publication']->is_active == true)
                                                        <span class="badge bg-green text-white">Published</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>


                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            @if (Auth::user()->hasPermission('media_publication.update') || $data['publication']->author)
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#editMediaPublication">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                            @endif


                                        </div>
                                        @include('users.admin.media.publications.include.editMediaPublication')



                                        <div class="d-flex align-items-center mt-4">
                                            <hr class="flex-grow-1">
                                            <span class="mx-2 text-black">Payments
                                            </span>
                                            <hr class="flex-grow-1">
                                        </div>

                                        <div class="row g-3 mt-2 mb-4">
                                            <div class="col-md-4">
                                                <div
                                                    class="card shadow-sm border-0 bg-primary-subtle bg-opacity-10 h-100 m-2">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i class="fas fa-users fa-2x text-blue"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small">Total Expected Payers</div>
                                                                <div class="fs-1 fw-bold text-blue">
                                                                    {{-- {{ $data['due']->expectedPayerCount() }} --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="d-flex gap-2 mt-auto">
                                                            <a
                                                            href="{{ route('due.expectedPayers', ['uuid' => $data['due']->uuid]) }}"
                                                                class="btn btn-sm btn-primary flex-fill">
                                                                <i class="fas fa-eye me-1"></i> View
                                                            </a>
                                                            <a
                                                            href="{{ route('due.expectedPayers.download', ['uuid' => $data['due']->uuid]) }}"
                                                                class="btn btn-sm btn-outline-primary flex-fill">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="card shadow-sm border-0 bg-info-subtle bg-opacity-10 h-100 m-2">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i class="fas fa-check-circle fa-2x text-info"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small">Successful Payments</div>
                                                                <div class="fs-4 fw-bold text-info">
                                                                    {{-- {{ $data['due']->successfulPaymentCount() }} --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 mt-auto">
                                                            {{-- <form type="GET"
                                                                action="{{ route('paymentTransactionsSearch') }}"
                                                                target="_blank" class="w-100">
                                                                @csrf
                                                                <div class="input-group-append">
                                                                    <input type="hidden" class="table_search my-2 my-sm-0"
                                                                        name="searchKey"
                                                                        value="{{ $data['due']->fee_id }}">
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-info flex-fill w-100">
                                                                        <i class="fas fa-eye me-1"></i> View
                                                                    </button>
                                                                </div>
                                                            </form> --}}
                                                            {{-- <a href="{{ route('due.successfulPayments.download', ['uuid' => $data['due']->uuid]) }}"
                                                                class="btn btn-sm btn-outline-info flex-fill">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div
                                                    class="card shadow-sm border-0 bg-warning-subtle bg-opacity-10 h-100 m-2">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i class="fas fa-hand-holding-heart fa-2x text-warning"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small">Waived</div>
                                                                <div class="fs-4 fw-bold text-warning">
                                                                    {{-- {{ $data['due']->approvedWaiverCount() }} --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 mt-auto">
                                                            {{-- <form type="GET"
                                                                action="{{ route('paymentWaiverRequestsSearch') }}"
                                                                target="_blank" class="w-100">
                                                                @csrf
                                                                <div class="input-group-append">
                                                                    <input type="hidden"
                                                                        class="table_search my-2 my-sm-0" name="searchKey"
                                                                        value="{{ $data['due']->fee_id }}">
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-warning flex-fill w-100">
                                                                        <i class="fas fa-eye me-1"></i> View
                                                                    </button>
                                                                </div>
                                                            </form> --}}
                                                            {{-- <a href="{{ route('due.paidOrWaived.download', ['uuid' => $data['due']->uuid]) }}"
                                                                class="btn btn-sm btn-outline-warning flex-fill">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div
                                                    class="card shadow-sm border-0 bg-success-subtle bg-opacity-10 h-100 m-2">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i class="fas fa-hand-holding-heart fa-2x text-success"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small">Paid or Waived</div>
                                                                <div class="fs-4 fw-bold text-success">
                                                                    {{-- {{ $data['due']->membersPaidOrWaived()->count() }} --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 mt-auto">
                                                            {{-- <a href="{{ route('due.outstanding', ['uuid' => $data['due']->uuid]) }}"
                                                                class="btn btn-sm btn-success flex-fill">
                                                                <i class="fas fa-eye me-1"></i> View
                                                            </a> --}}
                                                            <a href="
                                                            {{-- {{ route('due.paidOrWaived.download', ['uuid' => $data['due']->uuid]) }} --}}
                                                             "
                                                                class="btn btn-sm btn-success flex-fill">
                                                                <i class="fas fa-print me-1"></i> Print
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="card shadow-sm border-0 bg-danger bg-opacity-10 h-100 m-2">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i
                                                                    class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small">Outstanding</div>
                                                                <div class="fs-4 fw-bold text-danger">
                                                                    {{-- {{ $data['due']->membersUnpaidAndUnwaived()->count() }} --}}
                                                                    {{-- {{ $data['due']->expectedPayerCount() - $data['due']->membersPaidOrWaived()->count() }} --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 mt-auto">
                                                            {{-- <a href="{{ route('due.outstanding', ['uuid' => $data['due']->uuid]) }}"
                                                                class="btn btn-sm btn-danger flex-fill">
                                                                <i class="fas fa-eye me-1"></i> View
                                                            </a> --}}
                                                            <a {{-- href="{{ route('due.outstanding.download', ['uuid' => $data['due']->uuid]) }}" --}}
                                                                class="btn btn-sm btn-danger flex-fill w-100">
                                                                <i class="fas fa-print me-1"></i> Print
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('associationDues') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>



            {{-- <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.delete-confirm').forEach(button => {
                        button.addEventListener('click', function() {

                            const url = this.dataset.url;

                            Swal.fire({
                                title: 'Are you sure?',
                                text: "This policy will be permanently deleted.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = url;
                                }
                            });

                        });
                    });
                });
            </script> --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
