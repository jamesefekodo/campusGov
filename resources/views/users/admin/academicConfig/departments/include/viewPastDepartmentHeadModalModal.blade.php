<div class="modal fade" id="viewPastDepartmentHeadModalModal_{{ $head->uuid }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal_header" style="width: 100%">
                <h4 class="modal-title block text-white text-center p-1 text-capitalize" id="exampleModalLongTitle">
                    {{ $head->name }} ( Past Department Heads )</h4>
                <h4>
                    <button type="button" class="bg-red text-white mt-1 p-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal_form_wrapper pb-4">

                    {{-- <div class="card-body"> --}}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6 justify-content-center text-center">
                                @if ($head->photo && $head->photo)
                                    <img src="{{ asset('assets/uploads/department/head/photo/' . $head->photo) }}"
                                        alt="{{ $head->name }}" style="max-width: 200px">
                                @else
                                    <img src="{{ asset('system/images/hod_default_pic.webp') }}" alt="Default Picture"
                                        style="max-width: 200px">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p class="text-black my-0">
                                    <span class="text-secondary">Name:</span>
                                    {{ $head->name }}
                                    @if ($head->certifications)
                                        ({{ $head->certifications }})
                                    @endif
                                </p>
                                <p class="text-black my-0">
                                    <span class="text-secondary">Phone:</span>
                                    {{ $head->phone }}
                                </p>
                                <p class="text-black my-0">
                                    <span class="text-secondary">Email:</span>
                                    {{ $head->email }}
                                </p>
                                <p class="text-black my-0">
                                    <span class="text-secondary">Profile:</span>
                                    <span class="text-black">{!! $head->profile !!}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- </div> --}}


                </div> <!-- Close modal_form_wrapper -->
            </div>

        </div>
    </div>
</div>
