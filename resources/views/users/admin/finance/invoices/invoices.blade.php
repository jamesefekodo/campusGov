@extends('layouts.userLayout')
@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.finance.invoices.include.inner_nav')
            </div>
            @if (Auth::user()->hasPermission('invoice.create'))
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal" data-target="#addNewInvoiceModal">
                            <i class="fas fa-plus"></i> New Invoice</button>
                    </div>
                </div>
                @include('users.admin.finance.invoices.include.addNewInvoiceModal')
            @endif

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-8">
                            <div class="table_title">
                                {{ $data['pageTitle'] }}
                            </div>
                        </div>

                        <div class="table_head_item col-md-4">
                            <div class="custom_search">
                                <form type="GET" action="{{ route('invoicesSearch') }}">
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
                                                        Invoice No
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Amount
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Description
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Invoice For
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Date Generated
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Due Date
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['invoices']->isEmpty())
                                                <tr>
                                                    <td colspan="9" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['invoices'] as $invoice)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-uppercase">
                                                                {{ $invoice->invoice_number }}
                                                            </td>
                                                            <td>
                                                                ₦{{ number_format($invoice->amount, 2) }}
                                                            </td>
                                                            <td>
                                                                {{ $invoice->description }}
                                                            </td>
                                                            <td>
                                                                @if ($invoice->invoiceFor)
                                                                    {{ $invoice->invoiceFor?->memberPersonalProfile?->first_name }}
                                                                    {{ $invoice->invoiceFor?->memberPersonalProfile?->last_name }}
                                                                    <a href="{{ route('viewMember', ['uuid' => $invoice->invoiceFor->uuid]) }}"
                                                                        target="_blank"
                                                                        class="badge bg-secondary text-white"
                                                                        title="View Owner">
                                                                        <i class="fas fa-link"></i>
                                                                    </a>
                                                                @else
                                                                    {{ $invoice->non_registered_payer }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($invoice->issue_date)->format('l, d F, Y') }}
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('l, d F, Y h:i A') }}
                                                            </td>
                                                            <td>
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
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewInvoice', ['uuid' => $invoice->uuid]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['invoices'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            @switch($data['requestType'])
                                                @case('all')
                                                    {{ '' }}
                                                @break

                                                @case('search')
                                                    <a class="btn btn-success" href="{{ route('invoices') }}"> <i
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





        </div>
    </div><!--//app-content-->
@endsection
