@extends('layouts.userLayout')
@section('title', 'Dashboard')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <h1 class="app-page-title mb-0">Dashboard</h1>

            {{-- header start --}}
            <header class="dashboard-header">
                <div class="dashboard-header-inner">
                    <div class="dashboard-logos">
                        <img src="{{ associationLogo() }}" alt="{{ basicSystemInfo()->association_name }} Logo"
                            class="logo-association">
                        <img src="{{ schoolLogo() }}" alt="School Logo" class="logo-school">
                    </div>

                    <div class="dashboard-title">
                        <h1 class="dashboard-title-text">
                            {{ basicSystemInfo()->association_name }}
                            @if (basicSystemInfo()->association_acronym)
                                <span class="dashboard-acronym">({{ basicSystemInfo()->association_acronym }})</span>
                            @endif
                            @if (basicSystemInfo()->school_acronym)
                                <span class="dashboard-chapter"> - {{ basicSystemInfo()->school_acronym }} Chapter</span>
                            @endif
                        </h1>
                    </div>
                </div>
            </header>
            {{-- header end --}}

            {{-- static member stat start --}}
            <section class="dashboard-cards">
                <h5 class="dashboard-section-title">User Stat</h5>

                <div class="cards-grid">
                    {{-- Active Student Members --}}
                    <div class="dashboard-card card-accent-primary">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Active Student Members</div>
                            <div class="card-value">{{ number_format($data['activeStudentMembersAccounts'] ?? 0) }}</div>
                        </div>
                    </div>

                    {{-- Active Student Members --}}
                    <div class="dashboard-card card-accent-success">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Alumni</div>
                            <div class="card-value">{{ number_format($data['graduates'] ?? 0) }}</div>
                        </div>
                    </div>

                    {{-- Awaiting Registration --}}
                    <div class="dashboard-card card-accent-warning">
                        <div class="card-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Awaiting Registration</div>
                            <div class="card-value">{{ number_format($data['memberAccountsAwaitingRegistration'] ?? 0) }}
                            </div>
                        </div>
                    </div>

                    {{-- Awaiting Verification --}}
                    <div class="dashboard-card card-accent-info">
                        <div class="card-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Awaiting Verification</div>
                            <div class="card-value">{{ number_format($data['memberAccountsAwaitingVerification'] ?? 0) }}
                            </div>
                        </div>
                    </div>

                    {{-- Inactive Student Accounts --}}
                    <div class="dashboard-card card-accent-danger">
                        <div class="card-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Inactive Member Accounts</div>
                            <div class="card-value">{{ number_format($data['inactiveStudentAccounts'] ?? 0) }}</div>
                        </div>
                    </div>

                    {{-- Readmissions Pending --}}
                    <div class="dashboard-card card-accent-warning">
                        <div class="card-icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Readmissions Pending</div>
                            <div class="card-value">{{ number_format($data['readmissionsPendingApproval'] ?? 0) }}</div>
                        </div>
                    </div>

                    {{-- Administrators --}}
                    <div class="dashboard-card card-accent-secondary">
                        <div class="card-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Active Administrators</div>
                            <div class="card-value">{{ number_format($data['activeAdministrators'] ?? 0) }} <span
                                    class="fs-6">/ {{ number_format($data['administrators'] ?? 0) }}</span></div>
                        </div>
                    </div>

                </div>
            </section>
            {{-- static member stat end --}}

            {{-- finance stat start --}}
            <section class="dashboard-cards finance-stats-section">
                <h5 class="dashboard-section-title">Finance Overview</h5>
                <div class="cards-grid">
                    {{-- Account Balance --}}
                    <div class="dashboard-card card-accent-primary">
                        <div class="card-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Account Balance</div>
                            <div class="card-value-wrap">
                                <div class="card-value balance-text" id="accountBalanceText">
                                    ₦{{ number_format($data['accountBalance'] ?? 0, 2) }}
                                </div>
                                <button type="button" class="balance-toggle" id="balanceToggle" title="Show/Hide balance">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <script>
                        (function() {
                            const balanceText = document.getElementById('accountBalanceText');
                            const toggleBtn = document.getElementById('balanceToggle');
                            const icon = toggleBtn.querySelector('i');
                            const fullValue = balanceText.textContent.trim();

                            let visible = false;

                            function setMasked() {
                                balanceText.textContent = '***';
                                icon.classList.remove('fa-eye-slash');
                                icon.classList.add('fa-eye');
                            }

                            function setShown() {
                                balanceText.textContent = fullValue;
                                icon.classList.remove('fa-eye');
                                icon.classList.add('fa-eye-slash');
                            }

                            // Start masked
                            setMasked();

                            toggleBtn.addEventListener('click', function() {
                                visible = !visible;
                                if (visible) {
                                    setShown();
                                } else {
                                    setMasked();
                                }
                            });
                        })();
                    </script>


                    {{-- Financial Claims Pending --}}
                    <div class="dashboard-card card-accent-warning">
                        <div class="card-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Claims Pending Approval</div>
                            <div class="card-value">{{ number_format($data['financialClaimsPendingApproval'] ?? 0) }}</div>
                        </div>
                    </div>

                    {{-- Dues for Session --}}
                    <div class="dashboard-card card-accent-info">
                        <div class="card-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Dues for Session</div>
                            <div class="card-value">{{ number_format($data['duesForSession'] ?? 0) }}</div>
                        </div>
                    </div>

                    {{-- Ongoing Donations --}}
                    <div class="dashboard-card card-accent-success">
                        <div class="card-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Ongoing Donations</div>
                            <div class="card-value">{{ number_format($data['ongoingDonations'] ?? 0) }}</div>
                        </div>
                    </div>

                    {{-- Invoices Pending Payment --}}
                    <div class="dashboard-card card-accent-danger">
                        <div class="card-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-label">Invoices Pending Payment</div>
                            <div class="card-value">{{ number_format($data['invoicePendingPayments'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </section>
            {{-- finance stat end --}}

            <section class="dashboard-cards members-analytics-section">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h5 class="dashboard-section-title mb-0">Members Analytics</h5>

                    <button type="button" class="btn btn-outline-primary" data-toggle="collapse"
                        data-target="#membersAnalyticsFilters">
                        <i class="fas fa-filter me-1"></i> Filter Analytics
                    </button>
                </div>

                <div class="collapse show" id="membersAnalyticsFilters">
                    <div class="dashboard-card mb-3">
                        <form method="GET" action="{{ url()->current() }}" class="row g-3 p-3">
                            {{-- Preserve any existing unrelated query parameters if needed. --}}

                            <div class="col-md-3">
                                <label class="form-label">Academic Year</label>
                                <select class="form-select" name="analytics_year">
                                    @foreach ($data['analyticsYears'] as $year)
                                        <option value="{{ $year }}"
                                            {{ (int) $data['selectedAnalyticsYear'] === (int) $year ? 'selected' : '' }}>
                                            {{ $year }}/{{ $year + 1 }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Academic Status</label>
                                <select class="form-select" name="analytics_status">
                                    <option value="">Students in school</option>
                                    <option value="student"
                                        {{ $data['selectedAnalyticsStatus'] === 'student' ? 'selected' : '' }}>Student
                                    </option>
                                    <option value="extra_year"
                                        {{ $data['selectedAnalyticsStatus'] === 'extra_year' ? 'selected' : '' }}>Extra
                                        Year</option>
                                    <option value="alumni"
                                        {{ $data['selectedAnalyticsStatus'] === 'alumni' ? 'selected' : '' }}>Alumni
                                    </option>
                                    <option value="withdrawn"
                                        {{ $data['selectedAnalyticsStatus'] === 'withdrawn' ? 'selected' : '' }}>Withdrawn
                                    </option>
                                    <option value="suspended"
                                        {{ $data['selectedAnalyticsStatus'] === 'suspended' ? 'selected' : '' }}>Suspended
                                    </option>
                                    <option value="expelled"
                                        {{ $data['selectedAnalyticsStatus'] === 'expelled' ? 'selected' : '' }}>Expelled
                                    </option>
                                    <option value="unknown"
                                        {{ $data['selectedAnalyticsStatus'] === 'unknown' ? 'selected' : '' }}>Unknown
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Faculty</label>
                                <select class="form-select" id="analytics-faculty-select" name="analytics_faculty">
                                    <option value="">All faculties</option>
                                    @foreach ($data['analyticsFaculties'] as $faculty)
                                        <option value="{{ $faculty->slug }}"
                                            {{ $data['selectedAnalyticsFaculty'] === $faculty->slug ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Department</label>
                                <select class="form-select" id="analytics-department-select" name="analytics_department"
                                    disabled>
                                    <option value="">Select faculty first</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Programme</label>
                                <select class="form-select" id="analytics-program-select" name="analytics_program">
                                    <option value="">All programmes</option>
                                    @foreach ($data['analyticsPrograms'] as $program)
                                        <option value="{{ $program->slug }}"
                                            {{ $data['selectedAnalyticsProgram'] === $program->slug ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Programme Type</label>
                                <select class="form-select" id="analytics-program-type-select"
                                    name="analytics_program_type" disabled>
                                    <option value="">Select programme first</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="analytics_gender">
                                    <option value="">All genders</option>
                                    <option value="male"
                                        {{ $data['selectedAnalyticsGender'] === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female"
                                        {{ $data['selectedAnalyticsGender'] === 'female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="green-button submit-btn w-100">
                                    <i class="fas fa-chart-bar me-1"></i> Apply
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-outline-danger w-100">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="cards-grid mb-3">
                    <div class="dashboard-card card-accent-primary">
                        <div class="card-icon"><i class="fas fa-users"></i></div>
                        <div class="card-content">
                            <div class="card-label">Students in {{ $data['membersAnalytics']['session_label'] }}</div>
                            <div class="card-value">{{ number_format($data['membersAnalytics']['total_students']) }}</div>
                        </div>
                    </div>

                    <div class="dashboard-card card-accent-info">
                        <div class="card-icon"><i class="fas fa-filter"></i></div>
                        <div class="card-content">
                            <div class="card-label">Filtered Population</div>
                            <div class="card-value">{{ number_format($data['membersAnalytics']['total_displayed']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="dashboard-card h-100">
                            <div class="card-content w-100">
                                <div class="card-label">Students by Level</div><canvas id="analyticsByLevel"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="dashboard-card h-100">
                            <div class="card-content w-100">
                                <div class="card-label">Students by Gender</div><canvas id="analyticsByGender"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="dashboard-card h-100">
                            <div class="card-content w-100">
                                <div class="card-label">Students by Programme</div><canvas
                                    id="analyticsByProgram"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="dashboard-card h-100">
                            <div class="card-content w-100">
                                <div class="card-label">Students by Programme Type</div><canvas
                                    id="analyticsByProgramType"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="dashboard-card">
                            <div class="card-content w-100">
                                <div class="card-label">Academic Status Distribution</div><canvas
                                    id="analyticsByStatus"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="dashboard-card h-100 w-100">
                            <div class="card-content w-100">
                                <div class="card-label">Students by Faculty</div><canvas id="analyticsByFaculty"></canvas>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-12">
                        <div class="dashboard-card h-100">
                            <div class="card-content w-100">
                                <div class="card-label">Students by Department</div><canvas
                                    id="analyticsByDepartment"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const analytics = @json($data['membersAnalytics'] ?? []);

                    const palette = [
                        '#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed',
                        '#0891b2', '#db2777', '#65a30d', '#ea580c', '#4f46e5'
                    ];

                    function renderChart(canvasId, rows, type = 'bar', horizontal = false) {
                        const canvas = document.getElementById(canvasId);
                        if (!canvas || !rows) return;

                        new Chart(canvas, {
                            type: type,
                            data: {
                                labels: rows.map(row => row.label),
                                datasets: [{
                                    label: 'Members',
                                    data: rows.map(row => row.count),
                                    backgroundColor: rows.map((_, index) => palette[index % palette
                                        .length] + 'B3'),
                                    borderColor: rows.map((_, index) => palette[index % palette.length]),
                                    borderWidth: 1,
                                    borderRadius: type === 'bar' ? 5 : 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                indexAxis: horizontal ? 'y' : 'x',
                                plugins: {
                                    legend: {
                                        display: type === 'doughnut' || type === 'pie'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: context =>
                                                `${context.label}: ${Number(context.raw).toLocaleString()} member(s)`
                                        }
                                    }
                                },
                                scales: type === 'doughnut' || type === 'pie' ? {} : {
                                    x: {
                                        ticks: {
                                            autoSkip: false,
                                            maxRotation: 50,
                                            minRotation: 0
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        });
                    }

                    renderChart('analyticsByLevel', analytics.by_level, 'bar');
                    renderChart('analyticsByGender', analytics.by_gender, 'doughnut');
                    renderChart('analyticsByFaculty', analytics.by_faculty, 'bar', true);
                    renderChart('analyticsByDepartment', analytics.by_department, 'bar', true);
                    renderChart('analyticsByProgram', analytics.by_program, 'bar', true);
                    renderChart('analyticsByProgramType', analytics.by_program_type, 'doughnut');
                    renderChart('analyticsByStatus', analytics.by_status, 'bar');

                    // Faculty → Department
                    const facultySelect = document.getElementById('analytics-faculty-select');
                    const departmentSelect = document.getElementById('analytics-department-select');
                    const selectedDepartment = @json($data['selectedAnalyticsDepartment'] ?? '');

                    function loadAnalyticsDepartments(facultySlug, selected = '') {
                        if (!facultySlug) {
                            departmentSelect.innerHTML = '<option value="">Select faculty first</option>';
                            departmentSelect.disabled = true;
                            return;
                        }

                        departmentSelect.disabled = true;
                        departmentSelect.innerHTML = '<option value="">Loading...</option>';

                        fetch("{{ route('fetchDepartments') }}?faculty_slug=" + encodeURIComponent(facultySlug))
                            .then(response => {
                                if (!response.ok) throw new Error('Unable to load departments');
                                return response.json();
                            })
                            .then(response => {
                                departmentSelect.innerHTML = '<option value="">All departments</option>';

                                (response.departments || []).forEach(department => {
                                    const option = document.createElement('option');
                                    option.value = department.slug;
                                    option.textContent = department.name;
                                    option.selected = selected === department.slug;
                                    departmentSelect.appendChild(option);
                                });

                                departmentSelect.disabled = false;
                            })
                            .catch(() => {
                                departmentSelect.innerHTML = '<option value="">Unable to load departments</option>';
                                departmentSelect.disabled = false;
                            });
                    }

                    if (facultySelect && departmentSelect) {
                        facultySelect.addEventListener('change', function() {
                            loadAnalyticsDepartments(this.value);
                        });

                        if (facultySelect.value) {
                            loadAnalyticsDepartments(facultySelect.value, selectedDepartment);
                        }
                    }

                    // Programme → Programme Type
                    const programSelect = document.getElementById('analytics-program-select');
                    const programTypeSelect = document.getElementById('analytics-program-type-select');
                    const selectedProgramType = @json($data['selectedAnalyticsProgramType'] ?? '');

                    function loadAnalyticsProgramTypes(programSlug, selected = '') {
                        if (!programSlug) {
                            programTypeSelect.innerHTML = '<option value="">Select programme first</option>';
                            programTypeSelect.disabled = true;
                            return;
                        }

                        programTypeSelect.disabled = true;
                        programTypeSelect.innerHTML = '<option value="">Loading...</option>';

                        fetch("{{ route('fetchProgramTypes') }}?program_slug=" + encodeURIComponent(programSlug))
                            .then(response => {
                                if (!response.ok) throw new Error('Unable to load programme types');
                                return response.json();
                            })
                            .then(response => {
                                programTypeSelect.innerHTML = '<option value="">All programme types</option>';

                                (response.programTypes || []).forEach(type => {
                                    const option = document.createElement('option');
                                    option.value = type.slug;
                                    option.textContent = type.type_name;
                                    option.selected = selected === type.slug;
                                    programTypeSelect.appendChild(option);
                                });

                                programTypeSelect.disabled = false;
                            })
                            .catch(() => {
                                programTypeSelect.innerHTML =
                                    '<option value="">Unable to load programme types</option>';
                                programTypeSelect.disabled = false;
                            });
                    }

                    if (programSelect && programTypeSelect) {
                        programSelect.addEventListener('change', function() {
                            loadAnalyticsProgramTypes(this.value);
                        });

                        if (programSelect.value) {
                            loadAnalyticsProgramTypes(programSelect.value, selectedProgramType);
                        }
                    }
                });
            </script>

            {{-- script --}}
            {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
            {{-- script --}}
            {{-- <div class="col-md-12 w-100">
                <div class="container mt-5">
                    <div class="row row-cols-1 row-cols-md-3 g-4 wallet_cards_container">
                        <!-- Wallet Balance Card -->
                        <div class="col">
                            <div class="card shadow-sm wallet_card balance_card">
                                <div class="card-body">
                                    <i class="fas fa-wallet fa-2x"></i>
                                    <h5 class="card-title">Total Wallet Funds</h5>
                                    <p class="card-text p-0 m-0">
                                        <span class="fw-bold">₦{{ number_format(totalWalletFunds(), 2) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm wallet_card balance_card">
                                <div class="card-body">
                                    <i class="fas fa-dollar fa-2x"></i>
                                    <p class="card-title m-0 p-0" style="font-size: 1.2rem">
                                        ₦{{ number_format(totalSalesAmount(), 2) }}
                                    </p>
                                    <h6 class="card-title">From {{ totalOrders() }} orders</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-toggle="modal" data-target="#">
                            <div class="card shadow-sm wallet_card">
                                <div class="card-body">
                                    <p class="card-title m-0 p-0" style="font-size: 3.5rem">{{ paymentsAwaitingApproval() }}
                                    </p>
                                    <p class="card-text p-0 m-0">
                                        <span class="fw-bold">Payment(s) Awaiting Approval</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-toggle="modal" data-target="#">
                            <div class="card shadow-sm wallet_card">
                                <div class="card-body">
                                    <p class="card-title m-0 p-0" style="font-size: 3.5rem">{{ countAdministrators() }}</p>
                                    <p class="card-text p-0 m-0">
                                        <span class="fw-bold">Administrator(s)</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-toggle="modal" data-target="#">
                            <div class="card shadow-sm wallet_card">
                                <div class="card-body">
                                    <p class="card-title m-0 p-0" style="font-size: 3.5rem">{{ countCustomers() }}</p>
                                    <p class="card-text p-0 m-0">
                                        <span class="fw-bold">Customers</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-toggle="modal" data-target="#">
                            <div class="card shadow-sm wallet_card">
                                <div class="card-body">
                                    <p class="card-title m-0 p-0" style="font-size: 3.5rem">{{ countCategories() }}</p>
                                    <p class="card-text p-0 m-0">
                                        <span class="fw-bold">Categories</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-toggle="modal" data-target="#">
                            <div class="card shadow-sm wallet_card">
                                <div class="card-body">
                                    <p class="card-title m-0 p-0" style="font-size: 3.5rem">{{ countActiveProducts() }}</p>
                                    <p class="card-text p-0 m-0">
                                        <span class="fw-bold">Active Product(s)</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-toggle="modal" data-target="#">
                            <div class="card shadow-sm wallet_card">
                                <div class="card-body">
                                    <p class="card-title m-0 p-0" style="font-size: 3.5rem">{{ outOfStockProducts() }}</p>
                                    <p class="card-text p-0 m-0">
                                        <span class="fw-bold">Out of Stock Product(s)</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    .balance_card {
                        background: #152238 !important;
                        color: #ffffff !important;
                    }

                    .balance_card>.card-body>i {
                        color: #23395d !important;
                    }

                    .wallet_card {
                        background: #d8ebfb;
                        color: #152238;
                        border: none;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        min-height: 150px;
                        text-align: center;
                        padding-top: 5px;
                    }

                    .wallet_card:hover {
                        background: #203354;
                        color: #8dc7fb;
                        transition: all 0.3s ease-in-out;
                    }


                    .wallet_card i {
                        margin-bottom: 10px;
                    }
                </style>

            </div> --}}
            <hr>



            {{-- <h5>Orders</h5> --}}
            {{-- <div>
                <canvas id="analyticsChart" width="400" height="200"></canvas>

                <form class="form my_form" method="POST" enctype="multipart/form-data" id="analyticsForm">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                            <p style="color: red">{{ $errors->first('start_date') }}</p>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                            <p style="color: red">{{ $errors->first('end_date') }}</p>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-start mt-3">
                            <div class="m-1">
                                <button type="submit" class="btn btn-warning">
                                    Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div> --}}



            {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}

            {{-- <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // defauld - all time
                    fetchAnalyticsData(null, null);
                });

                document.getElementById('analyticsForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(e.target);
                    const startDate = formData.get('start_date');
                    const endDate = formData.get('end_date');

                    // Fetch filtered analytics data
                    fetchAnalyticsData(startDate, endDate);
                });

                function fetchAnalyticsData(startDate = null, endDate = null) {
                    fetch("{{ route('getOrdersAnalytics') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                start_date: startDate,
                                end_date: endDate,
                                successfulOrders: @json($data['successfulOrders']), // Pass your array here
                            }),
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            const labels = Object.keys(data); // Dates or periods
                            const values = Object.values(data); // Total amount paid

                            // Render the chart
                            const ctx = document.getElementById('analyticsChart').getContext('2d');
                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Total Amount Paid (₦)',
                                        data: values,
                                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1,
                                    }, ],
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                        },
                                    },
                                },
                            });
                        })
                        .catch((error) => console.error('Error:', error));
                }
            </script> --}}


        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
