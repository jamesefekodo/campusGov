@php
    $primaryColour = $data['basicSystemInfo']->primary_colour ?? '#3b5edd';
    $donation = $data['donation'];
@endphp

<table width="100%" cellpadding="0" cellspacing="0">
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

<p style="text-transform: uppercase; font-weight: bold; color: red; margin: 10px; font-size: 18px; text-align: center;">
    Donation Payment Report :: <br> <span class="text-black">{{ $donation->title ?? 'Donation' }} </span>
</p>


<div style="text-align:center; margin-top:3px;">

</div>

<p style="margin-top:8px;">
    <strong>Date Generated/Printed:</strong>
    {{ $data['generatedAt']->format('l, d F, Y h:i A') }}
</p>

<table class="report-table" cellpadding="4" cellspacing="0">
    <thead>
        <tr>
            <th colspan="2" style="background-color:{{ $primaryColour }}; text-align:center;">
                DONATION INFORMATION
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td width="50%">
                <strong>Donation:</strong>
                {{ $donation->title ?? 'N/A' }}
                <br>

                <strong>Description:</strong>
                {{ $donation->description ?? 'N/A' }}
                <br>

                <strong>Status:</strong>
                @switch($donation->is_active)
                    @case(true)
                        Active |
                        Accepting Donations
                    @break

                    @default
                        Inactive | Not
                        Accepting Donations
                @endswitch
                <br>
                <strong>Deadline:</strong>
                @if ($donation->deadline)
                    {{ \Carbon\Carbon::parse($donation->deadline)->format('l, d F, Y h:i A') }}
                @endif
                <br>
            </td>

            <td width="50%">
                <strong>Date Created:</strong>
                @if ($donation->created_at)
                    {{ \Carbon\Carbon::parse($donation->created_at)->format('l, d F, Y h:i A') }}
                @endif
                <br>

                <strong>Approval Status:</strong>
                {{ ucfirst($donation->approval_status ?? 'N/A') }}
                <br>

                <strong>Target Amount:</strong>
                @if ($donation->target_amount)
                    ₦{{ number_format((float) ($donation->target_amount ?? 0), 2) }}
                @else
                    -
                @endif
                <br>

                <strong>Amount Raised:</strong>
                
                ₦{{ number_format((float) ($data['donation']->getAmountRaisedAttribute() ?? 0), 2) }} (Gross: ₦{{ number_format((float) ($data['donation']->getGrossAmountRaisedAttribute() ?? 0), 2) }})
            </td>
        </tr>
    </tbody>
</table>

<table width="100%" cellpadding="3" cellspacing="0">
    <tr>
        <td width="33.33%">
            <div class="summary-box">
                <div class="summary-label">
                    Successful Payments
                </div>

                <div class="summary-value">
                    {{ number_format($data['successfulPaymentCount']) }}
                </div>

                <div>
                    ₦{{ number_format($data['donation']->getAmountRaisedAttribute(), 2) }}
                </div>
            </div>
        </td>

        <td width="33.33%">
            <div class="summary-box">
                <div class="summary-label">
                    Pending Approval
                </div>

                <div class="summary-value">
                    {{ number_format($data['pendingPaymentCount']) }}
                </div>

                <div>
                    ₦{{ number_format($data['pendingAmount'], 2) }}
                </div>
            </div>
        </td>

        <td width="33.33%">
            <div class="summary-box">
                <div class="summary-label">
                    Failed Payments
                </div>

                <div class="summary-value">
                    {{ number_format($data['failedPaymentCount']) }}
                </div>

                <div>
                    ₦{{ number_format($data['failedAmount'], 2) }}
                </div>
            </div>
        </td>
    </tr>
</table>
