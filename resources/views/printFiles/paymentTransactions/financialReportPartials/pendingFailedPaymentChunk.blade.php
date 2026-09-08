<table class="report-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="5%" style="background:#dc2626;">#</th>
            <th width="20%" style="background:#dc2626;">Transaction ID</th>
            <th width="25%" style="background:#dc2626;">Payer</th>
            <th width="8%" style="background:#dc2626;">Purpose</th>
            <th width="10%" style="background:#dc2626;">Status</th>
            <th width="11%" style="background:#dc2626;">Amount</th>
            <th width="10%" style="background:#dc2626;">Approval</th>
            <th width="11%" style="background:#dc2626;">Date</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($payments as $payment)
            @php
                $profile = $payment->payerPersonalProfile;

                $payerName = trim(
                    ($profile?->first_name ?? '') .
                        ' ' .
                        ($profile?->middle_name ?? '') .
                        ' ' .
                        ($profile?->last_name ?? ''),
                );

                $payerName = $payerName ?: $payment->registered_payer ?? ($payment->non_registered_payer ?? 'N/A');
            @endphp

            <tr>
                <td>{{ $serialStart + $loop->iteration }}</td>

                <td style="text-transform: uppercase">
                    {{ $payment->payment_transaction_id }}
                </td>

                <td>{{ $payerName }}</td>

                <td>
                    @switch($payment->payment_purpose)
                        @case('association_due')
                            Association Due
                        @break

                        @case('donation')
                            Donation
                        @break

                        @case('invoice')
                            Invoice
                        @break

                        @case('election_form')
                            Election Form
                        @break

                        @case('voting')
                            Voting
                        @break

                        @default
                    @endswitch
                </td>
                <td>
                    {{ strtoupper($payment->payment_status ?? 'N/A') }}
                </td>

                <td class="expense">
                    ₦{{ number_format((float) $payment->amount_paid, 2) }}
                </td>

                <td>
                    {{ strtoupper($payment->approval_status ?? 'N/A') }}
                </td>

                <td>
                    {{ $payment->paid_at
                        ? \Carbon\Carbon::parse($payment->paid_at)->timezone('Africa/Lagos')->format('d M Y, h:i A')
                        : 'N/A' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
