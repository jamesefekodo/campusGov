<div class="modal fade" id="viewElectionCandidate_{{ $candidate->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    {{ $candidate->theCandidate->memberPersonalProfile->first_name }}
                    {{ $candidate->theCandidate->memberPersonalProfile->last_name }} ({{ $candidate->position->position }} - Candidate)</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <div class="row col-md 12">
                        <div class="col-md-4">
                            @if (
                                $candidate->theCandidate->photo &&
                                    file_exists(public_path(
                                            'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo)))
                                <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo) }}"
                                    alt="{{ $candidate->theCandidate->username }}" class="img-fluid">
                            @else
                                <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture"
                                    class="img-fluid">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="p-1">
                                <span style="font-weight: bold">Name:</span>
                                <span class="text-black">
                                    {{ $candidate->theCandidate->memberPersonalProfile->first_name }}
                                    {{ $candidate->theCandidate->memberPersonalProfile->middle_name }}
                                    {{ $candidate->theCandidate->memberPersonalProfile->last_name }}
                                    <a href="{{ route('viewMember', ['uuid' => $candidate->theCandidate->uuid]) }}"
                                        class="badge bg-secondary text-white" title="View Initiator">
                                        <i class="fas fa-link"></i>
                                    </a>
                                </span>
                            </div>
                            <div class="p-1">
                                <span style="font-weight: bold">Program:</span>
                                <span class="text-black">
                                    {{ $candidate->program->name }}
                                    ({{ $candidate->programType->type_name }})
                                </span>
                            </div>
                            <div class="p-1">
                                <span style="font-weight: bold">Faculty:</span>
                                <span class="text-black">
                                    {{ $candidate->faculty->name }} @if ($candidate->faculty->acronym)
                                        ({{ $candidate->faculty->acronym }})
                                    @endif
                                </span>
                            </div>
                            <div class="p-1">
                                <span style="font-weight: bold">Department:</span>
                                <span class="text-black">
                                    {{ $candidate->department->name }}
                                </span>
                            </div>
                            <div class="p-1">
                                <span style="font-weight: bold">Level:</span>
                                <span class="text-black">
                                    {{ $candidate->level_at_application }}
                                </span>
                            </div>
                            <div class="p-1">
                                <span style="font-weight: bold">Position Aspired:</span>
                                <span class="text-black">
                                    {{ $candidate->position->position }}
                                </span>
                            </div>

                            <div class="p-1">
                                <span style="font-weight: bold">Description:</span>
                                <span class="text-black">
                                    {{ $candidate->position->description }}
                                </span>
                            </div>
                        </div>
                        <hr>
                        <div class="p-1 px-3">
                            <span style="font-weight: bold">Bio:</span>
                            <span class="text-black">
                                {!! $candidate->bio !!}
                            </span>
                        </div>
                        <div class="p-1 px-3">
                            <span style="font-weight: bold">Manifesto:</span>
                            <span class="text-black">
                                {!! $candidate->manifesto !!}
                            </span>
                        </div>
                    </div>

                    {{-- </div> --}}


                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>
