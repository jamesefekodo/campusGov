@php
    $primaryColour = $data['basicSystemInfo']->primary_colour ?? '#3b5edd';
    $secondaryColour = $data['basicSystemInfo']->secondary_colour ?? '#7a97ff';
@endphp

<div class="content">

    <table width="100%" cellpadding="0" cellspacing="0">
        {{-- <tr>
            <td width="16%" align="left" valign="middle">
                <img
                    src="{{ base64Image(associationLogo()) }}"
                    width="50"
                    alt="Association Logo"
                >
            </td>

            <td width="68%" align="center" valign="middle">
                <div style="font-size:13pt; font-weight:bold; color:{{ $primaryColour }};">
                    {{ $data['basicSystemInfo']->association_name ?? '' }}

                    @if ($data['basicSystemInfo']->association_acronym ?? false)
                        ({{ $data['basicSystemInfo']->association_acronym }})
                    @endif
                </div>

                @if ($data['basicSystemInfo']->school_acronym ?? false)
                    <div style="font-size:9pt; color:#475569;">
                        {{ $data['basicSystemInfo']->school_acronym }} Chapter
                    </div>
                @endif
            </td>

            <td width="16%" align="right" valign="middle">
                <img
                    src="{{ base64Image(schoolLogo()) }}"
                    width="50"
                    alt="School Logo"
                >
            </td>
        </tr> --}}
        <tr>
            <td style="width: 60px">
                <img src="{{ base64Image(associationLogo()) }}" alt="" width="60px">
            </td>
            <td
                style="text-align: center; display: flex; justify-content: center; text-transform:uppercase; font-weight: bold; color: {{ $primaryColour }}; font-size:1.5rem;">
                {{ basicSystemInfo()->association_name }} @if (basicSystemInfo()->association_acronym)
                    {{ basicSystemInfo()->association_acronym }}
                    @endif - @if (basicSystemInfo()->school_acronym)
                        {{ basicSystemInfo()->school_acronym }} Chapter
                    @endif
            </td>

            <td style="width: 50px">
                <img src="{{ base64Image(schoolLogo()) }}" alt="" width="50px">
            </td>
        </tr>
    </table>

    {{-- <div style="text-align:center; color:#b91c1c; font-size:14pt; font-weight:bold; margin-top:8px;">
        Waivers and Successful Payments
    </div>

    <div style="text-align:center; font-size:10pt; margin-top:3px;">
        {{ $data['due']->title }}
        —
        {{ $data['dueSession'] }}
    </div> --}}
     <p style="text-transform: uppercase; font-weight: bold; color: red; margin: 10px; font-size: 18px; text-align: center;">
                        Waivers and Successful Payments for <br> <span class="text-black">{{ $data['due']->title }}</span> -
                        {{ $data['due']->academic_year }}/{{ $data['due']->academic_year + 1 }}
                    </p>

    <p style="margin-top:10px; margin-bottom:6px;">
        <strong>Date Printed:</strong>
        {{ now('Africa/Lagos')->format('l, d F, Y h:i:s A') }}
    </p>

    <table class="report-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th colspan="2" style="background-color:{{ $primaryColour }};">
                    DUE INFORMATION
                </th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td width="50%">
                    <strong>Due:</strong> {{ $data['due']->title }}<br>
                    <strong>Description:</strong> {{ $data['due']->description }}<br>
                    <strong>Status:</strong>
                    {{ $data['due']->is_active ? 'Active' : 'Inactive' }}<br>
                    <strong>Expected Payers:</strong>
                    {{ $data['expectedPayers'] }}<br>
                    <strong>Approved Waivers:</strong>
                    {{ $data['approvedWaivers'] }}
                </td>

                <td width="50%">
                    <strong>Academic Session:</strong>
                    {{ $data['dueSession'] }}<br>
                    <strong>Amount:</strong>
                    ₦{{ number_format($data['due']->amount, 2) }}<br>
                    <strong>Variations:</strong>
                    {{ $data['variationsCount'] }}<br>
                    <strong>Successful Payments:</strong>
                    {{ $data['successfulPaymentsCount'] }}<br>
                    <strong>Outstanding:</strong>
                    {{ $data['outstandingCount'] }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
