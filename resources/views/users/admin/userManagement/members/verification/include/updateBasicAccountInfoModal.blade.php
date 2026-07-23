<div class="modal fade" id="basicAccountInfoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Update Basic Account Info</h4>
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
                        action="{{ route('updateBasicAccountInfo', ['uuid' => $data['member']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="username">Username<span class="text-red">*</span>:</label>
                                <input type="text" class="form-control" name="username"
                                    value="{{ $data['member']->username }}" readonly>
                                <p style="color: red">{{ $errors->first('username') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">Email<span class="text-red">*</span>:</label>
                                <input type="email" class="form-control" name="email"
                                    value="{{ $data['member']->email }}" required>
                                <p style="color: red">{{ $errors->first('email') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="phone">Phone<span class="text-red">*</span>:</label>
                                <input type="text" class="form-control" name="phone"
                                    value="{{ $data['member']->phone }}" required>
                                <p style="color: red">{{ $errors->first('phone') }}</p>
                            </div>

                        </div>
                        <hr>

                        <button type="submit" class="green-button submit-btn">
                            <span class="btn-text">
                                Update
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
