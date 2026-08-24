<div class="inner_nav_container">
    <div class="inner_nav">

        <a href="{{ route('memberNewElections') }}">
            <div
                class="inner_nav_menu {{ Str::startsWith(request()->path(), 'member/elections/new') ? 'inner_nav_active' : '' }}">
                New
            </div>
        </a>
        <a href="{{ route('memberOngoingElections') }}">
            <div
                class="inner_nav_menu {{ Str::startsWith(request()->path(), 'member/elections/ongoing') ? 'inner_nav_active' : '' }}">
                Ongoing &nbsp; @if (totalOngoingElection() > 0)
                                    <span
                                        class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                                        style="font-size: 11px"><i class="fas fa-thumbs-up me-1 fs-6"></i> </span>
                                @endif
            </div> 
        </a>
        <a href="{{ route('memberNewElections') }}">
            <div
                class="inner_nav_menu {{ Str::startsWith(request()->path(), 'member/elections/completed') ? 'inner_nav_active' : '' }}">
                Completed
            </div>
        </a>


    </div>

</div>
