{{-- @extends('layouts.userLayout')
@section('title', $data['method']->name)
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
                                        {{ $data['method']->name }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="row col-md-12">
                                            <div class="col-md-3 text-center">
                                                @if ($data['method']->logo)
                                                    <img src="{{ asset('system/images/paymentMethods/logo/' . $data['method']->logo) }}"
                                                        class="img-fluid">
                                                @endif
                                                <h4 class="my-2">{{ $data['method']->name }}</h4>
                                                <p>Default Version : {{ $data['method']->defaultApi->version }}</p>

                                                @livewire('electronic-payment-status-toggle', ['slug' => $data['method']->slug])
                                            </div>
                                            <div class="col-md-9 row">
                                                @forelse($data['method']->apiConfigs as $config)
                                                    <div class="col-md-4 mb-3">
                                                        <div class="card shadow-sm border-1 h-100">

                                                            <div class="card-body">

                                                                <div
                                                                    class="d-flex justify-content-between align-items-start mb-2">
                                                                    <div>
                                                                        <h5 class="mb-1 fw-bold">
                                                                            {{ $config->paymentMethod->name }}
                                                                            ({{ $config->version }})
                                                                        </h5>
                                                                    </div>

                                                                    @if ($data['method']->logo)
                                                                        <img src="{{ asset('system/images/paymentMethods/logo/' . $data['method']->logo) }}"
                                                                            width="20px">
                                                                    @endif
                                                                </div>

                                                                <hr>

                                                                <p class="mb-1">
                                                                    {{ $config->paymentMethod->name }} ->
                                                                    {{ $config->version }}
                                                                </p>
                                                                @if ($data['method']->defaultApi->id == $config->id)
                                                                    <p class="mb-1 badge bg-blue text-white">
                                                                        Default API
                                                                    </p>
                                                                @endif

                                                                @switch($config->is_active)
                                                                    @case(true)
                                                                        <span class="badge bg-success-subtle text-success me-1">
                                                                            Active
                                                                        </span>
                                                                    @break

                                                                    @default
                                                                        <span class="badge bg-danger-subtle text-danger me-1">
                                                                            Inactive
                                                                        </span>
                                                                @endswitch

                                                            </div>

                                                            <div class="card-footer bg-white border-0">
                                                                <div class="d-flex justify-content-end">
                                                                    <button class="btn btn-outline-primary btn-sm table_btn"
                                                                        data-toggle="modal"
                                                                        data-target="#editPaymentConfigModal_{{ $config->id }}">
                                                                        <i class="fas fa-edit"></i> Update
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @include('users.admin.finance.settings.paymentMethods.include.editElectronicPaymentConfigModal')
                                                        </div>
                                                    </div>

                                                    @empty

                                                        <div class="col-12">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body text-center py-5">
                                                                    <i
                                                                        class="fas fa-graduation-cap fa-3x text-secondary mb-3"></i>
                                                                    <h5>No API COnfig</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforelse

                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                Back Home
                                <div class="w-100 justify-content-center text-center my-4">
                                    <a class="btn btn-primary" href="{{ route('electronicPaymentMethods') }}"> <i
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
    @endsection --}}
