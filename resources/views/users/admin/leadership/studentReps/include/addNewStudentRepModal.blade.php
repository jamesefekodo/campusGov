<div class="modal fade" id="addNewStudentRepModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Student Representative</h4>
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
                        action="{{ route('addNewStudentLeadershipRepresentativeSubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="username">Username: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="username" value="{{ old('username') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('username') }}</p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="position_slug">Position<span class="text-red">*</span>:</label>
                                <select name="position_slug" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                    @foreach ($data['position'] as $position)
                                        <option value="{{ $position->slug }}"
                                            {{ old('position_slug') == "$position->slug" ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p style="color: red">{{ $errors->first('position_slug') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="tenure_slug">Academic Session<span class="text-red">*</span>:</label>
                                <select name="tenure_slug" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                    @foreach ($data['tenure'] as $tenure)
                                        <option value="{{ $tenure->slug }}"
                                            {{ old('tenure_slug') == "$tenure->slug" ? 'selected' : '' }}>
                                            {{ $tenure->academic_session }}/{{ $tenure->academic_session + 1 }}
                                            @if ($tenure->name && (int) $tenure->name !== (int) $tenure->academic_session)
                                                ({{ $tenure->name }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <p style="color: red">{{ $errors->first('tenure_slug') }}</p>
                            </div>
                            {{-- <div class="form-group col-md-6">
                                <label for="remarks">Remarks: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="remarks"
                                    value="{{ old('remarks') }}" required>
                                <p style="color: red">{{ $errors->first('remarks') }}</p>
                            </div> --}}
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
