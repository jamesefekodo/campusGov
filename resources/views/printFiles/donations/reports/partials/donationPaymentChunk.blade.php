@php
    $primaryColour = $data['basicSystemInfo']->primary_colour ?? '#3b5edd';

    $statusClass = match ($paymentCategory) {
        'successful' => 'status-successful',
        'pending' => 'status-pending',
        'failed' => 'status-failed',
        default => 'status-pending',
    };
@endphp

<table class="report-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="4%" style="background-color:{{ $primaryColour }};">
                #
            </th>

            <th width="24%" style="background-color:{{ $primaryColour }};">
                Donor
            </th>

            <th width="15%" style="background-color:{{ $primaryColour }};">
                Transaction Reference
            </th>

            <th width="12%" style="background-color:{{ $primaryColour }};">
                Amt Don.
            </th>
            <th width="12%" style="background-color:{{ $primaryColour }};">
                Don. + Tax
            </th>
            <th width="12%" style="background-color:{{ $primaryColour }};">
                Amt Settled
            </th>

            <th width="12%" style="background-color:{{ $primaryColour }};">
                Date
            </th>

            <th width="9%" style="background-color:{{ $primaryColour }};">
                Status
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($payments as $payment)
            @php
                $profile = $payment->payerPersonalProfile;

                $donorName = trim(
                    ($profile?->first_name ?? '') .
                        ' ' .
                        ($profile?->middle_name ?? '') .
                        ' ' .
                        ($profile?->last_name ?? ''),
                );

                if (empty($donorName)) {
                    $donorName = $payment->non_registered_payer;
                }

                $paymentDate = $payment->paid_at ?? $payment->created_at;
            @endphp

            <tr>
                <td align="center">
                    {{ $serialStart + $loop->iteration }}
                </td>

                <td>
                    <strong>{{ $donorName }}</strong>

                    @if ($payment->registered_payer)
                        <br>
                        <span style="font-size:8pt; color:#64748b;">
                            {{ $payment->registered_payer }}
                        </span>
                    @endif
                </td>

                <td style="font-size:8pt; text-transform:uppercase;">
                    {{ $payment->payment_transaction_id ?? 'N/A' }}
                </td>

                <td>
                    ₦{{ number_format($payment->amount_payable, 2) }}
                </td>
                <td>
                    ₦{{ number_format($payment->amount_paid, 2) }}
                </td>
                 <td>
                    ₦{{ number_format($payment->amount_settled, 2) }}
                </td>

                <td>
                    {{ $paymentDate ? \Carbon\Carbon::parse($paymentDate)->timezone('Africa/Lagos')->format('d M Y, h:i A') : 'N/A' }}
                </td>

                <td>
                    <span class="{{ $statusClass }}">
                        {{ strtoupper($payment->payment_status ?? $paymentCategory) }}
                    </span>

                    @if ($payment->approval_status)
                        <br>
                        <span style="font-size:8pt; color:#64748b;">
                            Approval:
                            {{ strtoupper($payment->approval_status) }}
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
