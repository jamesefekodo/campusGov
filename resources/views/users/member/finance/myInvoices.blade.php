@extends('layouts.userLayout')
@section('title', 'My Invoices')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- inner nav --}}
            </div>

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                My Invoices
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

                                            @if ($data['myInvoices']->isEmpty())
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['myInvoices'] as $invoice)
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
                                                                    href="{{ route('viewMyInvoice', ['uuid' => $invoice->uuid]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['myInvoices'];
                                        @endphp

                                        @include('layouts.include.pagination')
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
