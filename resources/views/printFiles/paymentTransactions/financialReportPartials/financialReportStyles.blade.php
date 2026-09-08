@php
    $primaryColour = $data['basicSystemInfo']->primary_colour ?? '#3b5edd';
@endphp

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 8pt;
        color: #1f2937;
    }

    .report-title {
        color: {{ $primaryColour }};
        font-size: 15pt;
        font-weight: bold;
        text-align: center;
    }

    .report-subtitle {
        color: #64748b;
        font-size: 9pt;
        text-align: center;
    }

    .section-title {
        color: #ffffff;
        padding: 7px;
        text-align: center;
        font-size: 10pt;
        font-weight: bold;
        margin-top: 10px;
        margin-bottom: 5px;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
        margin-bottom: 8px;
    }

    .report-table th {
        color: #ffffff;
        padding: 5px;
        border: 1px solid #ffffff;
        font-size: 7.5pt;
        text-align: left;
    }

    .report-table td {
        border: 1px solid #cbd5e1;
        padding: 4px;
        vertical-align: top;
        font-size: 7.5pt;
    }

    .summary-box {
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        padding: 5px;
        text-align: center;
    }

    .summary-label {
        color: #64748b;
        font-size: 7pt;
    }

    .summary-value {
        color: #123b68;
        font-size: 10pt;
        font-weight: bold;
    }

    .income {
        color: #198754;
        font-weight: bold;
    }

    .expense {
        color: #dc2626;
        font-weight: bold;
    }

    .muted {
        color: #64748b;
        font-size: 7pt;
    }

    .no-records {
        margin-top: 8px;
        padding: 12px;
        color: #b91c1c;
        text-align: center;
        border: 1px dashed #ef4444;
    }

</style>
