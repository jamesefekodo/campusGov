<div class="modal fade" id="editStudentLeadershipTenure_{{ $tenure->slug }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                   Edit Tenure</h4>
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
                        action="{{ route('editStudentLeadershipTenureSubmit', ['slug' => $tenure->slug]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Name: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $tenure->name }}"
                                    required>
                                <p style="color: red">{{ $errors->first('name') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="academic_session">Academic Session<span class="text-red">*</span>:</label>
                                <select name="academic_session" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                    @php
                                        $startYear = schoolFoundingYear();
                                        $endYear = currentAcademicYear();
                                    @endphp

                                    @for ($year = $endYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}"
                                            {{ $tenure->academic_session == "$year" ? 'selected' : '' }}>
                                            {{ $year }}/{{ $year + 1 }}
                                        </option>
                                    @endfor
                                </select>
                                <p style="color: red">{{ $errors->first('academic_session') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="start_date">Start Date: <span class="text-red">*</span></label>
                                <input type="date" class="form-control" name="start_date"
                                    value="{{ $tenure->start_date }}" required>
                                <p style="color: red">{{ $errors->first('start_date') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date">End Date:</label>
                                <input type="date" class="form-control" name="end_date"
                                    value="{{ $tenure->end_date }}">
                                <p style="color: red">{{ $errors->first('end_date') }}</p>
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
