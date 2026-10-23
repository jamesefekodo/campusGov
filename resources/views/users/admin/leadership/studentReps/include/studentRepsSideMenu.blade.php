<div class="d-flex flex-wrap justify-content-center">
    <a href="{{ route('studentLeadership') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
        title="Student Representatives"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/leadership/student_reps') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-user-tie"></i>
        </div>
    </a>

    <a href="{{ route('studentLeadershipTenures') }}" data-toggle="popover" data-trigger="focus" data-placement="top" title="Settings"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/leadership/student_rep/config') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-users-gear"></i>
        </div>
    </a>

</div>
