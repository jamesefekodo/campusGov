<table class="report-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="6%" style="background:#2563eb;">#</th>
            <th width="19%" style="background:#2563eb;">Claim ID</th>
            <th width="13%" style="background:#2563eb;">Type</th>
            <th width="17%" style="background:#2563eb;">Amount</th>
            <th width="27%" style="background:#2563eb;">Description</th>
            <th width="18%" style="background:#2563eb;">Date</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($claims as $claim)
            <tr>
                <td>{{ $serialStart + $loop->iteration }}</td>

                <td style="text-transform: uppercase">
                    {{ $claim->claim_id }}
                </td>

                <td>
                    {{ strtoupper($claim->claim_type ?? 'N/A') }}
                </td>

                <td class="{{ $claim->claim_type === 'credit' ? 'income' : 'expense' }}">
                    ₦{{ number_format((float) $claim->amount, 2) }}
                </td>

                <td>
                    {{ $claim->description ?? 'N/A' }}
                </td>

                <td>
                    {{ $claim->initiated_at
                        ? \Carbon\Carbon::parse($claim->initiated_at)
                            ->timezone('Africa/Lagos')
                            ->format('d M Y, h:i A')
                        : 'N/A'
                    }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>