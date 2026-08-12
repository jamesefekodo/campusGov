<div class="modal fade" id="addNewElectionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    New Election</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('addNewMisconductSubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="title">Title: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ old('title') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('title') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ old('description') }}" required>
                                <p style="color: red">{{ $errors->first('description') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="voting_method">Voting Method:<span class="text-red">*</span></label>
                                <select class="form-select" name="voting_method" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="standard" {{ old('voting_method') == 'standard' ? 'selected' : '' }}>
                                        Standard</option>
                                    <option value="monetary" {{ old('voting_method') == 'monetary' ? 'selected' : '' }}>
                                        Monetary (Public)</option>
                                </select>
                                <p style="color: red">
                                    {{ $errors->first('voting_method') }}</p>
                            </div>
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
