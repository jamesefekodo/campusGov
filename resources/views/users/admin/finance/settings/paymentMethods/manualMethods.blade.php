@extends('layouts.userLayout')
@section('title', 'Payment Methods (Manual)')

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
                                Payment Methods (Manual)
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 m-auto">
                        <div class="my_form_container pb-4 px-3 pt-2">

                            @livewire('manual-payment-methods-toggle')

                            @if ($data['manualMethodSetting']->bank_transfer)
                                <div class="d-flex align-items-center mt-4">
                                    <hr class="flex-grow-1">
                                    <span class="mx-2 text-black">Bank Accounts
                                    </span>
                                    <hr class="flex-grow-1">
                                </div>
                                <div class="px-3 pt-3 table-responsive">
                                    <table class="table">
                                        <tbody>
                                            @if ($data['bankAccounts']->isNotEmpty())
                                                @foreach ($data['bankAccounts'] as $account)
                                                    <tr class="text-black">
                                                        <th class="" scope="row">{{ $loop->iteration }}
                                                        </th>
                                                        <td class="">
                                                            {{ $account->account_number }}
                                                        </td>
                                                        <td class="">
                                                            {{ $account->account_name }}
                                                        </td>
                                                        <td class="">
                                                            {{ $account->bank }}
                                                        </td>
                                                        <td class="flex-wrap">
                                                            <span class="badge bg-blue text-white pe-2 ps-3 py-2 rounded-3"
                                                                data-toggle="modal"
                                                                data-target="#editBankAccountModal_{{ $account->id }}"
                                                                style="cursor: pointer;">
                                                                <i class="fas fa-edit me-1"></i>Edit
                                                            </span>
                                                            <a href="javascript:void(0)"
                                                                class="badge bg-red text-white pe-2 ps-3 py-2 rounded-3 delete-confirm"
                                                                data-toggle="modal"
                                                                data-url="{{ route('removeBankAccount', ['id' => $account->id]) }}"
                                                                style="cursor: pointer;">
                                                                <i class="fas fa-trash me-1"></i>Remove
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    @if (Auth::user()->hasPermission('manage_payment_settings'))
                                        <div class="d-flex justify-content-center">
                                            <div class="m-1">
                                                <button class="btn btn-primary btn-sm table_btn" data-toggle="modal"
                                                    data-target="#addNewBankAccountModal">
                                                    <i class="fas fa-plus"></i> Add New Bank Account</button>
                                            </div>
                                        </div>
                                    @endif
                                    @include('users.admin.finance.settings.paymentMethods.include.addNewBankAccountModal')
                                    @if ($data['bankAccounts']->isNotEmpty())
                                        @foreach ($data['bankAccounts'] as $account)
                                            @include('users.admin.finance.settings.paymentMethods.include.editBankAccountModal')
                                        @endforeach
                                    @endif

                                </div>
                            @endif

                        </div>



                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('click', function(e) {
                    // Find the closest element with the .delete-confirm class
                    const btn = e.target.closest('.delete-confirm');

                    if (btn) {
                        e.preventDefault();
                        const url = btn.getAttribute('data-url');


                        Swal.fire({
                            title: "Are you sure?",
                            text: "Remove this bank account",
                            icon: 'warning',
                            showCancelButton: true,
                            buttonsStyling: false,

                            customClass: {
                                confirmButton: 'btn btn-danger mx-2',
                                cancelButton: 'btn btn-secondary mx-2'
                            },
                            confirmButtonText: "Yes, Remove",
                            cancelButtonText: "Cancel",
                            background: '#ffffff',
                            color: '#000000',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                Swal.showLoading();
                                return true;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = url;
                            }
                        });
                    }
                });
            </script>



        </div>
    </div><!--//app-content-->
@endsection
