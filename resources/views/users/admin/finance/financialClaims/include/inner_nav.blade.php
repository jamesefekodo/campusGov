<div class="inner_nav_container">
    <div class="inner_nav">

        @if (Auth::user()->hasPermission('financial_claim.read'))
            <a href="{{ route('financialClaims') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/financial_claims') ? 'inner_nav_active' : '' }}">
                    Claims
                </div>
            </a>
        @endif
        @if (Auth::user()->hasPermission('financial_claim.put'))
            <a href="{{ route('financialClaimsPendingApproval') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/financial_claim/approval/approval') ? 'inner_nav_active' : '' }}">
                    New Requests
                    @if (financialClaimPendingApproval() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ financialClaimPendingApproval() }}</span>
                    @endif
                </div>
            </a>
        @endif


    </div>

</div>
