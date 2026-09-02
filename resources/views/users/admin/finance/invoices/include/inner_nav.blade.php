<div class="inner_nav_container">
    <div class="inner_nav">
        @if (Auth::user()->hasPermission('invoice.read'))
            <a href="{{ route('invoices') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/other_payments/invoices') ? 'inner_nav_active' : '' }}">
                    Invoice
                </div>
            </a>
        @endif
        @if (Auth::user()->hasPermission('donation.read'))
            <a href="{{ route('donations') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/other_payments/donations') ? 'inner_nav_active' : '' }}">
                    Donations
                </div>
            </a>
        @endif
    </div>
</div>
