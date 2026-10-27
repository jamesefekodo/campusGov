<div class="inner_nav_container">
    <div class="inner_nav">
        @if (Auth::user()->hasPermission('basic_system_config.read'))
            <a href="{{ route('configBasicSettings') }}">
                <div
                    class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/config/basics/basics') ? 'inner_nav_active' : '' }}">
                    Basic</div>
            </a>

            <a href="{{ route('homepageSliders') }}">
                <div
                    class="inner_nav_menu
                {{ Str::startsWith(request()->path(), 'admin/config/basics/sliders') ? 'inner_nav_active' : '' }}">
                    Slider</div>
            </a>
        @endif

    </div>

</div>
