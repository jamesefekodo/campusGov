<div class="inner_nav_container">
    <div class="inner_nav">

        @if (Auth::user()->hasPermission('elections.read'))
            <a href="{{ route('allElections') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/elections') ? 'inner_nav_active' : '' }}">
                    Elections
                </div>
            </a>
        @endif
        @if (Auth::user()->hasPermission('elections.put'))
            <a href="{{ route('electionOperationRequests') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/transaction/regularizations') ? 'inner_nav_active' : '' }}">
                    Requests
                    {{-- @if (totalPendingPaymentRegularizations() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ totalPendingPaymentRegularizations() }}</span>
                    @endif --}}
                </div>
            </a>
        @endif

        @if (Auth::user()->hasPermission('elections.put'))
            <a href="{{ route('electionOperationRequests') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/transaction/regularizations') ? 'inner_nav_active' : '' }}">
                    Candidates
                    {{-- @if (totalPendingPaymentRegularizations() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ totalPendingPaymentRegularizations() }}</span>
                    @endif --}}
                </div>
            </a>
        @endif

         @if (Auth::user()->hasPermission('elections.put'))
            <a href="{{ route('electionOperationRequests') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/finance/transaction/regularizations') ? 'inner_nav_active' : '' }}">
                    Results
                    {{-- @if (totalPendingPaymentRegularizations() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ totalPendingPaymentRegularizations() }}</span>
                    @endif --}}
                </div>
            </a>
        @endif


    </div>

</div>
