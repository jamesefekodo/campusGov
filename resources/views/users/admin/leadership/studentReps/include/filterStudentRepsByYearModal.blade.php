<div class="modal fade" id="sessionSelector" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
        <div class="modal-content">
            {{-- <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Filter</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div> --}}
            <div class="d-flex justify-content-end m-1">
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
    
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="GET" action="{{ request()->url() }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="academic_session">Academic Session<span class="text-red">*</span>:</label>
                                <select name="academic_session" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                    @php
                                        $startYear = schoolFoundingYear();
                                        $endYear = currentAcademicYear();
                                    @endphp

                                    @for ($year = $endYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}"
                                            {{ $data['academicSession'] == "$year" ? 'selected' : '' }}>
                                            {{ $year }}/{{ $year + 1 }}
                                        </option>
                                    @endfor
                                </select>
                                <p style="color: red">{{ $errors->first('academic_session') }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row g-2">
                            <div
                                class="{{ isset($data['requestType']) && $data['requestType'] === 'filter' ? 'col-md-9' : 'col-12' }}">
                                <button type="submit" class="green-button submit-btn w-100">
                                    <span class="btn-text">
                                        <i class="fas fa-filter me-2"></i>
                                        Apply
                                    </span>

                                    <span class="btn-loading" style="display:none;">
                                        <i class="fas fa-spinner fa-spin"></i> Processing...
                                    </span>
                                </button>
                            </div>

                            @if (isset($data['requestType']) && $data['requestType'] === 'filter')
                                <div class="col-md-3">
                                    <a href="{{ route('studentMembers') }}" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-undo me-2"></i>
                                        Clear Filter
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                    {{-- </div> --}}


                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>
