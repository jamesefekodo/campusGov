@extends('layouts.userLayout')
@section('title', 'Payment Methods (Electronic)')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.finance.settings.paymentMethods.include.inner_nav')
            </div>


            <div class="individual-review-card-container row">
                <div class="col-md-1 individual-review-card text-black">
                    {{-- sidde menu --}}
                    @include('users.admin.finance.settings.paymentMethods.include.paymentMethodsSideMenu')
                </div>
                <div class="col-md-11 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item">
                            <div class="table_title">
                                Payment Methods (Electronic)
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
                                                        Payment Method
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Logo
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['electronicPaymentMethods']->isEmpty())
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['electronicPaymentMethods'] as $method)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start">
                                                                {{ $method->name }}
                                                            </td>
                                                            <td class="">
                                                                @if ($method->logo)
                                                                    <img src="{{ asset('system/images/paymentMethods/logo/' . $method->logo) }}"
                                                                        alt="" width="50px">
                                                                @endif
                                                            </td>
                                                            <td class="">
                                                                @if ($method->apiConfig)
                                                                    @switch($method->apiConfig->is_active)
                                                                        @case(true)
                                                                            <span class="badge bg-success-subtle text-success">
                                                                                Active
                                                                            </span>
                                                                        @break

                                                                        @default
                                                                            <span class="badge bg-danger-subtle text-danger">
                                                                                Inactive
                                                                            </span>
                                                                    @endswitch
                                                                @else
                                                                    <span class="badge bg-danger-subtle text-danger">
                                                                        Not-Available
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{-- <a class="btn btn-primary btn-sm {{ $method->apiConfig ? '' : 'disabled' }}"
                                                                    href="{{ $method->apiConfig ? route('viewElectronicPaymentMethod', ['uuid' => $method->uuid]) : '#' }}"
                                                                    @unless ($method->apiConfig) aria-disabled="true" tabindex="-1" @endunless>
                                                                    View
                                                                </a> --}}
                                                                <div class="m-1">
                                                                    <button
                                                                        class="btn {{ $method->apiConfig ? 'btn-primary' : 'btn-success' }} btn-sm table_btn"
                                                                        data-toggle="modal"
                                                                        data-target="#addNewElectronicPaymentMethodModal_{{ $method->id }}">
                                                                        <i
                                                                            class="fas {{ $method->apiConfig ? 'fa-edit' : 'fa-plus' }}"></i>
                                                                        {{ $method->apiConfig ? 'Update' : 'Create' }}
                                                                    </button>
                                                                </div>

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


            {{-- modal start --}}
            @if ($data['electronicPaymentMethods']->isNotEmpty())
                @foreach ($data['electronicPaymentMethods'] as $method)
                    @include('users.admin.finance.settings.paymentMethods.include.addNewElectronicsPaymentMethodModal')
                @endforeach
            @endif

            {{-- modal end --}}
        </div>
    </div><!--//app-content-->
@endsection
