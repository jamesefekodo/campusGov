@extends('layouts.userLayout')
@section('title', $data['due']->title)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        {{ $data['due']->title }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="col-md-12 row">
                                            <div class="col-md-6">
                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Title:</span>
                                                    <span class="text-black">
                                                        {{ $data['due']->title }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Description:</span>
                                                    <span class="text-black">
                                                        {{ $data['due']->description }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Academic Year:</span>
                                                    <span class="text-black">
                                                        {{ $data['due']->academic_year }}/{{ $data['due']->academic_year + 1 }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Amount:</span>
                                                    <span class="text-black">
                                                        ₦{{ number_format($data['due']->amount, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Status:</span>
                                                    <span class="text-black">
                                                        @switch($data['due']->is_active)
                                                            @case(true)
                                                                <span class="badge bg-success-subtle text-success">
                                                                    Active
                                                                </span>
                                                            @break

                                                            @default
                                                                <span class="badge bg-danger-subtle text-danger">
                                                                    Inactive
                                                                </span>
                                                        @endswitch </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="col-md-6 p-1">
                                                    <span style="font-weight: bold">Variations:</span>
                                                    <span class="text-black">
                                                        {{ $data['due']->variations->count() }}
                                                    </span>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            @if (Auth::user()->hasPermission('dues.update'))
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#editAssociationDueModal">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                            @endif


                                        </div>
                                        @include('users.admin.finance.dues.include.editAssociationDueModal')

                                        <div class="d-flex align-items-center mt-4">
                                            <hr class="flex-grow-1">
                                            <span class="mx-2 text-black">Variations
                                            </span>
                                            <hr class="flex-grow-1">
                                        </div>

                                        <div class="px-3 pt-3 table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    @if ($data['due']->variations->isNotEmpty())
                                                        @foreach ($data['due']->variations as $variation)
                                                            <tr class="text-black">
                                                                <th class="" scope="row">{{ $loop->iteration }}
                                                                </th>
                                                                </td>
                                                                <td class="">
                                                                    @if ($variation->admission_session)
                                                                        {{ $variation->admission_session }}/{{ $variation->admission_session + 1 }}
                                                                        -
                                                                        @php
                                                                            $year =
                                                                                $data['due']->academic_year -
                                                                                $variation->admission_session +
                                                                                1;
                                                                        @endphp

                                                                        @if ($year == 1)
                                                                            (Freshers)
                                                                        @else
                                                                            (Year {{ $year }})
                                                                        @endif
                                                                    @else
                                                                        All members who were active students during the
                                                                        academic session.
                                                                    @endif
                                                                </td>
                                                                <td class="">
                                                                    {{ optional($variation->program)->name ?? 'All Programs' }}
                                                                    -
                                                                    {{ optional($variation->programType)->type_name ?? 'All Program Types' }}
                                                                </td>
                                                                <td class="">
                                                                    ₦{{ number_format($variation->amount, 2) }}
                                                                </td>
                                                                <td class="flex-wrap">
                                                                    <span
                                                                        class="badge bg-blue text-white pe-2 ps-3 py-2 rounded-3"
                                                                        data-toggle="modal"
                                                                        data-target="#editAssociationDueVsriationModal_{{ $variation->id }}"
                                                                        style="cursor: pointer;">
                                                                        <i class="fas fa-edit me-1"></i>Edit
                                                                    </span>
                                                                    <a href="javascript:void(0)"
                                                                        class="badge bg-red text-white pe-2 ps-3 py-2 rounded-3 delete-confirm"
                                                                        data-toggle="modal"
                                                                        data-url="{{ route('removeAssociationVariation', ['id' => $variation->id]) }}"
                                                                        style="cursor: pointer;">
                                                                        <i class="fas fa-trash me-1"></i>Remove
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4" class="text-center">
                                                                <h3 class="p-1 m-1 text-red">
                                                                    No Records Found!
                                                                </h3>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                            @if (Auth::user()->hasPermission('dues.create'))
                                                <div class="d-flex justify-content-center">
                                                    <div class="m-1">
                                                        <button class="btn btn-primary btn-sm table_btn" data-toggle="modal"
                                                            data-target="#addNewDueVariationModal">
                                                            <i class="fas fa-plus"></i> Add New Variation</button>
                                                    </div>
                                                </div>
                                            @endif
                                            @include('users.admin.finance.dues.include.addNewDueVariationModal')

                                            @if ($data['due']->variations->isNotEmpty())
                                                @foreach ($data['due']->variations as $variation)
                                                    @include('users.admin.finance.dues.include.editAssociationDueVariationModal')
                                                @endforeach
                                            @endif

                                        </div>


                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('associationDues') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.delete-confirm').forEach(button => {
                        button.addEventListener('click', function() {

                            const url = this.dataset.url;

                            Swal.fire({
                                title: 'Are you sure?',
                                text: "This policy will be permanently deleted.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = url;
                                }
                            });

                        });
                    });
                });
            </script>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
