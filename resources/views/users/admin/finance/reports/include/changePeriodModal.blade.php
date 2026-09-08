<div class="modal fade" id="changePeriodModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1" id="exampleModalLongTitle">
                    New Invoice</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <form class="form my_form" method="GET" action="{{ route('financialReports') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">  
                                <label for="start_date">From<span class="text-red">*</span></label>
                                <input type="datetime-local" class="form-control" name="start_date"
                                    value="{{ $data['startDate'] }}" required>
                                <p style="color: red">{{ $errors->first('start_date') }}</p>
                            </div>
                             <div class="form-group col-md-6">  
                                <label for="end_date">To<span class="text-red">*</span></label>
                                <input type="datetime-local" class="form-control" name="end_date"
                                    value="{{ $data['endDate'] }}" required>
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
