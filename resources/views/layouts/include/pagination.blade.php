
<div class="pagination_container w-100 my-5">
    <span class="pagination-count">
        {{ $paginatedArray->firstItem() }}
       to
        {{ $paginatedArray->lastItem() }}
        of
        {{ $paginatedArray->total() }}
    </span>
    <ul class="pagination-links">
        {{-- Previous Page Link --}}
        @if ($paginatedArray->currentPage() != 1)
            <li>
                <a href="{{ $paginatedArray->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo; Previous</span>
                </a>
            </li>
        @endif

        {{-- Pagination Range --}}
        @php
            $totalPages = $paginatedArray->lastPage();
            $currentPage = $paginatedArray->currentPage();
            $range = 2; // Adjust how many pages around current page you want to show
        @endphp

        {{-- Show first page if not near the beginning --}}
        @if ($currentPage > $range + 1)
            <li><a href="{{ $paginatedArray->url(1) }}">1</a></li>
            @if ($currentPage > $range + 2)
                <li class="disabled"><span>...</span></li> {{-- Ellipsis before current range --}}
            @endif
        @endif

        {{-- Page links within range --}}
        @for ($page = max(1, $currentPage - $range); $page <= min($totalPages, $currentPage + $range); $page++)
            <li class="{{ $page == $currentPage ? 'active' : '' }}">
                <a href="{{ $paginatedArray->url($page) }}">{{ $page }}</a>
            </li>
        @endfor

        {{-- Show last page if not near the end --}}
        @if ($currentPage < $totalPages - $range)
            @if ($currentPage < $totalPages - $range - 1)
                <li class="disabled"><span>...</span></li> {{-- Ellipsis after current range --}}
            @endif
            <li><a href="{{ $paginatedArray->url($totalPages) }}">{{ $totalPages }}</a></li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginatedArray->currentPage() != $totalPages)
            <li>
                <a href="{{ $paginatedArray->nextPageUrl() }}" aria-label="Next">
                    <span aria-hidden="true">Next &raquo;</span>
                </a>
            </li>
        @endif
    </ul>
</div>
