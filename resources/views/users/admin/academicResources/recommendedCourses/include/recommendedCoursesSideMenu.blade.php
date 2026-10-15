<div class="d-flex flex-wrap justify-content-center">
    <a href="{{ route('recommendedCourses') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
        title="Courses"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/academic_resources/recommended_courses') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-lightbulb"></i>
        </div>
    </a>

    @if (Auth::user()->hasPermission('recommended_course.manage_tags'))
        <a href="{{ route('recommendedCourseTags') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
            title="Tags"
            class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/academic_resources/recommended_course/tag') ? 'mini-menu-active' : '' }}">
            <div class="p-2" tabindex="0">
                <i class="fas fa-tags"></i>
            </div>
        </a>
    @endif

</div>
