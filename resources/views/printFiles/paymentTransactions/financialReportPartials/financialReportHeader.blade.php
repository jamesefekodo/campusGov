@php
    $primaryColour = $data['basicSystemInfo']->primary_colour ?? '#3b5edd';
@endphp

<table width="100%" cellpadding="0" cellspacing="0">
    {{-- <tr>
        <td width="14%" align="left">
            <img src="{{ base64Image(associationLogo()) }}" width="40" alt="Association Logo">
        </td>

        <td width="72%" align="center">
            <div class="report-title">
                {{ $data['basicSystemInfo']->association_name ?? 'Financial Report' }}
            </div>

            <div class="report-subtitle">
                Financial Transactions and Financial Claims Report
            </div>
        </td>

        <td width="14%" align="right">
            <img src="{{ base64Image(schoolLogo()) }}" width="38" alt="School Logo">
        </td>
    </tr> --}}

    <tr>
        <td style="width: 70px">
            <img src="{{ base64Image(associationLogo()) }}" alt="" width="70px">
        </td>
        <td
            style="text-align: center; display: flex; justify-content: center; text-transform:uppercase; font-weight: bold; color: {{ $primaryColour }}; font-size:1.7rem;">
            {{ basicSystemInfo()->association_name }} @if (basicSystemInfo()->association_acronym)
                {{ basicSystemInfo()->association_acronym }}
                @endif - @if (basicSystemInfo()->school_acronym)
                    {{ basicSystemInfo()->school_acronym }} Chapter Lorem ipsum dolor sit amet consectetur.
                @endif
        </td>

        <td style="width: 70px">
            <img src="{{ base64Image(schoolLogo()) }}" alt="" width="70px">
        </td>
    </tr>
</table>


