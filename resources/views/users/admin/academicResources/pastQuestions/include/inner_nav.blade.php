<div class="inner_nav_container">
    <div class="inner_nav">
        @if (Auth::user()->hasPermission('past_questions.read'))
            <a href="{{ route('pastQuestionsHome') }}">
                <div
                    class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/academic_resources/past_questions/home') ? 'inner_nav_active' : '' }}">
                    Past Questions</div>
            </a>
        @endif
        {{-- countPastQuestionsPendingApproval --}}
        @if (Auth::user()->hasPermission('past_questions.update'))
            <a href="{{ route('pastQuestionsApproval') }}">
                <div
                    class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/academic_resources/past_questions/approval') ? 'inner_nav_active' : '' }}">
                    Approval @if (countPastQuestionsPendingApproval() > 0)
                        &nbsp;
                        <span class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                            style="font-size: 11px">{{ countPastQuestionsPendingApproval() }}
                    @endif
                </div>
            </a>
        @endif

    </div>

</div>
