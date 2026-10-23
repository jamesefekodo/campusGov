<div class="inner_nav_container">
    <div class="inner_nav">
        <a href="{{ route('studentLeadershipTenures') }}">
            <div
                class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/leadership/student_rep/config/tenures') ? 'inner_nav_active' : '' }}">
                Tenures</div>
        </a>
        <a {{-- href="{{ route('pastQuestionsApproval') }}" --}}>
            <div
                class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/academic_resources/past_questions/approval') ? 'inner_nav_active' : '' }}">
                Position
            </div>
        </a>
        <a href="{{ route('studentLeadershipBodies') }}">
            <div
                class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/leadership/student_rep/config/arms') ? 'inner_nav_active' : '' }}">
                Arms/Bodies
            </div>
        </a>

    </div>

</div>
