<div class="inner_nav_container">
    <div class="inner_nav">

        <a href="{{ route('memberViewElection', ['uuid' => $data['election']->uuid]) }}">
            <div class="inner_nav_menu {{ Route::is('memberViewElection') ? 'inner_nav_active' : '' }}">
                Election (Home)
            </div>
        </a>
        @if (Auth::user()->memberElectionApplications()->exists())
            <a href="{{ route('myElectionApplications', ['uuid' => $data['election']->uuid]) }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'member/elections/my_applications') ? 'inner_nav_active' : '' }}">
                    My Applications
                </div>
            </a>
        @endif
        <a href="{{ route('memberNewElections') }}">
            <div
                class="inner_nav_menu {{ Str::startsWith(request()->path(), 'member/elections/completed') ? 'inner_nav_active' : '' }}">
                Candidates
            </div>
        </a>
        <a href="{{ route('memberNewElections') }}">
            <div
                class="inner_nav_menu {{ Str::startsWith(request()->path(), 'member/elections/completed') ? 'inner_nav_active' : '' }}">
                Voting
            </div>
        </a>
        <a href="{{ route('memberNewElections') }}">
            <div
                class="inner_nav_menu {{ Str::startsWith(request()->path(), 'member/elections/completed') ? 'inner_nav_active' : '' }}">
                Results
            </div>
        </a>


    </div>

</div>
