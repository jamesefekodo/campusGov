<div class="modal fade" id="editMisconductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Misconduct Case</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="POST" action="{{ route('updateMisconductCaseSubmit', ['uuid' => $data['misconduct']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="case_id">Case ID:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" value="{{ $data['misconduct']->case_id }}"
                                    readonly>
                                <p style="color: red">{{ $errors->first('case_id') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="username">Student's Username:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="username"
                                    value="{{ $data['misconduct']->username }}" required>
                                <p style="color: red">{{ $errors->first('username') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="incident_date">Incident Date:<span class="text-red">*</span></label>
                                <input type="date" class="form-control" name="incident_date"
                                    value="{{ $data['misconduct']->incident_date }}"max="{{ \Carbon\Carbon::today()->toDateString() }}"
                                    required>

                                <p style="color: red">{{ $errors->first('incident_date') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="report_date">Report Date:<span class="text-red">*</span></label>
                                <input type="date" class="form-control" name="report_date"
                                    value="{{ $data['misconduct']->report_date }}"
                                    max="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                                <p style="color: red">{{ $errors->first('report_date') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="reported_by">Reported By:<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="reported_by"
                                    value="{{ $data['misconduct']->reported_by }}" required>
                                <p style="color: red">{{ $errors->first('reported_by') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="reporter_username">Reporter's Username:</label>
                                <input type="text" class="form-control" name="reporter_username"
                                    value="{{ $data['misconduct']->reporter_username }}">
                                <p style="color: red">{{ $errors->first('reporter_username') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="misconduct_type">Misconduct Type<span class="text-red">*</span></label>
                                <select class="form-select" name="misconduct_type" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="abuse_of_office"
                                        {{ $data['misconduct']->misconduct_type == 'abuse_of_office' ? 'selected' : '' }}>
                                        Abuse of Office</option>
                                    <option value="bullying"
                                        {{ $data['misconduct']->misconduct_type == 'bullying' ? 'selected' : '' }}>
                                        Bullying</option>
                                    <option value="conflict_of_interest"
                                        {{ $data['misconduct']->misconduct_type == 'conflict_of_interest' ? 'selected' : '' }}>
                                        Conflict of Interest</option>
                                    <option value="cyberbullying"
                                        {{ $data['misconduct']->misconduct_type == 'cyberbullying' ? 'selected' : '' }}>
                                        Cyberbullying</option>
                                    <option value="financial_misconduct"
                                        {{ $data['misconduct']->misconduct_type == 'financial_misconduct' ? 'selected' : '' }}>
                                        Financial Misconduct</option>
                                    <option value="fighting"
                                        {{ $data['misconduct']->misconduct_type == 'fighting' ? 'selected' : '' }}>
                                        Fighting</option>
                                    <option value="inciting_unrest"
                                        {{ $data['misconduct']->misconduct_type == 'inciting_unrest' ? 'selected' : '' }}>
                                        Inciting Unrest</option>
                                    <option value="insubordination"
                                        {{ $data['misconduct']->misconduct_type == 'insubordination' ? 'selected' : '' }}>
                                        Insubordination</option>
                                    <option value="misinformation"
                                        {{ $data['misconduct']->misconduct_type == 'misinformation' ? 'selected' : '' }}>
                                        Misinformation</option>
                                    <option value="neglect_of_duties"
                                        {{ $data['misconduct']->misconduct_type == 'neglect_of_duties' ? 'selected' : '' }}>
                                        Neglect of Duties</option>
                                    <option value="substance_abuse"
                                        {{ $data['misconduct']->misconduct_type == 'substance_abuse' ? 'selected' : '' }}>
                                        Substance Abuse</option>
                                    <option value="theft"
                                        {{ $data['misconduct']->misconduct_type == 'theft' ? 'selected' : '' }}>
                                        Theft</option>
                                    <option value="unauthorized_meetings"
                                        {{ $data['misconduct']->misconduct_type == 'unauthorized_meetings' ? 'selected' : '' }}>
                                        Unauthorized Meetings</option>
                                    <option value="vandalism"
                                        {{ $data['misconduct']->misconduct_type == 'vandalism' ? 'selected' : '' }}>
                                        Vandalism</option>
                                    <option value="verbal_abuse"
                                        {{ $data['misconduct']->misconduct_type == 'verbal_abuse' ? 'selected' : '' }}>
                                        Verbal Abuse</option>

                                </select>
                                <p style="color: red">{{ $errors->first('misconduct_type') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Status<span class="text-red">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="pending"
                                        {{ $data['misconduct']->status == 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="under_investigation"
                                        {{ $data['misconduct']->status == 'under_investigation' ? 'selected' : '' }}>
                                        Under Investigation</option>
                                    <option value="partially_resolved"
                                        {{ $data['misconduct']->status == 'partially_resolved' ? 'selected' : '' }}>
                                        Partially Resolved</option>
                                    <option value="resolved "
                                        {{ $data['misconduct']->status == 'resolved ' ? 'selected' : '' }}>
                                        Resolved</option>
                                </select>
                                <p style="color: red">{{ $errors->first('status') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description:<span class="text-red">*</span></label>
                                <textarea id="description" name="description" required>
                                    {{ $data['misconduct']->description }}
                                </textarea>
                                <p style="color: red">
                                    {{ $errors->first('description') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="witness_report">Witness Report:</label>
                                <textarea id="witness_report" name="witness_report">
                                    {{ $data['misconduct']->witness_report }}
                                </textarea>
                                <p style="color: red">
                                    {{ $errors->first('witness_report') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="disciplinary_action">Disciplinary Action:</label>
                                <textarea id="disciplinary_action" name="disciplinary_action">
                                    {{ $data['misconduct']->disciplinary_action }}
                                </textarea>
                                <p style="color: red">
                                    {{ $errors->first('disciplinary_action') }}</p>
                            </div>
                            <div class="accordion mt-3" id="changeFilesAccordion">

                                <div class="accordion-item">

                                    <h2 class="
                                    {{-- accordion-header --}}
                                    " id="headingFiles">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseFiles"
                                            aria-expanded="false" aria-controls="collapseFiles">

                                            Change Files

                                        </button>
                                    </h2>

                                    <div id="collapseFiles" class="accordion-collapse collapse"
                                        aria-labelledby="headingFiles" data-bs-parent="#changeFilesAccordion">

                                        <div class="accordion-body">

                                            <div class="row">

                                                <div class="form-group col-md-6">
                                                    <label for="attachment_1">Attachment 1:</label>
                                                    <input type="file" class="form-control" name="attachment_1"
                                                        accept=".png, .jpg, .webp">
                                                    <p style="color: red">{{ $errors->first('attachment_1') }}</p>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="attachment_2">Attachment 2:</label>
                                                    <input type="file" class="form-control" name="attachment_2"
                                                        accept=".png, .jpg, .webp">
                                                    <p style="color: red">{{ $errors->first('attachment_2') }}</p>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

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




<script>
    $(document).ready(function() {
        $('#description').summernote({
            height: 200, // Set editor height
            toolbar: [
                // [groupName, [list of buttons]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', []],
                ['insert', ['link']],
                ['view', []]
            ]
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#witness_report').summernote({
            height: 200, // Set editor height
            toolbar: [
                // [groupName, [list of buttons]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', []],
                ['insert', ['link']],
                ['view', []]
            ]
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#disciplinary_action').summernote({
            height: 200, // Set editor height
            toolbar: [
                // [groupName, [list of buttons]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', []],
                ['view', []]
            ]
        });
    });
</script>
