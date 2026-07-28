@extends('layouts.userLayout')
@section('title', 'Taxes')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.finance.settings.paymentMethods.include.inner_nav')
            </div>
            @if (Auth::user()->hasPermission('manage_payment_settings'))
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        <button class="btn btn-success btn-sm table_btn" data-toggle="modal" data-target="#addNewTaxModal">
                            <i class="fas fa-plus"></i> Add New</button>
                    </div>
                </div>
                @include('users.admin.finance.settings.taxes.include.addNewTaxModal')
            @endif
            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item">
                            <div class="table_title">
                                Taxes
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
                                                        Tax Name
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Method
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Rate
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Update
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['taxes']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['taxes'] as $tax)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td>
                                                                {{ $tax->tax_name }}
                                                            </td>
                                                            <td>
                                                                @switch($tax->tax_method)
                                                                    @case('percentage')
                                                                        Percentage
                                                                    @break

                                                                    @case('fixed_amount')
                                                                        Fixed Amount
                                                                    @break

                                                                    @default
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                @switch($tax->tax_method)
                                                                    @case('percentage')
                                                                        {{ $tax->tax_rate }}%
                                                                    @break

                                                                    @case('fixed_amount')
                                                                        ₦{{ number_format($tax->tax_rate, 2) }}
                                                                    @break

                                                                    @default
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                @switch($tax->is_active)
                                                                    @case(true)
                                                                        <span class="badge bg-green text-white">
                                                                            Active
                                                                        </span>
                                                                    @break

                                                                    @default
                                                                        <span class="badge bg-red text-white">
                                                                            Inactive
                                                                        </span>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-warning btn-sm" data-toggle="modal"
                                                                    data-target="#updateTaxModal_{{ $tax->id }}">
                                                                    Edit
                                                                </a>
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
            @if ($data['taxes']->isNotEmpty())

                @foreach ($data['taxes'] as $tax)
                    @include('users.admin.finance.settings.taxes.include.updateTaxModal')
                @endforeach
            @endif

        </div>
    </div><!--//app-content-->
@endsection
