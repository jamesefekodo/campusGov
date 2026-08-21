<div class="modal fade" id="processElectionCandidateRequestModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Process Candidate Request</h4>
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
                    action="{{ route('processElectionCandidateRequestSubmit', ['uuid' => $data['application']->uuid]) }}"
                     enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="application_form">
                                    Application Form <span class="text-muted fs-6">(Upload a scanned copy of the application/consent form signed by relevant stakeholders)</span>:
                                </label>

                                {{-- Existing file / Preview --}}
                                <div class="mb-3" id="application-form-preview">
                                    @if (!empty($data['application']->application_form))
                                        <div class="border rounded p-2">
                                            <img src="{{ asset($data['application']->application_form) }}"
                                                alt="Application Form" id="application-form-preview-image"
                                                class="img-fluid"
                                                style="max-width: 300px; max-height: 400px; object-fit: contain;">
                                        </div>
                                    @else
                                        <div class="text-muted" id="no-application-form">
                                            No application form uploaded.
                                        </div>
                                    @endif
                                </div>

                                <input type="file" accept=".png,.jpg,.jpeg,.webp" class="form-control"
                                    name="application_form" id="application_form">

                                <p style="color: red">
                                    {{ $errors->first('application_form') }}
                                </p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="stakeholders_remarks">Stakeholders Remarks:</label>
                                <textarea name="stakeholders_remarks" id="stakeholders_remarks">{{ old('stakeholders_remarks') }}</textarea>
                                <p style="color: red">{{ $errors->first('stakeholders_remarks') }}</p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="status">
                                    Approval Status <span class="text-red">*</span>
                                </label>

                                <select class="form-select" name="status" required>
                                    <option value="">- Select -</option>
                                    <option value="pending_approval"
                                        {{ $data['application']->status == 'awaiting_approval' ? 'selected' : '' }}
                                        disabled>
                                        Pending Approval
                                    </option>
                                    <option value="approved"
                                        {{ $data['application']->status == 'approved' ? 'selected' : '' }}>
                                        Approved
                                    </option>
                                    <option value="declined"
                                        {{ $data['application']->status == 'declined' ? 'selected' : '' }}>
                                        Declined
                                    </option>
                                </select>

                                <p class="text-danger">
                                    {{ $errors->first('status') }}
                                </p>
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



                    {{-- script start --}}
                    <script>
                        document.getElementById('application_form').addEventListener('change', function(event) {

                            const file = event.target.files[0];

                            if (!file) {
                                return;
                            }

                            // Make sure it is an image
                            if (!file.type.startsWith('image/')) {
                                return;
                            }

                            const previewContainer = document.getElementById('application-form-preview');

                            // Remove existing preview
                            previewContainer.innerHTML = '';

                            // Create image preview
                            const img = document.createElement('img');

                            img.src = URL.createObjectURL(file);
                            img.alt = 'Application Form Preview';
                            img.className = 'img-fluid';
                            img.style.maxWidth = '300px';
                            img.style.maxHeight = '400px';
                            img.style.objectFit = 'contain';

                            const wrapper = document.createElement('div');
                            wrapper.className = 'border rounded p-2';

                            wrapper.appendChild(img);
                            previewContainer.appendChild(wrapper);
                        });
                    </script>
                    <script>
                        $(document).ready(function() {
                            $('#stakeholders_remarks').summernote({
                                height: 200,
                                toolbar: [
                                    ['style', ['bold', 'italic', 'underline', 'clear']],
                                    ['font', ['strikethrough', 'superscript', 'subscript']],
                                    ['fontsize', ['fontsize']],
                                    ['color', ['color']],
                                    ['para', ['ul', 'ol', 'paragraph']],
                                    ['height', ['height']]
                                ]
                            });
                        });
                    </script>
                    {{-- script end --}}

                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>
