<div class="d-flex flex-wrap justify-content-center">
    <a href="{{ route('industryAffiliates') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
        title="Ecosystem & Industry Associates"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/academic_resources/industry_affiliates') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-briefcase"></i>
        </div>
    </a>
    @if (Auth::user()->hasPermission('archive.manage_tags'))
        <a href="{{ route('industryAffiliatesTags') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
            title="Tags"
            class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/academic_resources/industry_affiliate/tag') ? 'mini-menu-active' : '' }}">
            <div class="p-2" tabindex="0">
                <i class="fas fa-tags"></i>
            </div>
        </a>
    @endif

</div>
