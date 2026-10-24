<div class="modal fade" id="editAssociationAdvisor_{{ $advisor->uuid }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Update Advisor</h4>
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
                        action="{{ route('editStudentLeadershipAdvisorSubmit', ['uuid' => $advisor->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="advisor-upload-container">
                            <div class="row d-flex justify-content-center align-items-center">
                                @php
                                    $thePhoto = asset('system/images/default_profile_pic.webp');

                                    if (!empty($advisor->photo)) {
                                        $photoPath =
                                            'assets/tenant_' .
                                            tenant()->id .
                                            '/uploads/leadership/advisor/' .
                                            $advisor->photo;

                                        if (file_exists(public_path($photoPath))) {
                                            $thePhoto = asset($photoPath);
                                        }
                                    }
                                @endphp
                                <div class="col-md-6 text-center">
                                    <label class="advisor-preview-frame"
                                        for="advisor-photo-{{ $loop->index ?? 'new' }}">
                                        <img class="advisor-preview-image" src="{{ $thePhoto }}"
                                            alt="Advisor passport">

                                        <div class="advisor-preview-overlay">
                                            <i class="fas fa-upload"></i>
                                        </div>
                                    </label>
                                </div>

                                <div class="col-md-6">
                                    <label class="btn btn-outline-primary advisor-upload-button"
                                        for="advisor-photo-{{ $loop->index ?? 'new' }}">
                                        <i class="fas fa-upload me-1"></i>
                                        Choose Photo
                                    </label>

                                    <input type="file" id="advisor-photo-{{ $loop->index ?? 'new' }}"
                                        class="advisor-photo-input" accept=".jpg, .jpeg, .png, .webp" name="photo">

                                    <p class="text-red mt-2 mb-0">
                                        {{ $errors->first('photo') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="name">Name: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $advisor->name }}"
                                    required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="position">Position: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="position"
                                    value="{{ $advisor->position }}" required>
                                <p style="color: red">{{ $errors->first('position') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="start_date">
                                    Start Month & Year: <span class="text-red">*</span>
                                </label>
                                <input type="month" class="form-control" id="start_date" name="start_date"
                                    value="{{ $advisor->start_date?->format('Y-m') }}" required>
                                <p style="color: red">{{ $errors->first('start_date') }}</p>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="end_date">End Month & Year:</label>
                                <input type="month" class="form-control" id="end_date" name="end_date"
                                    value="{{ $advisor->end_date?->format('Y-m') }}">
                                <p style="color: red">{{ $errors->first('end_date') }}</p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="remarks">Remarks:</label>
                                <input type="text" class="form-control" name="remarks"
                                    value="{{ $advisor->remarks }}">
                                <p style="color: red">{{ $errors->first('remarks') }}</p>
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
