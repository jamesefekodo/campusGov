<div class="modal fade" id="editProgramModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Academic Program</h4>
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
                        action="{{ route('updateAcademicProgramSubmit', ['uuid' => $data['program']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Name:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ $data['program']->name }}" required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="short_name">Short Name:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="short_name"
                                    value="{{ $data['program']->short_name }}" required>
                                <p style="color: red">{{ $errors->first('short_name') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="duration">Duration (Years):<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="duration"
                                    value="{{ $data['program']->duration }}" required>
                                <p style="color: red">{{ $errors->first('duration') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="level_format">Level Format<span class="text-red">*</span></label>
                                <select class="form-select" name="level_format" required>
                                    <option value="" disabled @selected($data['program']->level_format == '')>
                                        -Select-
                                    </option>

                                    <option value="ND {value}" @selected($data['program']->level_format == 'ND {value}')>
                                        ND {value}
                                    </option>

                                    <option value="HND {value}" @selected($data['program']->level_format == 'HND {value}')>
                                        HND {value}
                                    </option>

                                    <option value="NCE {value}" @selected($data['program']->level_format == 'NCE {value}')>
                                        NCE {value}
                                    </option>

                                    <option value="PGD {value}" @selected($data['program']->level_format == 'PGD {value}')>
                                        PGD {value}
                                    </option>

                                    <option value="MSc {value}" @selected($data['program']->level_format == 'MSc {value}')>
                                        MSc {value}
                                    </option>

                                    <option value="{value}L" @selected($data['program']->level_format == '{value}L')>
                                        {value}L
                                    </option>

                                    <option value="L{value}" @selected($data['program']->level_format == 'L{value}')>
                                        L{value}
                                    </option>

                                    <option value="Level {value}" @selected($data['program']->level_format == 'Level {value}')>
                                        Level {value}
                                    </option>

                                    <option value="Year {value}" @selected($data['program']->level_format == 'Year {value}')>
                                        Year {value}
                                    </option>
                                </select>
                                <p style="color: red">{{ $errors->first('level_format') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="numbering">Numbering<span class="text-red">*</span></label>
                                <select class="form-select" name="numbering" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="arabic"
                                        {{ $data['program']->numbering == 'arabic' ? 'selected' : '' }}>
                                        Arabic (1,2,3)
                                    </option>
                                    <option value="roman"
                                        {{ $data['program']->numbering == 'roman' ? 'selected' : '' }}>
                                        Roman (I, II, III)
                                    </option>
                                    <option value="weighted"
                                        {{ $data['program']->numbering == 'weighted' ? 'selected' : '' }}>
                                        Roman (100, 200, 300)
                                    </option>
                                </select>
                                <p style="color: red">{{ $errors->first('numbering') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="increment">Increment:<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="increment"
                                    value="{{ $data['program']->increment }}" required>
                                <p style="color: red">{{ $errors->first('increment') }}</p>
                            </div>


                            <div class="form-group col-md-6">
                                <label for="award">Award (B.Sc., HND):<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="award"
                                    value="{{ $data['program']->award }}" required>
                                <p style="color: red">{{ $errors->first('award') }}</p>
                            </div>
                             <div class="form-group col-md-6">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $data['program']->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $data['program']->is_active == '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                <p style="color: red">{{ $errors->first('is_active') }}</p>
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
