<div class="inner_nav_container">
    <div class="inner_nav">
        <a href="{{ route('studentMembers') }}">
            <div
                class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/user_management/members/students') ? 'inner_nav_active' : '' }}">
                Students</div>
        </a>

        @if (Auth::user()->hasPermission('verify_users'))
            <a href="{{ route('memberAwaitingVerification') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/user_management/members/verification') ? 'inner_nav_active' : '' }}">
                    Verification
                    @if (countMemberProfilesAwaitingVerification() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ countMemberProfilesAwaitingVerification() }}</span>
                    @endif
                </div>
            </a>
        @endif


    </div>

</div>
