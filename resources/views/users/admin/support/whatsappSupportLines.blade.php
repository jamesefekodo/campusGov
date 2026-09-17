@extends('layouts.userLayout')
@section('title', 'Whatsapp Support')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                {{-- inner nav --}}
            </div>
            @if (Auth::user()->hasPermission('manage_whatsapp_support'))
                <div class="d-flex justify-content-end">
                    <div class="m-1">
                        @if ($data['whatsappLines']->count() >= 4)
                            <button class="btn btn-secondary btn-sm table_btn">
                                <i class="fas fa-plus"></i> Add New</button>
                        @else
                            <button class="btn btn-success btn-sm table_btn" data-toggle="modal"
                                data-target="#addNewWhatsappSupportLineModal">
                                <i class="fas fa-plus"></i> Add New</button>
                        @endif
                    </div>
                </div>
                @include('users.admin.support.include.addNewWhatsappSupportLineModal')
            @endif

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                WhatsApp Support
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 m-auto">
                        <div class="my_form_container">
                            <div class=" pb-4">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered p-3">
                                            <thead class="justify-content-center text-center align-middle">
                                                <tr>
                                                    <th scope="col" style="width: 10px" class="table_header">S/N
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Name
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Number
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Update
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['whatsappLines']->isEmpty())
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['whatsappLines'] as $line)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td>{{ $line->name }}</td>
                                                            <td><span class="d-inline-flex align-items-center gap-2">
                                                                    <span
                                                                        id="phone-{{ $loop->index ?? $line->id }}">{{ $line->number }}</span>
                                                                    <a href="#" class="text-success"
                                                                        title="Chat on WhatsApp"
                                                                        onclick="openWhatsApp(event, '{{ $line->number }}', '{{ addslashes(basicSystemInfo()->association_acronym ?? config('app.name')) }} :: Support')">
                                                                        <i class="fab fa-whatsapp fs-5"></i>
                                                                    </a>
                                                                </span>
                                                                <script>
                                                                    function formatToWhatsAppNumber(rawNumber) {
                                                                        let number = rawNumber.trim();
                                                                        let hasPlus = number.startsWith('+');
                                                                        number = number.replace(/[^\d]/g, '');

                                                                        if (hasPlus) {
                                                                            return number;
                                                                        }

                                                                        if (number.startsWith('234')) {
                                                                            return number;
                                                                        }

                                                                        if (number.startsWith('0') && number.length === 11) {
                                                                            return '234' + number.substring(1);
                                                                        }

                                                                        if (number.length === 10) {
                                                                            return '234' + number;
                                                                        }

                                                                        return number;
                                                                    }

                                                                    function openWhatsApp(event, rawNumber, introMessage) {
                                                                        event.preventDefault();
                                                                        const formatted = formatToWhatsAppNumber(rawNumber);
                                                                        const encodedMessage = encodeURIComponent(introMessage);
                                                                        window.open(`https://wa.me/${formatted}?text=${encodedMessage}`, '_blank');
                                                                    }
                                                                </script>
                                                            </td>
                                                            <td>
                                                                @switch($line->is_active)
                                                                    @case(1)
                                                                        <span
                                                                            class="badge bg-success-subtle text-success">Active</span>
                                                                    @break

                                                                    @default
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">In-active</span>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-warning btn-sm" data-toggle="modal"
                                                                    data-target="#editWhatsappLineModal_{{ $line->id }}">Update</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>

                                    </div>


                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>


            @if ($data['whatsappLines']->isNotEmpty())
                @foreach ($data['whatsappLines'] as $line)
                    @include('users.admin.support.include.editWhatsappLineModal')
                @endforeach
            @endif

        </div>
    </div><!--//app-content-->
@endsection
