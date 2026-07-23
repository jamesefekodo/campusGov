<div class="modal fade" id="editProgramDurationOverrideModal_{{ $duration->uuid }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Program Duration Override</h4>
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
                        action="{{ route('editProgramDurationOverrideSubmit', ['uuid' => $duration->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="department_slug">Department:</label>
                                <select class="form-select" name="department_slug">
                                    <option value="" selected>- All -</option>
                                    @if ($data['activeDepartments']->isNotEmpty())
                                        @foreach ($data['activeDepartments'] as $department)
                                            <option value="{{ $department->slug }}"
                                                {{ $duration->department_slug == $department->slug ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p style="color: red">{{ $errors->first('department_slug') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="program_type_slug">Program Type:</label>
                                <select class="form-select" name="program_type_slug">
                                    <option value="" selected>- All -</option>
                                    @if ($data['program']->programTypes->isNotEmpty())
                                        @foreach ($data['program']->programTypes as $type)
                                            <option value="{{ $type->slug }}"
                                                {{ $duration->program_type_slug == $type->slug ? 'selected' : '' }}>
                                                {{ $type->type_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p style="color: red">{{ $errors->first('program_type_slug') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="number">Duration:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="duration"
                                    value="{{ $duration->duration }}" required>
                                <p style="color: red">{{ $errors->first('duration') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $duration->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $duration->is_active == '0' ? 'selected' : '' }}>
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
