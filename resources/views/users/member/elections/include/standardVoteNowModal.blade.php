<div class="modal fade" id="standardVoteNowModal_{{ $candidate->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    {{ $data['election']->title }}
                    {{ $data['election']->academic_session }}/{{ $data['election']->academic_session + 1 }}</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST"
                        action="{{ route('memberSubmitStandardVote', ['election' => $data['election']->uuid, 'position' => $data['position']->uuid, 'candidate' => $candidate->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="row p-3 align-items-center text-center">
                                <div class="col-md-6 d-flex justify-content-center">
                                    @if (
                                        $candidate->theCandidate->photo &&
                                            file_exists(public_path(
                                                    'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo)))
                                        <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo) }}"
                                            alt="{{ $candidate->theCandidate->username }}" class="img-fluid">
                                    @else
                                        <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Candidate"
                                            class="img-fluid">
                                    @endif
                                </div>
                                <div class="col-md-6 d-flex justify-content-center align-items-center">
                                    <p class="m-0 fs-1">
                                        Vote
                                        <span class="fw-bold text-black">
                                            {{ $candidate->theCandidate->memberPersonalProfile->first_name }}
                                            {{ $candidate->theCandidate->memberPersonalProfile->middle_name }}
                                            {{ $candidate->theCandidate->memberPersonalProfile->last_name }}</span>
                                        as <span class="text-black fw-bold">{{ $data['position']->position }}?</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <button type="submit" class="green-button submit-btn">
                            <span class="btn-text">
                                Yes, Proceed! </span>

                            <span class="btn-loading" style="display:none;">
                                <i class="fas fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                    </form>
                    {{-- </div> --}}


                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>
