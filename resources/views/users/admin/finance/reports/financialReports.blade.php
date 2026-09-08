@extends('layouts.userLayout')
@section('title', 'Financial Report')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        Financial Report
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        @include('users.admin.finance.reports.include.changePeriodModal')

                                        <div class="col-md-12 row">
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">Report from:</span>
                                                <span class="text-black">
                                                    {{ \Carbon\Carbon::parse($data['startDate'])->format('l, d F, Y h:i A') }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 p-1">
                                                <span style="font-weight: bold">To:</span>
                                                <span class="text-black">
                                                    {{ \Carbon\Carbon::parse($data['endDate'])->format('l, d F, Y h:i A') }}
                                                    <button class="ms-3 mb-1 btn btn-warning" data-toggle="modal"
                                                        data-target="#changePeriodModal" style="cursor: pointer;">
                                                        <i class="fa-solid fa-calendar-days me-1"></i> Change period
                                                    </button>
                                                    <a class="ms-3 mb-1 btn btn-danger"
                                                        href="{{ route('financialReports') }}" style="cursor: pointer;">
                                                        <i class="fa-solid fa-rotate-left me-1"></i> Clear Selection
                                                    </a>
                                                </span>
                                            </div>
                                            @php
                                                $net =
                                                    $data['totalAmountPaid'] +
                                                    $data['totalCreditFinClaim'] -
                                                    ($data['totalDebitFinClaim'] + $data['totalServiceCharge']);
                                                $netClaims = $data['totalCreditFinClaim'] - $data['totalDebitFinClaim'];
                                            @endphp

                                            {{-- ════ CHART ════ --}}
                                            <div class="card mb-4 p-0">
                                                <div
                                                    class="card-header fw-bold d-flex align-items-center justify-content-between">
                                                    <span>
                                                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                                                        Income vs Expenses
                                                        <span class="text-muted fw-normal ms-2" style="font-size:13px;">
                                                            {{ $data['startDate']->format('d M Y') }} —
                                                            {{ $data['endDate']->format('d M Y') }}
                                                        </span>
                                                    </span>
                                                    <span
                                                        class="badge bg-secondary">{{ ucfirst($data['chartGrouping']) }}</span>
                                                </div>
                                                <div class="card-body">
                                                    <div id="financeChart"></div>
                                                </div>
                                            </div>

                                            {{-- ════ SECTION 1 — OVERVIEW ════ --}}
                                            <div class="card mb-4 p-0">
                                                <div class="card-header fw-bold bg-dark text-white">
                                                    <i class="fas fa-chart-pie me-2"></i> Overall Summary
                                                    <span class="fw-normal ms-2" style="font-size:12px; opacity:.7;">
                                                        Approved transactions &amp; claims only
                                                    </span>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">

                                                        <div class="col-md-3 col-6">
                                                            <div class="card border-success border h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Total Income</div>
                                                                    <div class="fw-bold fs-5 text-success">
                                                                        ₦{{ number_format($data['totalAmountPaid'] + $data['totalCreditFinClaim'], 2) }}
                                                                    </div>
                                                                    <div class="small text-muted">Payments + Credit claims
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 col-6">
                                                            <div class="card border-danger border h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Total Expenses</div>
                                                                    <div class="fw-bold fs-5 text-danger">
                                                                        ₦{{ number_format($data['totalDebitFinClaim'] + $data['totalServiceCharge'], 2) }}
                                                                    </div>
                                                                    <div class="small text-muted">Debit claims + Charges
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 col-6">
                                                            <div class="card border-primary border h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Net (This Period)
                                                                    </div>
                                                                    <div
                                                                        class="fw-bold fs-5 {{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                                                                        ₦{{ number_format(abs($net), 2) }}
                                                                    </div>
                                                                    <div
                                                                        class="small {{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                                                                        {{ $net >= 0 ? 'Surplus' : 'Deficit' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 col-6">
                                                            <div class="card border-secondary border h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Total Transactions
                                                                    </div>
                                                                    <div class="fw-bold fs-5">
                                                                        {{ number_format($data['totalPaymentTransactions'] + $data['totalCreditFinClaimCount'] + $data['totalDebitFinClaimCount']) }}
                                                                    </div>
                                                                    <div class="small text-muted">Approved only</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    {{-- Pending / Failed Warning --}}
                                                    @if ($data['totalPendingFailedCount'] > 0 || $data['totalUnapprovedClaimCount'] > 0)
                                                        <div
                                                            class="alert alert-warning d-flex align-items-center gap-3 mt-3 mb-0">
                                                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                                                            <div>
                                                                <strong>Excluded from totals above:</strong>
                                                                @if ($data['totalPendingFailedCount'] > 0)
                                                                    <span class="badge bg-warning text-dark ms-1">
                                                                        {{ number_format($data['totalPendingFailedCount']) }}
                                                                        pending/failed payment(s)
                                                                        —
                                                                        ₦{{ number_format($data['totalPendingFailedAmount'], 2) }}
                                                                    </span>
                                                                @endif
                                                                @if ($data['totalUnapprovedClaimCount'] > 0)
                                                                    <span class="badge bg-warning text-dark ms-1">
                                                                        {{ number_format($data['totalUnapprovedClaimCount']) }}
                                                                        unapproved claim(s)
                                                                        —
                                                                        ₦{{ number_format($data['totalUnapprovedClaimAmount'], 2) }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- ════ SECTION 2 — PAYMENT TRANSACTIONS ════ --}}
                                            <div class="card mb-4 p-0">
                                                <div
                                                    class="card-header fw-bold bg-success text-white d-flex justify-content-between align-items-center">
                                                    <span><i class="fas fa-credit-card me-2"></i> Payment
                                                        Transactions</span>
                                                    <span class="badge bg-white text-success">Successful &amp;
                                                        Approved</span>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">

                                                        <div class="col-md-4 col-6">
                                                            <div class="card border-0 bg-light h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Total Amount Paid
                                                                    </div>
                                                                    <div class="fw-bold fs-5 text-success">
                                                                        ₦{{ number_format($data['totalAmountPaid'], 2) }}
                                                                    </div>
                                                                    <div class="small text-muted">
                                                                        {{ number_format($data['totalPaymentTransactions']) }}
                                                                        transactions</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 col-6">
                                                            <div class="card border-0 bg-light h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Amount Settled</div>
                                                                    <div class="fw-bold fs-5 text-primary">
                                                                        ₦{{ number_format($data['totalAmountSettled'], 2) }}
                                                                    </div>
                                                                    <div class="small text-muted">After charges &amp; tax
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 col-6">
                                                            <div class="card border-0 bg-light h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Total Tax</div>
                                                                    <div class="fw-bold fs-5 text-warning">
                                                                        ₦{{ number_format($data['totalTax'], 2) }}</div>
                                                                    <div class="small text-muted">
                                                                        {{ number_format($data['totalTaxTransactions']) }}
                                                                        taxed</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 col-6">
                                                            <div class="card border-0 bg-light h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Service Charges</div>
                                                                    <div class="fw-bold fs-5 text-danger">
                                                                        ₦{{ number_format($data['totalServiceCharge'], 2) }}
                                                                    </div>
                                                                    <div class="small text-muted">Gateway fees</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 col-6">
                                                            <div class="card border-0 bg-light h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Direct Payments
                                                                    </div>
                                                                    <div class="fw-bold fs-5">
                                                                        ₦{{ number_format($data['totalDirectPaymentAmount'], 2) }}
                                                                    </div>
                                                                    <div class="small text-muted">
                                                                        {{ number_format($data['totalDirectPaymentCount']) }}
                                                                        payments</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 col-6">
                                                            <div class="card border-0 bg-light h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Regularized Payments
                                                                    </div>
                                                                    <div class="fw-bold fs-5">
                                                                        ₦{{ number_format($data['totalRegularizedPaymentAmount'], 2) }}
                                                                    </div>
                                                                    <div class="small text-muted">
                                                                        {{ number_format($data['totalRegularizedPaymentCount']) }}
                                                                        payments</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    {{-- Pending / Failed breakdown --}}
                                                    @if ($data['totalPendingFailedCount'] > 0)
                                                        <hr>
                                                        <div class="row g-3">
                                                            <div class="col-12">
                                                                <div class="small fw-bold text-muted text-uppercase mb-2">
                                                                    <i class="fas fa-clock me-1"></i> Pending &amp; Failed
                                                                    (excluded from totals)
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <div class="card border-warning border h-100">
                                                                    <div class="card-body text-center">
                                                                        <div class="small text-muted mb-1">Pending Payments
                                                                        </div>
                                                                        <div class="fw-bold fs-5 text-warning">
                                                                            {{ number_format($data['totalPendingCount']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <div class="card border-danger border h-100">
                                                                    <div class="card-body text-center">
                                                                        <div class="small text-muted mb-1">Failed Payments
                                                                        </div>
                                                                        <div class="fw-bold fs-5 text-danger">
                                                                            {{ number_format($data['totalFailedCount']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <div class="card border-secondary border h-100">
                                                                    <div class="card-body text-center">
                                                                        <div class="small text-muted mb-1">Total
                                                                            Pending/Failed Amount</div>
                                                                        <div class="fw-bold fs-5 text-secondary">
                                                                            ₦{{ number_format($data['totalPendingFailedAmount'], 2) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- ════ SECTION 3 — FINANCIAL CLAIMS ════ --}}
                                            <div class="card mb-4 p-0">
                                                <div
                                                    class="card-header fw-bold bg-primary text-white d-flex justify-content-between align-items-center">
                                                    <span><i class="fas fa-file-invoice-dollar me-2"></i> Financial
                                                        Claims</span>
                                                    <span class="badge bg-white text-primary">Approved Only</span>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">

                                                        <div class="col-md-3 col-6">
                                                            <div class="card border-success border h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Credit Claims</div>
                                                                    <div class="fw-bold fs-5 text-success">
                                                                        ₦{{ number_format($data['totalCreditFinClaim'], 2) }}
                                                                    </div>
                                                                    <div class="small text-muted">
                                                                        {{ number_format($data['totalCreditFinClaimCount']) }}
                                                                        claims</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 col-6">
                                                            <div class="card border-danger border h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Debit Claims</div>
                                                                    <div class="fw-bold fs-5 text-danger">
                                                                        ₦{{ number_format($data['totalDebitFinClaim'], 2) }}
                                                                    </div>
                                                                    <div class="small text-muted">
                                                                        {{ number_format($data['totalDebitFinClaimCount']) }}
                                                                        claims</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 col-6">
                                                            <div class="card border-info border h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Net Claims</div>
                                                                    <div
                                                                        class="fw-bold fs-5 {{ $netClaims >= 0 ? 'text-success' : 'text-danger' }}">
                                                                        ₦{{ number_format(abs($netClaims), 2) }}
                                                                    </div>
                                                                    <div
                                                                        class="small {{ $netClaims >= 0 ? 'text-success' : 'text-danger' }}">
                                                                        {{ $netClaims >= 0 ? 'Net Credit' : 'Net Debit' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 col-6">
                                                            <div class="card border-secondary border h-100">
                                                                <div class="card-body text-center">
                                                                    <div class="small text-muted mb-1">Total Claims</div>
                                                                    <div class="fw-bold fs-5">
                                                                        {{ number_format($data['totalCreditFinClaimCount'] + $data['totalDebitFinClaimCount']) }}
                                                                    </div>
                                                                    <div class="small text-muted">credit + debit</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    {{-- Unapproved claims breakdown --}}
                                                    @if ($data['totalUnapprovedClaimCount'] > 0)
                                                        <hr>
                                                        <div class="row g-3">
                                                            <div class="col-12">
                                                                <div class="small fw-bold text-muted text-uppercase mb-2">
                                                                    <i class="fas fa-hourglass-half me-1"></i> Unapproved
                                                                    Claims (excluded from totals)
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <div class="card border-warning border h-100">
                                                                    <div class="card-body text-center">
                                                                        <div class="small text-muted mb-1">Pending Claims
                                                                        </div>
                                                                        <div class="fw-bold fs-5 text-warning">
                                                                            {{ number_format($data['totalPendingClaimCount']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <div class="card border-danger border h-100">
                                                                    <div class="card-body text-center">
                                                                        <div class="small text-muted mb-1">Rejected Claims
                                                                        </div>
                                                                        <div class="fw-bold fs-5 text-danger">
                                                                            {{ number_format($data['totalRejectedClaimCount']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <div class="card border-secondary border h-100">
                                                                    <div class="card-body text-center">
                                                                        <div class="small text-muted mb-1">Unapproved
                                                                            Amount</div>
                                                                        <div class="fw-bold fs-5 text-secondary">
                                                                            ₦{{ number_format($data['totalUnapprovedClaimAmount'], 2) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- ════ APEXCHARTS SCRIPT ════ --}}
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {

                                                    const chartData = @json($data['chartData'] ?? []);

                                                    if (!chartData || chartData.length === 0) {
                                                        document.getElementById('financeChart').innerHTML =
                                                            '<p class="text-center text-muted py-4"><i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>No chart data available for the selected period.</p>';
                                                        return;
                                                    }

                                                    const labels = chartData.map(d => d.label);
                                                    const income = chartData.map(d => parseFloat(d.income) || 0);
                                                    const expenses = chartData.map(d => parseFloat(d.expenses) || 0);

                                                    new ApexCharts(document.getElementById('financeChart'), {
                                                        series: [{
                                                                name: 'Income',
                                                                data: income
                                                            },
                                                            {
                                                                name: 'Expenses',
                                                                data: expenses
                                                            },
                                                        ],
                                                        chart: {
                                                            type: 'bar',
                                                            height: 360,
                                                            toolbar: {
                                                                show: true
                                                            },
                                                            fontFamily: 'inherit',
                                                        },
                                                        plotOptions: {
                                                            bar: {
                                                                borderRadius: 4,
                                                                columnWidth: '55%',
                                                                dataLabels: {
                                                                    position: 'top'
                                                                }
                                                            }
                                                        },
                                                        colors: ['#198754', '#dc3545'],
                                                        dataLabels: {
                                                            enabled: false
                                                        },
                                                        xaxis: {
                                                            categories: labels,
                                                            labels: {
                                                                rotate: -35,
                                                                trim: true,
                                                                style: {
                                                                    fontSize: '12px'
                                                                }
                                                            }
                                                        },
                                                        yaxis: {
                                                            labels: {
                                                                formatter: val => '₦' + val.toLocaleString('en-NG', {
                                                                    minimumFractionDigits: 0,
                                                                    maximumFractionDigits: 0
                                                                })
                                                            }
                                                        },
                                                        tooltip: {
                                                            shared: true,
                                                            intersect: false,
                                                            y: {
                                                                formatter: val => '₦' + val.toLocaleString('en-NG', {
                                                                    minimumFractionDigits: 2,
                                                                    maximumFractionDigits: 2
                                                                })
                                                            }
                                                        },
                                                        legend: {
                                                            position: 'top',
                                                            horizontalAlign: 'left'
                                                        },
                                                        grid: {
                                                            borderColor: '#f0f0f0',
                                                            strokeDashArray: 4
                                                        },
                                                    }).render();
                                                });
                                            </script>
                                        </div>


                                    </div>

                                </div>

                                <form action="{{ route('financialReports') }}" method="GET">
                                    @csrf

                                    <input type="hidden" class="form-control" name="start_date"
                                        value="{{ $data['startDate'] }}" required>
                                    <input type="hidden" class="form-control" name="end_date"
                                        value="{{ $data['endDate'] }}" required>

                                    <input type="hidden" name="page_type" id="page_type_input"
                                        value="{{ $data['pageType'] }}">

                                    <div class="d-flex gap-2 justify-content-center my-3 mt-5 flex-wrap">

                                        <button type="button" onclick="submitPageType('transactions_page')"
                                            class="btn {{ $data['pageType'] === 'transactions_page' ? 'btn-success' : 'btn-secondary' }}">
                                            <i class="fas fa-receipt me-1"></i>
                                            Transactions
                                            @if (isset($data['successfulPayments']))
                                            @endif
                                        </button>

                                        <button type="button" onclick="submitPageType('claims_page')"
                                            class="btn {{ $data['pageType'] === 'claims_page' ? 'btn-success' : 'btn-secondary' }}">
                                            <i class="fas fa-file-circle-check me-1"></i>
                                            Claim
                                        </button>

                                    </div>
                                </form>

                                <script>
                                    function submitPageType(type) {
                                        document.getElementById('page_type_input').value = type;
                                        document.getElementById('page_type_input').closest('form').submit();
                                    }
                                </script>


                                <div class="my_form_container mt-4">
                                    <div class=" pb-4">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered p-3">
                                                    <thead class="justify-content-center text-center align-middle">
                                                        @if ($data['pageType'] == 'transactions_page')
                                                            <tr>
                                                                <th scope="col" style="width: 10px"
                                                                    class="table_header">
                                                                    S/N
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Transaction ID
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Purpose
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Amount
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Paid By
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Date
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Method
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Status
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    View
                                                                </th>
                                                            </tr>
                                                        @elseif ($data['pageType'] == 'claims_page')
                                                            <tr>
                                                                <th scope="col" style="width: 10px"
                                                                    class="table_header">S/N
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Type
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Amount
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Description
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Date
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Submitted
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    Status
                                                                </th>
                                                                <th scope="col" class="table_header">
                                                                    View
                                                                </th>
                                                            </tr>
                                                        @endif
                                                    </thead>
                                                    {{-- payment transactions start --}}
                                                    @if ($data['pageType'] == 'transactions_page')
                                                        @if ($data['paymentTransactions']->isEmpty())
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="9" class="text-center">
                                                                        <h3 class="p-1 m-1 text-red">
                                                                            No Records Found!
                                                                        </h3>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        @else
                                                            <tbody>
                                                                @foreach ($data['paymentTransactions'] as $payment)
                                                                    <tr class=" align-middle text-center">
                                                                        <th>{{ $loop->iteration }}</th>
                                                                        <td class="text-start text-uppercase">
                                                                            {{ $payment->payment_transaction_id }}
                                                                        </td>
                                                                        <td class="text-start">
                                                                            @switch($payment->payment_purpose)
                                                                                @case('association_due')
                                                                                    @if ($payment->due)
                                                                                        {{-- @php
                                                                                            $academic =
                                                                                                $payment->registeredPayer
                                                                                                    ->memberAcademicInfo;
                                                                                            $level = studentLevelAtDue(
                                                                                                $academic,
                                                                                                $payment->due,
                                                                                            );
                                                                                            @endphp --}}
                                                                                        @php
                                                                                            $academic =
                                                                                                $payment
                                                                                                    ->registeredPayer
                                                                                                    ?->memberAcademicInfo;

                                                                                            $programLevel = studentLevelAtDue(
                                                                                                $academic,
                                                                                                $payment->due,
                                                                                            );

                                                                                            $program =
                                                                                                $programLevel[
                                                                                                    'program'
                                                                                                ];
                                                                                            $level =
                                                                                                $programLevel['level'];
                                                                                        @endphp

                                                                                        {{ $payment->due->title }}
                                                                                        ({{ $payment->due->academic_year }}/{{ $payment->due->academic_year + 1 }}
                                                                                        Academic Session)
                                                                                        -
                                                                                        {{-- {{ currentLevel($academic->theProgram, $level) }} --}} @if ($program)
                                                                                            {{ currentLevel($program, $level) }}
                                                                                        @else
                                                                                            <span class="text-danger">
                                                                                                Previous programme is missing
                                                                                            </span>
                                                                                        @endif
                                                                                    @endif

                                                                                    <a href="{{ route('viewDue', ['uuid' => $payment->due->uuid]) }}"
                                                                                        target="_blank"
                                                                                        class="badge bg-secondary text-white"
                                                                                        title="View Due">
                                                                                        <i class="fas fa-link"></i>
                                                                                    </a>
                                                                                @break

                                                                                @case('donation')
                                                                                    Donation @if ($payment->donation)
                                                                                        ({{ $payment->donation->title }})
                                                                                        <a href="{{ route('viewDonation', ['uuid' => $payment->donation->uuid]) }}"
                                                                                            target="_blank"
                                                                                            class="badge bg-secondary text-white"
                                                                                            title="View Donation">
                                                                                            <i class="fas fa-link"></i>
                                                                                        </a>
                                                                                    @endif
                                                                                @break

                                                                                @case('invoice')
                                                                                    Invoice
                                                                                    @if ($payment->invoice)
                                                                                        ({{ \Illuminate\Support\Str::words($payment->invoice->description, 5, '...') }})
                                                                                        <a href="{{ route('viewInvoice', ['uuid' => $payment->invoice->uuid]) }}"
                                                                                            target="_blank"
                                                                                            class="badge bg-secondary text-white"
                                                                                            title="View Invoice">
                                                                                            <i class="fas fa-link"></i>
                                                                                        </a>
                                                                                    @endif
                                                                                @break

                                                                                @case('election_form')
                                                                                    Election Form @if ($payment->election)
                                                                                        (Candidacy Fee:
                                                                                        {{ $payment?->electionCandidateApplication?->position?->position ?? 'Position' }}
                                                                                        — {{ $payment->election->title }}
                                                                                        ({{ $payment->election->academic_session }}/{{ $payment->election->academic_session + 1 }}))
                                                                                        <a href="{{ route('viewElectionCandidateApplication', ['uuid' => $payment->electionCandidateApplication->uuid]) }}"
                                                                                            target="_blank"
                                                                                            class="badge bg-secondary text-white"
                                                                                            title="View Donation">
                                                                                            <i class="fas fa-link"></i>
                                                                                        </a>
                                                                                    @endif
                                                                                @break

                                                                                @case('voting')
                                                                                    Voting
                                                                                    @if ($payment->monetaryVote)
                                                                                        -
                                                                                        {{ $payment->monetaryVote->no_of_votes }}
                                                                                        votes for
                                                                                        {{ $payment->monetaryVote->thePosition }} —
                                                                                        {{ $payment->election->title }}
                                                                                        ({{ $payment->election->academic_session }}/{{ $payment->election->academic_session + 1 }})
                                                                                    @endif
                                                                                @break

                                                                                @default
                                                                            @endswitch
                                                                        </td>
                                                                        <td class="text-start">
                                                                            ₦{{ number_format($payment->amount_payable, 2) }}
                                                                        </td>
                                                                        <td class="text-start">
                                                                            @if ($payment->registeredPayer)
                                                                                <div
                                                                                    class="d-flex align-items-center gap-3">
                                                                                    {{-- Avatar --}}
                                                                                    <div class="flex-shrink-0">
                                                                                        @php
                                                                                            $user =
                                                                                                $payment->registeredPayer;
                                                                                        @endphp
                                                                                        @include('users.admin.userManagement.members.include.profilePictureAndAvatarDisplay')
                                                                                    </div>

                                                                                    {{-- Name & Code --}}
                                                                                    <div
                                                                                        class="flex-grow-1 min-width-0 text-center">
                                                                                        <p
                                                                                            class="fw-bold mb-0 text-truncate text-capitalize">
                                                                                            @php
                                                                                                $member =
                                                                                                    $payment
                                                                                                        ->registeredPayer
                                                                                                        ->memberAcademicInfo;
                                                                                                $middleInitial = $member
                                                                                                    ->userProfile
                                                                                                    ->memberPersonalProfile
                                                                                                    ->middle_name
                                                                                                    ? strtoupper(
                                                                                                            substr(
                                                                                                                $member
                                                                                                                    ->userProfile
                                                                                                                    ->memberPersonalProfile
                                                                                                                    ->middle_name,
                                                                                                                0,
                                                                                                                1,
                                                                                                            ),
                                                                                                        ) . '. '
                                                                                                    : '';
                                                                                            @endphp

                                                                                            {{ strtolower($member->userProfile->memberPersonalProfile->first_name) . ' ' . $middleInitial . strtolower($member->userProfile->memberPersonalProfile->last_name) }}
                                                                                        </p>
                                                                                        <p
                                                                                            class="text-muted mb-0 small text-truncate">
                                                                                            {{ strtoupper($member->matric_number) }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                {{ $payment->non_registered_payer }}
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ \Carbon\Carbon::parse($payment->paid_at)->format('l, d F, Y h:i A') }}
                                                                        </td>
                                                                        <td>
                                                                            @if ($payment->payment_mode == 'manual')
                                                                                @php
                                                                                    $methods = [
                                                                                        'cash' => 'Cash',
                                                                                        'bank_transfer' =>
                                                                                            'Bank Transfer/Deposit',
                                                                                        'cheque' => 'Cheque',
                                                                                    ];
                                                                                @endphp

                                                                                {{ $methods[$payment->manual_payment_method] ?? ucfirst(str_replace('_', ' ', $payment->manual_payment_method)) }}
                                                                            @endif
                                                                            @if ($payment->payment_mode == 'electronic')
                                                                                <span class="text-capitalize">
                                                                                    {{ $payment->electronic_payment_method }}
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @switch($payment->payment_status)
                                                                                @case('successful')
                                                                                    <span
                                                                                        class="badge bg-green text-white">Successful</span>
                                                                                @break

                                                                                @case('failed')
                                                                                    <span
                                                                                        class="badge bg-red text-white">Failed</span>
                                                                                @break

                                                                                @case('pending')
                                                                                    <span
                                                                                        class="badge bg-yellow text-black">Pending</span>
                                                                                @break

                                                                                @default
                                                                                    <span
                                                                                        class="badge bg-yellow text-black">{{ $payment->payment_status }}</span>
                                                                            @endswitch

                                                                            @switch($payment->approval_status)
                                                                                @case('approved')
                                                                                    <span
                                                                                        class="badge bg-green text-white">Approved</span>
                                                                                @break

                                                                                @case('declined')
                                                                                    <span
                                                                                        class="badge bg-red text-white">Declined</span>
                                                                                @break

                                                                                @case('pending')
                                                                                    <span
                                                                                        class="badge bg-yellow text-black">Pending</span>
                                                                                @break

                                                                                @default
                                                                                    <span
                                                                                        class="badge bg-yellow text-black">Unknown</span>
                                                                            @endswitch

                                                                            @if ($payment->payment_status !== 'successful')
                                                                                @switch($payment->electronic_payment_method)
                                                                                    @case('flutterwave')
                                                                                        <form
                                                                                            action="{{ route('flutterwavePaymentCallback') }}"
                                                                                            method="GET" class="d-inline">
                                                                                            @csrf
                                                                                            <input type="hidden"
                                                                                                name="transaction_id"
                                                                                                value="{{ $payment->service_provider_id }}">
                                                                                            <input type="hidden" name="tx_ref"
                                                                                                value="{{ $payment->payment_transaction_id }}">
                                                                                            <button type="submit"
                                                                                                class="border-0 bg-transparent p-0"><i
                                                                                                    class="fa-solid fa-rotate-right text-primary"></i></button>
                                                                                        </form>
                                                                                    @break

                                                                                    @case('paystack')
                                                                                        <form
                                                                                            action="{{ route('paystackPaymentCallback', ['transaction_id' => $payment->payment_transaction_id]) }}"
                                                                                            method="GET" class="d-inline">
                                                                                            @csrf
                                                                                            <input type="hidden" name="reference"
                                                                                                value="{{ $payment->paystack_reference }}">
                                                                                            <button type="submit"
                                                                                                class="border-0 bg-transparent p-0"><i
                                                                                                    class="fa-solid fa-rotate-right text-primary"></i></button>
                                                                                        </form>
                                                                                    @break

                                                                                    @default
                                                                                @endswitch
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <a class="btn btn-primary btn-sm"
                                                                                href="{{ route('viewPaymentTransaction', ['payment_transaction_id' => $payment->payment_transaction_id]) }}">View</a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        @endif
                                                    @endif
                                                    {{-- payment transactions end --}}

                                                    {{-- financial claim start --}}
                                                    @if ($data['pageType'] == 'claims_page')
                                                        @if ($data['financialClaims']->isEmpty())
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="9" class="text-center">
                                                                        <h3 class="p-1 m-1 text-red">
                                                                            No Records Found!
                                                                        </h3>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        @else
                                                            <tbody>
                                                                @foreach ($data['financialClaims'] as $claim)
                                                                    <tr class=" align-middle text-center">
                                                                        <th>{{ $loop->iteration }}</th>
                                                                        <td>
                                                                            @switch($claim->claim_type)
                                                                                @case('credit')
                                                                                    <span
                                                                                        class="badge bg-success-subtle text-success">Credit</span>
                                                                                @break

                                                                                @case('debit')
                                                                                    <span
                                                                                        class="badge bg-danger-subtle text-danger">Debit</span>
                                                                                @break

                                                                                @default
                                                                                    <span
                                                                                        class="badge bg-warning-subtle text-warning">{{ $claim->claim_type }}</span>
                                                                            @endswitch
                                                                        </td>
                                                                        <td>
                                                                            ₦{{ number_format($claim->amount, 2) }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $claim->description }}
                                                                        </td>
                                                                        <td>
                                                                            {{ \Carbon\Carbon::parse($claim->date)->format('l, d F, Y') }}
                                                                        </td>
                                                                        <td>
                                                                            {{ \Carbon\Carbon::parse($claim->created_at)->format('l, d F, Y h:i A') }}
                                                                        </td>
                                                                        <td>
                                                                            @switch($claim->approval_status)
                                                                                @case('approved')
                                                                                    <span
                                                                                        class="badge bg-success-subtle text-success">Approved</span>
                                                                                @break

                                                                                @case('declined')
                                                                                    <span
                                                                                        class="badge bg-danger-subtle text-danger">Declined</span>
                                                                                @break

                                                                                @case('pending')
                                                                                    <span
                                                                                        class="badge bg-warning-subtle text-warning">Pending</span>
                                                                                @break

                                                                                @default
                                                                                    <span
                                                                                        class="badge bg-warning-subtle text-warning">{{ $claim->approval_status }}</span>
                                                                            @endswitch
                                                                        </td>
                                                                        <td>
                                                                            <a class="btn btn-primary btn-sm"
                                                                                href="{{ route('viewFinancialClaim', ['uuid' => $claim->uuid]) }}">View</a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        @endif
                                                    @endif
                                                    {{-- financial claim end --}}
                                                </table>
                                                {{-- Pagination --}}
                                                @php
                                                    if ($data['pageType'] == 'transactions_page') {
                                                        $paginatedArray = $data['paymentTransactions'];
                                                    } elseif ($data['pageType'] == 'claims_page') {
                                                        $paginatedArray = $data['financialClaims'];
                                                    }
                                                @endphp

                                                @include('layouts.include.pagination')

                                                <div class="px-5">
                                                    <form action="{{ route('downloadFinancialReports') }}"
                                                        method="GET">
                                                        @csrf

                                                        <input type="hidden" class="form-control" name="start_date"
                                                            value="{{ $data['startDate'] }}" required>
                                                        <input type="hidden" class="form-control" name="end_date"
                                                            value="{{ $data['endDate'] }}" required>

                                                        <button class="btn w-100 btn-danger mt-5"> <i
                                                                class="fas fa-download me-2"></i>
                                                            Download Report</button>
                                                    </form>
                                                </div>
                                            </div>


                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
