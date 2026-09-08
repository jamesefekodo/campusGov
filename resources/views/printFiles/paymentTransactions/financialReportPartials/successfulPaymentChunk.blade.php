<table class="report-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="4%" style="background:#198754;">#</th>
            <th width="10%" style="background:#198754;">Transaction ID</th>
            <th width="15%" style="background:#198754;">Payer</th>
            <th width="6%" style="background:#198754;">Type</th>
            <th width="6%" style="background:#198754;">Purpose</th>
            <th width="10%" style="background:#198754;">Amount Payable</th>
            <th width="9%" style="background:#198754;">Tax</th>
            <th width="10%" style="background:#198754;">Amount Paid</th>
            <th width="10%" style="background:#198754;">Charges</th>
            <th width="10%" style="background:#198754;">Settled</th>
            <th width="10%" style="background:#198754;">Date</th>
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

                <td>{{ ucfirst($payment->payment_type ?? 'N/A') }}</td>

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

                <td class="">
                    ₦{{ number_format((float) $payment->amount_payable, 2) }}
                </td>

                <td class="">
                    ₦{{ number_format((float) $payment->total_tax, 2) }}
                </td>

                <td class="">
                    ₦{{ number_format((float) $payment->amount_paid, 2) }}
                </td>
                <td class="">
                    ₦{{ number_format((float) $payment->service_charge, 2) }}
                </td>

                <td class="income">
                    ₦{{ number_format((float) $payment->amount_settled, 2) }}
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
