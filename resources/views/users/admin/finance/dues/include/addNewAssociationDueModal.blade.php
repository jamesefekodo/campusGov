<div class="modal fade" id="addNewAssociationDueModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Association Due</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('addNewAssociationDueSubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="title">Name: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ old('title') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('title') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description:</label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ old('description') }}">
                                <p style="color: red">
                                    {{ $errors->first('description') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount">Amount (₦)<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="amount" value="{{ old('amount') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('amount') }}</p>
                            </div>


                            <div class="form-group col-md-6">
                                <label for="academic_year">Academic Year: <span class="text-red">*</span></label>
                                <select name="academic_year" class="form-control" required>
                                    <option value="" disabled selected>Select</option>
                                    @php
                                        $startYear = schoolFoundingYear();
                                        $endYear = currentAcademicYear();
                                    @endphp

                                    @for ($year = $endYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}"
                                            {{ old('academic_year') == "$year" ? 'selected' : '' }}>
                                            {{ $year }}/{{ $year + 1 }}
                                        </option>
                                    @endfor
                                </select>
                                <p style="color: red">{{ $errors->first('academic_year') }}</p>
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
