<div class="d-flex flex-wrap justify-content-center">
    <a href="{{ route('manualPaymentMethods') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
        title="Manual Payment Methods"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/finance/settings/payment_methods/manual') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-money-bill-wave"></i>
        </div>
    </a>
    <a href="{{ route('electronicPaymentMethods') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
        title="Electronic Payment Methods"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/finance/settings/payment_methods/electronic') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-mobile-screen-button"></i>
        </div>
    </a>

</div>
