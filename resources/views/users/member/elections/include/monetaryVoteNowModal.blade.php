<div class="modal fade" id="monetaryVoteNowModal_{{ $candidate->id }}" tabindex="-1" role="dialog"
    aria-labelledby="monetaryVoteNowModalLabel_{{ $candidate->id }}" tabindex="-1" role="dialog" aria-hidden="true">
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
                        action="{{ route('memberSubmitMonetaryVote', ['election' => $data['election']->uuid, 'position' => $data['position']->uuid, 'candidate' => $candidate->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="row align-items-center g-4 mb-4">

                                {{-- Candidate Photo --}}
                                <div class="col-md-4 text-center">
                                    @if (
                                        $candidate->theCandidate->photo &&
                                            file_exists(public_path(
                                                    'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo)))
                                        <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $candidate->theCandidate->photo) }}"
                                            alt="{{ $candidate->theCandidate->username }}"
                                            class="img-fluid rounded-circle shadow"
                                            style="width:160px;height:160px;object-fit:cover;border:4px solid #e5e7eb;">
                                    @else
                                        <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Candidate"
                                            class="img-fluid rounded-circle shadow"
                                            style="width:160px;height:160px;object-fit:cover;border:4px solid #e5e7eb;">
                                    @endif
                                    <h5 class="mt-3 mb-0 fw-bold">
                                        {{ $candidate->theCandidate->memberPersonalProfile->first_name }}
                                        {{ $candidate->theCandidate->memberPersonalProfile->middle_name }}
                                        {{ $candidate->theCandidate->memberPersonalProfile->last_name }}
                                    </h5>
                                    <span class="badge bg-primary mt-1">{{ $data['position']->position }}</span>
                                </div>

                                {{-- Vote Info --}}
                                <div class="col-md-8">
                                    <div class="card border-0 bg-light rounded-3 p-4">

                                        <h4 class="fw-bold mb-1">Cast your vote</h4>
                                        <p class="text-muted mb-3" style="font-size:14px;">
                                            You are voting for
                                            <strong>{{ $candidate->theCandidate->memberPersonalProfile->first_name }}
                                                {{ $candidate->theCandidate->memberPersonalProfile->last_name }}</strong>
                                            as <strong>{{ $data['position']->position }}</strong>
                                        </p>

                                        <div class="alert alert-info py-2 mb-3 d-flex align-items-center gap-2">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Each vote costs
                                                <strong>₦{{ number_format($data['election']->cost_per_vote, 2) }}</strong></span>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="no_of_votes_{{ $candidate->id }}"
                                                class="form-label fw-semibold">
                                                Number of Votes <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" id="no_of_votes_{{ $candidate->id }}"
                                                name="no_of_votes" class="form-control form-control-lg vote-input"
                                                data-candidate="{{ $candidate->id }}"
                                                data-cost="{{ $data['election']->cost_per_vote }}"
                                                value="{{ old('no_of_votes') }}" min="1"
                                                placeholder="Enter number of votes" required>
                                            <p class="text-danger small mt-1">{{ $errors->first('no_of_votes') }}</p>
                                        </div>

                                        @if (!Auth::user())
                                            <div class="row col-md-12">
                                                <div class="form-group mb-3 col-md-12">
                                                    <label for="voters_name_{{ $candidate->id }}"
                                                        class="form-label fw-semibold">
                                                        Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" id="voters_name_{{ $candidate->id }}"
                                                        name="voters_name"
                                                        class="form-control form-control-lg vote-input"
                                                        value="{{ old('voters_name') }}" placeholder="Your Name"
                                                        required>
                                                    <p class="text-danger small mt-1">
                                                        {{ $errors->first('voters_name') }}
                                                    </p>
                                                </div>
                                                <div class="form-group mb-3 col-md-6">
                                                    <label for="phone_{{ $candidate->id }}"
                                                        class="form-label fw-semibold">
                                                        Phone <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" id="phone_{{ $candidate->id }}"
                                                        name="phone"
                                                        class="form-control form-control-lg vote-input"
                                                        value="{{ old('phone') }}" placeholder="Phone No" required>
                                                    <p class="text-danger small mt-1">{{ $errors->first('phone') }}
                                                    </p>
                                                </div>
                                                <div class="form-group mb-3 col-md-6">
                                                    <label for="email_{{ $candidate->id }}"
                                                        class="form-label fw-semibold">
                                                        Email <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" id="email_{{ $candidate->id }}"
                                                        name="email"
                                                        class="form-control form-control-lg vote-input"
                                                        value="{{ old('email') }}" placeholder="Phone No" required>
                                                    <p class="text-danger small mt-1">{{ $errors->first('email') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Dynamic summary --}}
                                        <div id="vote-summary-{{ $candidate->id }}"
                                            class="card border-primary bg-primary bg-opacity-10 p-3 rounded-3"
                                            style="display:none!important">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted">Votes</span>
                                                <span id="vote-count-{{ $candidate->id }}" class="fw-bold">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="text-muted">Cost per vote</span>
                                                <span>₦{{ number_format($data['election']->cost_per_vote, 2) }}</span>
                                            </div>
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold fs-5">Total Amount</span>
                                                <span class="fw-bold fs-4 text-primary"
                                                    id="vote-total-{{ $candidate->id }}">₦0.00</span>
                                            </div>
                                        </div>

                                        {{-- ✅ THIS WAS MISSING — total pay banner --}}
                                        <div id="vote-pay-banner-{{ $candidate->id }}"
                                            class="alert alert-success d-flex justify-content-between align-items-center mt-3 mb-0"
                                            style="display:none!important">
                                            <span class="fw-semibold">
                                                <i class="fas fa-wallet me-2"></i>You will be charged:
                                            </span>
                                            <span class="fw-bold fs-4"
                                                id="vote-pay-total-{{ $candidate->id }}">₦0.00</span>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <input type="hidden" name="mode" value="electronic">

                            {{-- Payment Methods --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Electronic Payment Method</label>
                                <div class="row">
                                    @foreach (electronicsPaymentMethods() as $method)
                                        <div class="col-lg-4 col-md-6 mb-3 electronic-payment-option">
                                            <input type="radio"
                                                id="electronic-payment-{{ $candidate->id }}-{{ $method->slug }}"
                                                name="electronic_payment_method" value="{{ $method->slug }}"
                                                required>
                                            <label for="electronic-payment-{{ $candidate->id }}-{{ $method->slug }}">
                                                <div class="electronic-payment-logo">
                                                    @if ($method->logo)
                                                        <img src="{{ asset('system/images/paymentMethods/logo/' . $method->logo) }}"
                                                            class="img-fluid" alt="{{ $method->name }}">
                                                    @else
                                                        <i class="fas fa-credit-card fa-3x"></i>
                                                    @endif
                                                </div>
                                                <h5 class="mt-3">{{ $method->name }}</h5>
                                                <p>Instant confirmation after successful payment.</p>
                                                <span class="electronic-payment-selected">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                        <hr>

                        <button type="submit" class="green-button submit-btn">
                            <span class="btn-text">
                                Proceed</span>

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
