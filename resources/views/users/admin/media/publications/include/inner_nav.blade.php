<div class="inner_nav_container">
    <div class="inner_nav">

        @if (Auth::user()->hasPermission('elections.read'))
            <a href="{{ route('mediaPublications') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/media/publications/home') ? 'inner_nav_active' : '' }}">
                    Publications 
                </div>
            </a>
        @endif
        @if (Auth::user()->hasPermission('elections.approve'))
            <a href="{{ route('mediaPublicationsPendingApproval') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/media/publications/approval') ? 'inner_nav_active' : '' }}">
                    Requests
                    @if (countMediaPublicationsPendingApproval() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ countMediaPublicationsPendingApproval() }}</span>
                    @endif
                </div>
            </a>
        @endif

        @if (countMyDraftedMediaPublications() > 0)
            <a href="{{ route('mediaPublicationsDraft') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/media/publications/draft') ? 'inner_nav_active' : '' }}">
                    Requests
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ countMyDraftedMediaPublications() }}</span>
                </div>
            </a>
        @endif


    </div>

</div>
