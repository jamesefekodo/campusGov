<div class="modal fade" id="addNewProgramModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Academic Program</h4>
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
                    action="{{ route('addAcademicProgramSubmit') }}"
                    enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="name">Name:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="short_name">Short Name:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="short_name"
                                    value="{{ old('short_name') }}" required>
                                <p style="color: red">{{ $errors->first('short_name') }}</p>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="duration">Duration (Years):<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="duration" value="{{ old('duration') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('duration') }}</p>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="level_format">Level Format<span class="text-red">*</span></label>
                                <select class="form-select" name="level_format" required>
                                    <option value="" disabled @selected(old('level_format') == '')>
                                        -Select-
                                    </option>

                                    <option value="ND {value}" @selected(old('level_format') == 'ND {value}')>
                                        ND {value}
                                    </option>

                                    <option value="HND {value}" @selected(old('level_format') == 'HND {value}')>
                                        HND {value}
                                    </option>

                                    <option value="NCE {value}" @selected(old('level_format') == 'NCE {value}')>
                                        NCE {value}
                                    </option>

                                    <option value="PGD {value}" @selected(old('level_format') == 'PGD {value}')>
                                        PGD {value}
                                    </option>

                                    <option value="MSc {value}" @selected(old('level_format') == 'MSc {value}')>
                                        MSc {value}
                                    </option>

                                    <option value="{value}L" @selected(old('level_format') == '{value}L')>
                                        {value}L
                                    </option>

                                    <option value="L{value}" @selected(old('level_format') == 'L{value}')>
                                        L{value}
                                    </option>

                                    <option value="Level {value}" @selected(old('level_format') == 'Level {value}')>
                                        Level {value}
                                    </option>

                                    <option value="Year {value}" @selected(old('level_format') == 'Year {value}')>
                                        Year {value}
                                    </option>
                                </select>
                                <p style="color: red">{{ $errors->first('level_format') }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="numbering">Numbering<span class="text-red">*</span></label>
                                <select class="form-select" name="numbering" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="arabic" {{ old('numbering') == 'arabic' ? 'selected' : '' }}>
                                        Arabic (1,2,3)
                                    </option>
                                    <option value="roman" {{ old('numbering') == 'roman' ? 'selected' : '' }}>
                                        Roman (I, II, III)
                                    </option>
                                    <option value="weighted" {{ old('numbering') == 'weighted' ? 'selected' : '' }}>
                                        Roman (100, 200, 300)
                                    </option>
                                </select>
                                <p style="color: red">{{ $errors->first('numbering') }}</p>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="increment">Increment:<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="increment"
                                    value="{{ old('increment') }}" required>
                                <p style="color: red">{{ $errors->first('increment') }}</p>
                            </div>


                            <div class="form-group col-md-3">
                                <label for="award">Award (B.Sc., HND):<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="award" value="{{ old('award') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('award') }}</p>
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
