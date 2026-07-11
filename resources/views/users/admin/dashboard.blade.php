@extends('layouts.userLayout')
@section('title', 'Dashboard')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">

            <h1 class="app-page-title mb-0">Dashboard</h1>


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



            <h5>Orders</h5>
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
