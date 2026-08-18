<div class="inner_nav_container">
    <div class="inner_nav">

        <a href="{{ route('memberNewElections') }}">
            <div
                class="inner_nav_menu {{ Str::startsWith(request()->path(), 'member/elections/new') ? 'inner_nav_active' : '' }}">
                New
            </div>
        </a>
        <a href="{{ route('memberNewElections') }}">
            <div
                class="inner_nav_menu {{ Str::startsWith(request()->path(), 'member/elections/ongoing') ? 'inner_nav_active' : '' }}">
                Ongoing
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
