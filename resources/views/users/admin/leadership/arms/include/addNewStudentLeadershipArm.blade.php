<div class="modal fade" id="addNewStudentLeadershipArm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Add New Arm/Body</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('addNewStudentLeadershipArmSubmit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="name">Name: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>
                              <div class="form-group col-md-4">
                                <label for="sort_order">Sort Order: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="sort_order" value="{{ old('sort_order') }}"
                                    required>
                                <p style="color: red">{{ $errors->first('sort_order') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ old('description') }}" required>
                                <p style="color: red">{{ $errors->first('description') }}</p>
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
