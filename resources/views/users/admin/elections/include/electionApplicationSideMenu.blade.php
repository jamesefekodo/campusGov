<div class="d-flex flex-wrap justify-content-center">
    <a href="{{ route('electionOperationRequests') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
        title="Pending Approval"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/election/requests') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
    </a>


    <a href="{{ route('processedElectionOperationRequests') }}" data-toggle="popover" data-trigger="focus" data-placement="top"
        title="Processed"
        class="mini-menu  {{ Str::startsWith(request()->path(), 'admin/election/request/processed') ? 'mini-menu-active' : '' }}">
        <div class="p-2" tabindex="0">
            <i class="fas fa-mobile-screen-button"></i>
        </div>
    </a>

</div>
