<div class="modal fade" id="editSocialMediaHandleModal_{{ $handle->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Social Media Handle</h4>
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
                        action="{{ route('editSocialMediaHandleSubmit', ['id' => $handle->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="platform">Platform<span class="text-red">*</span></label>
                                <select class="form-select" name="platform" required>
                                    <option selected>
                                        -Select-</option>
                                    @if (!$data['recognizedSocialMediaPlatforms']->isEmpty())
                                        @foreach ($data['recognizedSocialMediaPlatforms'] as $platform)
                                            <option value="{{ $platform->slug }}"
                                                {{ $handle->platform == $platform->slug ? 'selected' : '' }}>
                                                {{ $platform->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="text-red">{{ $errors->first('platform') }}</p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="handle">Handle/Username <span class="text-red">*</span></label>
                                <input type="text" value="{{ $handle->handle }}" class="form-control" name="handle"
                                    required>
                                <p style="color: red">{{ $errors->first('handle') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="link">Link <span class="text-red">*</span></label>
                                <input type="url" value="{{ $handle->link }}" class="form-control" name="link"
                                    required>
                                <p style="color: red">{{ $errors->first('link') }}</p>
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
