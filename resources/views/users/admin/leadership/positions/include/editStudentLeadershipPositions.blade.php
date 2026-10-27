<div class="modal fade" id="editStudentLeadershipPosition_{{ $position->slug }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Position</h4>
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
                        action="{{ route('editStudentLeadershipPositionSubmit', ['slug' => $position->slug]) }}" 
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Name: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $position->name }}"
                                    required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">Description: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ $position->description }}" required>
                                <p style="color: red">{{ $errors->first('description') }}</p>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="arm_slug">Arm/Body<span class="text-red">*</span>:</label>
                                <select name="arm_slug" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                    @foreach ($data['arms'] as $arm)
                                        <option value="{{ $arm->slug }}"
                                            {{ $position->arm_slug == "$arm->slug" ? 'selected' : '' }}>
                                            {{ $arm->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p style="color: red">{{ $errors->first('arm_slug') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="sort_order">Sort Order: <span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="sort_order"
                                    value="{{ $position->sort_order }}" required>
                                <p style="color: red">{{ $errors->first('sort_order') }}</p>
                            </div>
                             <div class="form-group col-md-4">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $position->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $position->is_active == '0' ? 'selected' : '' }}>
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
