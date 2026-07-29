<div class="modal fade" id="editAssociationDueModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    Edit Association Due</h4>
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
                        action="{{ route('editAssociationDueSubmit', ['uuid' => $data['due']->uuid]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="title">Name: <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="title"
                                    value="{{ $data['due']->title }}" required>
                                <p style="color: red">{{ $errors->first('title') }}</p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="is_active">Status: <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_active" required>
                                    <option selected disabled>
                                        -Select-</option>
                                    <option value="1" {{ $data['due']->is_active == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $data['due']->is_active == '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                <p style="color: red">{{ $errors->first('is_active') }}</p>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description:</label>
                                <input type="text" class="form-control" name="description"
                                    value="{{ $data['due']->description }}">
                                <p style="color: red">
                                    {{ $errors->first('description') }}</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount">Amount (₦)<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="amount"
                                    value="{{ $data['due']->amount }}" required>
                                <p style="color: red">{{ $errors->first('amount') }}</p>
                            </div>


                            <div class="form-group col-md-6">
                                <label for="academic_year">Academic Year: <span class="text-red">*</span></label>
                                <select name="academic_year" class="form-control" required>
                                    <option value="" disabled selected>Select</option>
                                    @php
                                        $startYear = schoolFoundingYear();
                                        $endYear = date('Y');
                                    @endphp

                                    @for ($year = $endYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}"
                                            {{ $data['due']->academic_year == "$year" ? 'selected' : '' }}>
                                            {{ $year }}/{{ $year + 1 }}
                                        </option>
                                    @endfor
                                </select>
                                <p style="color: red">{{ $errors->first('academic_year') }}</p>
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
