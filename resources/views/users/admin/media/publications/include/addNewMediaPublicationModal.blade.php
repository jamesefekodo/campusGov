<div class="modal fade" id="addNewMediaPublicationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    New Publication</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('addNewElectionSubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="title">Title: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ old('title') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('title') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="academic_session">Admission Session<span class="text-red">*</span>:</label>
                                <select name="academic_session" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                    @php
                                        // $startYear = currentAcademicYear() - highestProgramDuration();
                                        $startYear = currentAcademicYear();
                                        $endYear = currentAcademicYear() + 1;
                                    @endphp

                                    @for ($year = $endYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}"
                                            {{ old('academic_session', currentAcademicYear()) == $year ? 'selected' : '' }}>
                                            {{ $year }}/{{ $year + 1 }}
                                        </option>
                                    @endfor
                                </select>
                                <p style="color: red">{{ $errors->first('academic_session') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ old('description') }}" required>
                                <p style="color: red">{{ $errors->first('description') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="voting_method">
                                    Voting Method:<span class="text-red">*</span>
                                </label>

                                <select class="form-select" name="voting_method" id="voting_method" required>
                                    <option value="" selected disabled>-Select-</option>

                                    <option value="standard"
                                        {{ old('voting_method') == 'standard' ? 'selected' : '' }}>
                                        Standard
                                    </option>

                                    <option value="monetary"
                                        {{ old('voting_method') == 'monetary' ? 'selected' : '' }}>
                                        Monetary
                                    </option>
                                </select>

                                <p style="color: red">
                                    {{ $errors->first('voting_method') }}
                                </p>
                            </div>


                            <div class="form-group col-md-6" id="cost_per_vote_container"
                                style="{{ old('voting_method') == 'monetary' ? '' : 'display: none;' }}">

                                <label for="cost_per_vote">
                                    Cost Per Vote:<span class="text-red">*</span>
                                </label>

                                <input type="number" step="0.01" min="0" class="form-control"
                                    name="cost_per_vote" id="cost_per_vote" value="{{ old('cost_per_vote') }}"
                                    {{ old('voting_method') == 'monetary' ? 'required' : '' }}>

                                <p style="color: red">
                                    {{ $errors->first('cost_per_vote') }}
                                </p>
                            </div>


                            <script>
                                document.addEventListener('DOMContentLoaded', function() {

                                    const votingMethod = document.getElementById('voting_method');
                                    const costContainer = document.getElementById('cost_per_vote_container');
                                    const costInput = document.getElementById('cost_per_vote');

                                    function toggleCostPerVote() {

                                        if (votingMethod.value === 'monetary') {

                                            costContainer.style.display = 'block';
                                            costInput.required = true;

                                        } else {

                                            costContainer.style.display = 'none';
                                            costInput.required = false;
                                            costInput.value = '';
                                        }
                                    }

                                    votingMethod.addEventListener('change', toggleCostPerVote);

                                    // Handle old input after validation failure
                                    toggleCostPerVote();
                                });
                            </script>
                            <div class="form-group col-md-6">
                                <label for="public_visibility">Public Visibility:<span class="text-red">*</span></label>
                                <select class="form-select" name="public_visibility" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value=0 {{ old('public_visibility') == '0' ? 'selected' : '' }}>
                                        No</option>
                                    <option value=1 {{ old('public_visibility') == '1' ? 'selected' : '' }}>
                                        Yes</option>
                                </select>
                                <p style="color: red">
                                    {{ $errors->first('public_visibility') }}</p>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <hr class="flex-grow-1">
                                <span class="mx-2 text-blue">Form Sales Period</span>
                                <hr class="flex-grow-1">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="form_sales_starts">Form Sales Starts<span class="text-red">*</span></label>
                                <input type="datetime-local" class="form-control" name="form_sales_starts"
                                    value="{{ old('form_sales_starts') }}" required>
                                <p style="color: red">{{ $errors->first('form_sales_starts') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="form_sales_ends">Form Sales Ends<span class="text-red">*</span></label>
                                <input type="datetime-local" class="form-control" name="form_sales_ends"
                                    value="{{ old('form_sales_ends') }}" required>
                                <p style="color: red">{{ $errors->first('form_sales_ends') }}</p>
                            </div>

                            <div class="d-flex align-items-center mt-2">
                                <hr class="flex-grow-1">
                                <span class="mx-2 text-blue">Screening Period</span>
                                <hr class="flex-grow-1">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="screening_starts">Screening Starts</label>
                                <input type="datetime-local" class="form-control" name="screening_starts"
                                    value="{{ old('screening_starts') }}">
                                <p style="color: red">{{ $errors->first('screening_starts') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="screening_ends">Screening Ends</label>
                                <input type="datetime-local" class="form-control" name="screening_ends"
                                    value="{{ old('screening_ends') }}">
                                <p style="color: red">{{ $errors->first('screening_ends') }}</p>
                            </div>

                            <div class="d-flex align-items-center mt-2">
                                <hr class="flex-grow-1">
                                <span class="mx-2 text-blue">Screening Period</span>
                                <hr class="flex-grow-1">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="campaign_starts">Screening Starts</label>
                                <input type="datetime-local" class="form-control" name="campaign_starts"
                                    value="{{ old('campaign_starts') }}">
                                <p style="color: red">{{ $errors->first('campaign_starts') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="campaign_ends">Screening Ends</label>
                                <input type="datetime-local" class="form-control" name="campaign_ends"
                                    value="{{ old('campaign_ends') }}">
                                <p style="color: red">{{ $errors->first('campaign_ends') }}</p>
                            </div>

                            <div class="d-flex align-items-center">
                                <hr class="flex-grow-1">
                                <span class="mx-2 text-blue">Voting Period</span>
                                <hr class="flex-grow-1">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="voting_starts">Voting Starts<span class="text-red">*</span></label>
                                <input type="datetime-local" class="form-control" name="voting_starts"
                                    value="{{ old('voting_starts') }}" required>
                                <p style="color: red">{{ $errors->first('voting_starts') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="voting_ends">Voting Ends<span class="text-red">*</span></label>
                                <input type="datetime-local" class="form-control" name="voting_ends"
                                    value="{{ old('voting_ends') }}" required>
                                <p style="color: red">{{ $errors->first('voting_ends') }}</p>
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
