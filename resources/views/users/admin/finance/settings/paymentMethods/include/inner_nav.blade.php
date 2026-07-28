<div class="inner_nav_container">
    <div class="inner_nav">
        <a href="{{ route('manualPaymentMethods') }}">
            <div
                class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/finance/settings/payment_methods') ? 'inner_nav_active' : '' }}">
                Payment Methods</div>
        </a>
        <a href="#">
            <div
                class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/user_management/members/non_students') ? 'inner_nav_active' : '' }}">
                Taxes</div>
        </a>

    </div>

</div>
