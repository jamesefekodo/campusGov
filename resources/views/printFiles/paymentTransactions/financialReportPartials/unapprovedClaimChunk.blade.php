<table class="report-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="6%" style="background:#d97706;">#</th>
            <th width="19%" style="background:#d97706;">Claim ID</th>
            <th width="13%" style="background:#d97706;">Type</th>
            <th width="17%" style="background:#d97706;">Amount</th>
            <th width="27%" style="background:#d97706;">Description</th>
            <th width="18%" style="background:#d97706;">Status / Date</th>
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

                <td class="expense">
                    ₦{{ number_format((float) $claim->amount, 2) }}
                </td>

                <td>
                    {{ $claim->description ?? 'N/A' }}
                </td>

                <td>
                    {{ strtoupper($claim->approval_status ?? 'N/A') }}
                    <br>

                    <span class="muted">
                        {{ $claim->initiated_at
                            ? \Carbon\Carbon::parse($claim->initiated_at)
                                ->timezone('Africa/Lagos')
                                ->format('d M Y, h:i A')
                            : 'N/A'
                        }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>