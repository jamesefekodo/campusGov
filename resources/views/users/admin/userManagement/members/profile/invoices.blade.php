@extends('layouts.userLayout')
@section('title', ($data['member']->memberPersonalProfile?->first_name ?? $data['member']->username) . ' ' .
    ($data['member']->memberPersonalProfile?->last_name ?? '') . ' - Invoices')
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
                                    Invoices
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    @forelse($data['invoices'] as $invoice)
                                        <div class="col-md-6 mb-3">
                                            <div class="card shadow-sm border-1 h-100">

                                                <div class="card-body p-4">

                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h5 class="mb-1 fw-bold">
                                                                ₦{{ number_format($invoice->amount, 2) }}
                                                            </h5>

                                                            <small class="text-muted text-uppercase">
                                                                {{ $invoice->invoice_number }}
                                                            </small>
                                                        </div>

                                                        <i class="fas fa-file-invoice fs-3 text-secondary"></i>
                                                    </div>

                                                    <hr>

                                                    <p class="mb-2">
                                                        <strong>Invoice Number</strong>:
                                                        <span class="text-uppercase">
                                                            {{ $invoice->invoice_number }}
                                                        </span>
                                                    </p>
                                                    <p class="mb-3">
                                                        <strong>Amount</strong>:
                                                        ₦{{ number_format($invoice->amount, 2) }}
                                                    </p>
                                                    <p class="mb-3">
                                                        <strong>Description</strong>:
                                                        {{ $invoice->description }}
                                                    </p>

                                                    <p class="mb-3">
                                                        <strong>Issued On</strong>:
                                                        {{ \Carbon\Carbon::parse($invoice->issue_date)->format('l, d F, Y') }}
                                                    </p>

                                                    <p class="mb-3">
                                                        <strong>Due Date</strong>:
                                                        {{ \Carbon\Carbon::parse($invoice->due_date)->format('l, d F, Y h:i A') }}
                                                    </p>

                                                    <p class="mb-3">
                                                        <strong>Status</strong>:
                                                        @switch($invoice->status)
                                                            @case('paid')
                                                                <span class="badge bg-green text-white">Paid</span>
                                                            @break

                                                            @case('expired')
                                                                <span class="badge bg-red text-white">Expired</span>
                                                            @break

                                                            @case('pending')
                                                                <span class="badge bg-yellow text-black">Pending</span>
                                                            @break

                                                            @default
                                                                <span
                                                                    class="badge bg-yellow text-black">{{ $invoice->status }}</span>
                                                        @endswitch
                                                    </p>
                                                    @if (Auth::user()->hasPermission('misconducts.read'))
                                                        <div class="d-flex justify-content-end">
                                                            <a class="btn btn-primary btn-sm"
                                                                href="{{ route('viewInvoice', ['uuid' => $invoice->uuid]) }}">View</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @empty

                                            <div class="col-12">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body text-center py-5">
                                                        <h5>No Records Found!</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                        @if (Auth::user()->hasPermission('invoice.create'))
                                            <div class="d-flex justify-content-end flex-wrap mt-3">
                                                <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                                    data-target="#addNewMisconductModal">
                                                    <i class="fas fa-plus"></i> Create New</button>
                                            </div>


                                            @include('users.admin.misconducts.include.addNewMisconductModal')
                                        @endif
                                    </div>
                                </div>
                                {{-- Pagination --}}
                                @php
                                    $paginatedArray = $data['invoices'];
                                @endphp

                                @include('layouts.include.pagination')
                            </div>
                        </div>
                        {{-- Back Home --}}
                        <div class="w-100 justify-content-center text-center my-4 pt-5">
                            <a class="btn btn-primary" href="{{ route('viewMember', ['uuid' => $data['member']->uuid]) }}">
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
