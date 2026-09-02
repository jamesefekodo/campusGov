@extends('layouts.userLayout')
@section('title', 'Invoice - ' . $data['invoice']->invoice_number)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="my_form_container">
                                <h4 class="block text-white text-center py-2 type-b-page-header text-capitalize">
                                    Invoice - <span
                                        class="text-uppercase">{{ strtolower($data['invoice']->invoice_number) }}</span>
                                </h4>
                                <div class="card-body p-4 row col-md-12">
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Invoice Number:</span>
                                        <span class="text-black text-uppercase">
                                            {{ $data['invoice']->invoice_number }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Invoice for:</span>
                                        <span class="text-black">
                                            @if ($data['invoice']->invoiceFor)
                                                {{ $data['invoice']->invoiceFor?->memberPersonalProfile?->first_name }}
                                                {{ $data['invoice']->invoiceFor?->memberPersonalProfile?->last_name }}
                                                <a href="{{ route('viewMember', ['uuid' => $data['invoice']->invoiceFor->uuid]) }}"
                                                    target="_blank" class="badge bg-secondary text-white"
                                                    title="View Approver">
                                                    <i class="fas fa-link"></i>
                                                </a>
                                            @else
                                                {{ $data['invoice']->non_registered_payer }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Amount:</span>
                                        <span class="text-black">
                                            ₦{{ number_format($data['invoice']->amount, 2) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Description:</span>
                                        <span class="text-black">
                                            {{ $data['invoice']->description }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Issued Date:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['invoice']->issue_date)->format('l, d F, Y') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Due Date:</span>
                                        <span class="text-black">
                                            {{ \Carbon\Carbon::parse($data['invoice']->due_date)->format('l, d F, Y h:i A') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Status:</span>
                                        <span class="text-black">
                                            @switch($data['invoice']->status)
                                                @case('paid')
                                                    <span class="badge bg-green text-white">Paid</span> <a
                                                        href="{{ route('viewPaymentTransaction', ['payment_transaction_id' => $data['invoice']->successfulPayment->payment_transaction_id]) }}"
                                                        target="_blank" class="badge bg-secondary text-white" title="View Approver">
                                                        <i class="fas fa-link"></i>
                                                    </a>
                                                @break

                                                @case('expired')
                                                    <span class="badge bg-red text-white">Expired</span>
                                                @break

                                                @case('pending')
                                                    <span class="badge bg-yellow text-black">Pending</span>
                                                @break

                                                @default
                                                    <span class="badge bg-yellow text-black">{{ $data['invoice']->status }}</span>
                                            @endswitch </span>
                                    </div>
                                    <div class="col-md-6 p-1">
                                        <span style="font-weight: bold">Created by:</span>
                                        <span class="text-black">
                                            @if ($data['invoice']?->createdBy?->memberPersonalProfile)
                                                {{ $data['invoice']->createdBy?->memberPersonalProfile?->first_name }}
                                                {{ $data['invoice']->createdBy?->memberPersonalProfile?->last_name }}
                                                <a href="{{ route('viewMember', ['uuid' => $data['invoice']->createdBy->uuid]) }}"
                                                    target="_blank" class="badge bg-secondary text-white"
                                                    title="View Approver">
                                                    <i class="fas fa-link"></i>
                                                </a>
                                            @else
                                                {{ $data['invoice']->created_by }}
                                            @endif |
                                            {{ \Carbon\Carbon::parse($data['invoice']->created_at)->format('l, d F, Y h:i A') }}
                                        </span>
                                    </div>

                                    <hr class="mt-5">
                                    <div class="d-flex justify-content-end flex-wrap mt-3">
                                        @if (Auth::user()->hasPermission('invoice.update'))
                                            @if (!$data['invoice']->invoiceFor)
                                                <button class="btn btn-primary m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#linkInvoiceToMemberModal">
                                                    <i class="fas fa-user me-2"></i>
                                                    <span>Link to to a member</span>
                                                </button>
                                                
                                                @include('users.admin.finance.invoices.include.linkInvoiceToMemberModal')
                                            @endif
                                            @if ($data['invoice']->status == 'pending')
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#editInvoiceModal">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                                @include('users.admin.finance.invoices.include.editInvoiceModal')
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Back Home --}}
                    <div class="w-100 justify-content-center text-center my-4 pt-5">
                        <a class="btn btn-primary" href="{{ route('invoices') }}">
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
