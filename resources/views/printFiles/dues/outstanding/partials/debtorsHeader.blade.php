@php
    $primaryColour = $data['basicSystemInfo']->primary_colour ?? '#3b5edd';
@endphp

<div class="content">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="16%" align="left">
                <img
                    src="{{ base64Image(associationLogo()) }}"
                    width="45"
                    alt="Association Logo"
                >
            </td>

            <td width="68%" align="center">
                <div
                    style="
                        color: {{ $primaryColour }};
                        font-size: 13pt;
                        font-weight: bold;
                    "
                >
                    {{ $data['basicSystemInfo']->association_name ?? '' }}
                </div>

                <div style="color: #64748b; font-size: 9pt;">
                    Outstanding Debtors Report
                </div>
            </td>

            <td width="16%" align="right">
                <img
                    src="{{ base64Image(schoolLogo()) }}"
                    width="42"
                    alt="School Logo"
                >
            </td>
        </tr>
    </table>

    <div
        style="
            color: #b91c1c;
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin-top: 8px;
        "
    >
        Outstanding Debtors
    </div>

    <div style="font-size: 10pt; text-align: center; margin-top: 3px;">
        {{ $data['due']->title }}
        —
        {{ $data['dueSession'] }}
    </div>

    <p style="margin-top: 10px; margin-bottom: 7px;">
        <strong>Date Printed:</strong>
        {{ now('Africa/Lagos')->format('l, d F, Y h:i:s A') }}
    </p>

    <table class="report-table" width="100%" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th
                    colspan="2"
                    style="background-color: {{ $primaryColour }};"
                >
                    DUE SUMMARY
                </th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td width="50%">
                    <strong>Due:</strong> {{ $data['due']->title }}<br>
                    <strong>Academic Session:</strong> {{ $data['dueSession'] }}<br>
                    <strong>Amount:</strong>
                    ₦{{ number_format((float) $data['due']->amount, 2) }}<br>
                    <strong>Status:</strong>
                    {{ $data['due']->is_active ? 'Active' : 'Inactive' }}
                </td>

                <td width="50%">
                    <strong>Expected Payers:</strong>
                    {{ number_format($data['expectedPayers']) }}<br>

                    <strong>Successful Payments:</strong>
                    {{ number_format($data['successfulPaymentsCount']) }}<br>

                    <strong>Approved Waivers:</strong>
                    {{ number_format($data['approvedWaiversCount']) }}<br>

                    <strong>Outstanding Debtors:</strong>
                    {{ number_format($data['debtorCount']) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div
        class="section-title"
        style="background-color: {{ $primaryColour }};"
    >
        MEMBERS WITH NO SUCCESSFUL PAYMENT OR APPROVED WAIVER
    </div>
</div>