{{-- ###################### --}}
<p
    style="text-transform: uppercase; font-weight: bold; color: black; margin: 10px; font-size: 18px; text-align: center;">
    <strong style="color: red">Report Period: </strong>
    {{ $data['startDate']->format('d M Y') }}
    —
    {{ $data['endDate']->format('d M Y') }}
</p>

<p style="margin-top:10px; margin-bottom:6px;">
    <strong>Date Generated:</strong>
    {{ now('Africa/Lagos')->format('l, d F, Y h:i:s A') }}
</p>
{{-- ###################### --}}

<div class="section-title" style="background-color:{{ $primaryColour }};">
    FINANCIAL SUMMARY
</div>

<table width="100%" cellpadding="2" cellspacing="0">
    <tr>
        <td width="25%">
            <div class="summary-box">
                <span class="summary-label">Successful Payments</span>
                <br>
                <span class="summary-value income">
                    ₦{{ number_format($data['totalAmountPaid'], 2) }}
                </span>
                <br>
                <span class="muted">
                    {{ number_format($data['totalPaymentTransactions']) }} transaction(s)
                </span>
            </div>
        </td>

        <td width="25%">
            <div class="summary-box">
                <span class="summary-label">Amount Settled</span>
                <br>
                <span class="summary-value income">
                    ₦{{ number_format($data['totalAmountSettled'], 2) }}
                </span>
                <br>
                <span class="muted">
                    Direct: ₦{{ number_format($data['totalDirectPaymentAmount'], 2) }}
                </span>
            </div>
        </td>

        <td width="25%">
            <div class="summary-box">
                <span class="summary-label">Service Charges</span>
                <br>
                <span class="summary-value expense">
                    ₦{{ number_format($data['totalServiceCharge'], 2) }}
                </span>
                <br>
                <span class="muted">
                    Tax: ₦{{ number_format($data['totalTax'], 2) }}
                </span>
            </div>
        </td>

        <td width="25%">
            <div class="summary-box">
                <span class="summary-label">Debit Claims</span>
                <br>
                <span class="summary-value expense">
                    ₦{{ number_format($data['totalDebitFinClaim'], 2) }}
                </span>
                <br>
                <span class="muted">
                    {{ number_format($data['totalDebitFinClaimCount']) }} claim(s)
                </span>
            </div>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="2" cellspacing="0">
    <tr>
        <td width="25%">
            <div class="summary-box">
                <span class="summary-label">Credit Claims</span>
                <br>
                <span class="summary-value income">
                    ₦{{ number_format($data['totalCreditFinClaim'], 2) }}
                </span>
                <br>
                <span class="muted">
                    {{ number_format($data['totalCreditFinClaimCount']) }} claim(s)
                </span>
            </div>
        </td>

        <td width="25%">
            <div class="summary-box">
                <span class="summary-label">Pending Payments</span>
                <br>
                <span class="summary-value expense">
                    {{ number_format($data['totalPendingCount']) }}
                </span>
                <br>
                <span class="muted">Transaction(s)</span>
            </div>
        </td>

        <td width="25%">
            <div class="summary-box">
                <span class="summary-label">Failed Payments</span>
                <br>
                <span class="summary-value expense">
                    {{ number_format($data['totalFailedCount']) }}
                </span>
                <br>
                <span class="muted">Transaction(s)</span>
            </div>
        </td>

        <td width="25%">
            <div class="summary-box">
                <span class="summary-label">Unapproved Claims</span>
                <br>
                <span class="summary-value expense">
                    ₦{{ number_format($data['totalUnapprovedClaimAmount'], 2) }}
                </span>
                <br>
                <span class="muted">
                    {{ number_format($data['totalUnapprovedClaimCount']) }} claim(s)
                </span>
            </div>
        </td>


    </tr>
</table>

@php
    $net =
        $data['totalAmountPaid'] +
        $data['totalCreditFinClaim'] -
        ($data['totalDebitFinClaim'] + $data['totalServiceCharge']);
    $netClaims = $data['totalCreditFinClaim'] - $data['totalDebitFinClaim'];
@endphp



<table style="width:100%; border-collapse:separate; border-spacing:8px; table-layout:fixed;">

    {{-- ROW 1 --}}
    <tr>

        {{-- TOTAL INCOME --}}
        <td style="width:33.33%; border:1px solid #198754; vertical-align:top; text-align:center; padding:16px; word-break:break-word;">
            <div style="font-size:12px; color:#666; margin-bottom:6px; letter-spacing:.05em;">TOTAL INCOME</div>
            <div style="font-size:22px; font-weight:bold; color:#198754; word-break:break-all;">
                ₦{{ number_format($data['totalAmountPaid'] + $data['totalCreditFinClaim'], 2) }}
            </div>
            <div style="font-size:10px; color:#777; margin-top:6px;">Payments + Credit Claims</div>
        </td>

        {{-- TOTAL EXPENSES --}}
        <td style="width:33.33%; border:1px solid #dc3545; vertical-align:top; text-align:center; padding:16px; word-break:break-word;">
            <div style="font-size:12px; color:#666; margin-bottom:6px; letter-spacing:.05em;">TOTAL EXPENSES</div>
            <div style="font-size:22px; font-weight:bold; color:#dc3545; word-break:break-all;">
                ₦{{ number_format($data['totalDebitFinClaim'] + $data['totalServiceCharge'], 2) }}
            </div>
            <div style="font-size:10px; color:#777; margin-top:6px;">Debit Claims + Charges</div>
        </td>

        {{-- NET --}}
        <td style="width:33.33%; border:1px solid #0d6efd; vertical-align:top; text-align:center; padding:16px; word-break:break-word;">
            <div style="font-size:12px; color:#666; margin-bottom:6px; letter-spacing:.05em;">NET (THIS PERIOD)</div>
            <div style="font-size:22px; font-weight:bold; color:{{ $net >= 0 ? '#198754' : '#dc3545' }}; word-break:break-all;">
                ₦{{ number_format(abs($net), 2) }}
            </div>
            <div style="font-size:10px; color:{{ $net >= 0 ? '#198754' : '#dc3545' }}; margin-top:6px; font-weight:600;">
                {{ $net >= 0 ? '▲ Surplus' : '▼ Deficit' }}
            </div>
        </td>

    </tr>

    {{-- ROW 2 --}}
    <tr>

        {{-- TOTAL TRANSACTIONS --}}
        <td style="width:33.33%; border:1px solid #6c757d; vertical-align:top; text-align:center; padding:16px; word-break:break-word;">
            <div style="font-size:12px; color:#666; margin-bottom:6px; letter-spacing:.05em;">TOTAL TRANSACTIONS</div>
            <div style="font-size:22px; font-weight:bold; color:#333; word-break:break-all;">
                {{ number_format(
                    $data['totalPaymentTransactions'] +
                    $data['totalCreditFinClaimCount'] +
                    $data['totalDebitFinClaimCount']
                ) }}
            </div>
            <div style="font-size:10px; color:#777; margin-top:6px;">Approved Only</div>
        </td>

        {{-- ACCOUNT BALANCE --}}
        <td style="width:33.33%; border:2px solid #ffc107; background:#fffdf0; vertical-align:top; text-align:center; padding:16px; word-break:break-word;">
            <div style="font-size:12px; color:#666; margin-bottom:6px; letter-spacing:.05em;">ACCOUNT BALANCE</div>
            <div style="font-size:22px; font-weight:bold; color:#856404; word-break:break-all;">
                ₦{{ number_format($data['currentAccountBalance'], 2) }}
            </div>
            <div style="font-size:10px; color:#777; margin-top:6px; line-height:1.6; word-break:break-word;">
                As at {{ now('Africa/Lagos')->format('D, d M Y') }}
                &nbsp;|&nbsp;
                {{ now('Africa/Lagos')->format('h:i:s A') }} WAT
            </div>
        </td>

        {{-- PENDING / FAILED WARNING --}}
        <td style="width:33.33%; border:1px solid #ffc107; background:#fff8e1; vertical-align:top; text-align:center; padding:16px; word-break:break-word;">
            <div style="font-size:12px; color:#666; margin-bottom:6px; letter-spacing:.05em;">EXCLUDED FROM TOTALS</div>

            @if ($data['totalPendingFailedCount'] > 0 || $data['totalUnapprovedClaimCount'] > 0)
                @if ($data['totalPendingFailedCount'] > 0)
                    <div style="font-size:13px; font-weight:bold; color:#856404; word-break:break-all;">
                        {{ number_format($data['totalPendingFailedCount']) }} Pending/Failed
                    </div>
                    <div style="font-size:11px; color:#856404; word-break:break-all;">
                        ₦{{ number_format($data['totalPendingFailedAmount'], 2) }}
                    </div>
                @endif
                @if ($data['totalUnapprovedClaimCount'] > 0)
                    <div style="font-size:13px; font-weight:bold; color:#856404; margin-top:6px; word-break:break-all;">
                        {{ number_format($data['totalUnapprovedClaimCount']) }} Unapproved Claims
                    </div>
                    <div style="font-size:11px; color:#856404; word-break:break-all;">
                        ₦{{ number_format($data['totalUnapprovedClaimAmount'], 2) }}
                    </div>
                @endif
            @else
                <div style="font-size:13px; color:#198754; font-weight:bold; margin-top:8px;">
                    ✓ None
                </div>
                <div style="font-size:10px; color:#777; margin-top:4px;">
                    All transactions accounted for
                </div>
            @endif
        </td>

    </tr>

</table>