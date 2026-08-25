<div class="inner_nav_container">
    <div class="inner_nav">

        @if (Auth::user()->hasPermission('elections.read'))
            <a href="{{ route('viewElection', ['uuid' => $data['election']->uuid]) }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/elections/view') ? 'inner_nav_active' : '' }}">
                    Election (Basic Info)
                </div>
            </a>
        @endif

        @if (Auth::user()->hasPermission('elections.approve'))
            <a href="{{ route('electionOperationRequests') }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/election/requests/prospective_candidates') ? 'inner_nav_active' : '' }}">
                    Applications
                    @if (totalElectionCandidatesAwaitingApproval() > 0)
                        &nbsp;
                        <span class="position-relative translate-middle badge rounded-pill bg-red ml-3 ms-2"
                            style="font-size: 11px">{{ totalElectionCandidatesAwaitingApproval() }}</span>
                    @endif
                </div>
            </a>
        @endif

        <a href="{{ route('electionCandidates', ['uuid' => $data['election']->uuid]) }}">
            <div
                class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/election/requests/candidates') ? 'inner_nav_active' : '' }}">
                Candidates
            </div>
        </a>

        @if (Auth::user()->hasPermission('elections.results') && in_array($data['election']->status, ['voting', 'completed']))
            <a href="{{ route('electionResults', ['uuid' => $data['election']->uuid]) }}">
                <div
                    class="inner_nav_menu {{ Str::startsWith(request()->path(), 'admin/election/results') ? 'inner_nav_active' : '' }}">
                    Results
                </div>
            </a>
        @endif


    </div>

</div>
