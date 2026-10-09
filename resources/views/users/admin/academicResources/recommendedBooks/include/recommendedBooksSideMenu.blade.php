<div class="d-flex flex-wrap justify-content-center">
    <a href="{{ route('recommendedBooks') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
        title="Books"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/academic_resources/recommended_books') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-book"></i>
        </div>
    </a>
    @if (Auth::user()->hasPermission('recommended_book.manage_tags'))
        <a href="{{ route('recommendedBooksTags') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
            title="Tags"
            class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/academic_resources/recommended_book/tag') ? 'mini-menu-active' : '' }}">
            <div class="p-2" tabindex="0">
                <i class="fas fa-tags"></i>
            </div>
        </a>
    @endif

</div>
