<div class="inner_nav_container">
    <div class="inner_nav">

        @if (Auth::user()->hasPermission('transactions.read'))
            <a href="{{ route('paymentTransactions') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/transactions') ? 'inner_nav_active' : '' }}">
                    Transactions
                    @if (totalPendingPaymentTransactions() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ totalPendingPaymentTransactions() }}</span>
                    @endif
                </div>
            </a>
        @endif
        @if (Auth::user()->hasPermission('transactions.read'))
            <a href="{{ route('paymentRegularizationRequests') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/transaction/regularizations') ? 'inner_nav_active' : '' }}">
                    Regularization
                    @if (totalPendingPaymentRegularizations() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ totalPendingPaymentRegularizations() }}</span>
                    @endif
                </div>
            </a>
        @endif
        @if (Auth::user()->hasPermission('transactions.read'))
            <a href="{{ route('paymentWaiverRequests') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/transaction/waivers') ? 'inner_nav_active' : '' }}">
                    Waivers
                    @if (totalPendingPaymentWaiverRequest() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ totalPendingPaymentWaiverRequest() }}</span>
                    @endif
                </div>
            </a>
        @endif
        

    </div>

</div>
