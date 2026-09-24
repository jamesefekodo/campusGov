<div class="d-flex flex-wrap justify-content-center">
    <a href="{{ route('mediaPublications') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
        title="Publications"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/media/publications') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-globe"></i>
        </div>
    </a>
    @if (Auth::user()->hasPermission('media_publication.manage_tags'))
        <a href="{{ route('mediaPublicationTags') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
            title="Tags"
            class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/media/publication/tag') ? 'mini-menu-active' : '' }}">
            <div class="p-2" tabindex="0">
                <i class="fas fa-tags"></i>
            </div>
        </a>
    @endif

</div>
