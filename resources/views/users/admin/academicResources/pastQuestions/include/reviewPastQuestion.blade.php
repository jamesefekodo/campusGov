<div class="modal fade" id="reviewPastQuestion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Review Past Question</h4>
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
                        action="{{ route('reviewPastQuestionSubmit', ['uuid' => $data['pastQuestion']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="approval_status">Approval Status:<span class="text-red">*</span></label>
                                <select class="form-select" name="approval_status" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    @if ($data['pastQuestion']->approval_status == 'approved')
                                        {{-- approved --}}
                                        <option value="declined"
                                            {{ $data['pastQuestion']->approval_status == 'declined' ? 'selected' : '' }}>
                                            Retract/Declined</option>
                                    @elseif ($data['pastQuestion']->approval_status == 'declined')
                                        <option value="approved"
                                            {{ $data['pastQuestion']->approval_status == 'approved' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="declined"
                                            {{ $data['pastQuestion']->approval_status == 'declined' ? 'selected' : '' }}>
                                            Declined</option>
                                    @else
                                        {{-- pending --}}
                                        <option value="pending"
                                            {{ $data['pastQuestion']->approval_status == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="approved"
                                            {{ $data['pastQuestion']->approval_status == 'approved' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="declined"
                                            {{ $data['pastQuestion']->approval_status == 'declined' ? 'selected' : '' }}>
                                            Declined</option>
                                    @endif
                                </select>
                                <p style="color: red">
                                    {{ $errors->first('approval_status') }}</p>
                            </div>


                        </div>
                        <hr>

                        <button type="submit" class="green-button submit-btn">
                            <span class="btn-text">
                                Submit
                            </span>

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
