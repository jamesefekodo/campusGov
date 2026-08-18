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
        @if (Auth::user()->hasPermission('elections.approve'))
            @php
                $totalElectionNotifications = (int) totalElectionsAwaitingApproval() + 0;
            @endphp

            <a href="{{ route('electionOperationRequests') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/election/requests') ? 'inner_nav_active' : '' }}">
                    Requests
                    @if ($totalElectionNotifications > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ $totalElectionNotifications }}</span>
                    @endif
                </div>
            </a>
        @endif


    </div>

</div>
