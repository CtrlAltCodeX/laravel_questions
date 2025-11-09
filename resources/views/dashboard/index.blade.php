@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    /* Force both graph boxes side by side */
    .analytics-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: nowrap;
    }

    .analytics-box {
        flex: 1;
        min-width: 48%;
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container mx-auto p-6">
    <!-- Stats Boxes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Categories</h5>
            <p class="text-4xl font-semibold">{{ $categoriesCount }}</p>
        </div>

        <div class="bg-green-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total SubCategories</h5>
            <p class="text-4xl font-semibold">{{ $subCategoriesCount }}</p>
        </div>

        <div class="bg-yellow-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Subjects</h5>
            <p class="text-4xl font-semibold">{{ $subjectsCount }}</p>
        </div>

          <div class="bg-yellow-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Videos</h5>
            <p class="text-4xl font-semibold">{{ $videoCount }}</p>
        </div>

        <div class="bg-teal-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Topics</h5>
            <p class="text-4xl font-semibold">{{ $topicsCount }}</p>
        </div>

        <div class="bg-red-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Questions</h5>
            <p class="text-4xl font-semibold">{{ $questionsCount }}</p>
        </div>

        <div class="bg-gray-700 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Users</h5>
            <p class="text-4xl font-semibold">{{ $usersCount }}</p>
        </div>

         <div class="bg-yellow-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Reports</h5>
            <p class="text-4xl font-semibold">{{ $reportCount }}</p>
        </div>
    </div>

    <!-- Both Analytics Side by Side -->
    <div class="mt-10 analytics-row">
        <!-- User Analytics -->
        <div class="analytics-box">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">User Analytics</h2>
                <div class="flex items-center gap-2">
                    <select id="userRange" class="border border-gray-300 rounded px-3 py-1 focus:outline-none">
                        <option value="7days" selected>Last 7 Days</option>
                        <option value="1month">Last 1 Month</option>
                        <option value="all">All Data</option>
                    </select>
                    <button id="exportBtn" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                        Export
                    </button>

                </div>
            </div>

            <canvas id="userAnalyticsChart" height="140"></canvas>
        </div>

        <!-- User Payment Analytics -->
        <div class="analytics-box">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">User Payment Analytics</h2>
                <select id="paymentRange" class="border border-gray-300 rounded px-3 py-1 focus:outline-none">
                    <option value="7days" selected>Last 7 Days</option>
                    <option value="1month">Last 1 Month</option>
                    <option value="all">All Data</option>
                </select>
            </div>
            <canvas id="userPaymentChart" height="140"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('userAnalyticsChart').getContext('2d');
        let userChart;

        function fetchUserAnalytics(range = '7days') {
            fetch(`/dashboard/user-analytics/${range}`)
                .then(res => res.json())
                .then(data => {
                    const labels = data.map(item => item.date);
                    const totals = data.map(item => item.total);

                    if (userChart) userChart.destroy();

                    userChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Users Registered',
                                data: totals,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 5,
                                        callback: function(value) {
                                            return value % 5 === 0 ? value : '';
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: (context) => `Total: ${context.parsed.y}`
                                    }
                                }
                            }
                        }
                    });
                });
        }

        fetchUserAnalytics();
        document.getElementById('userRange').addEventListener('change', function() {
            fetchUserAnalytics(this.value);
        });
        document.getElementById('exportBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const range = document.getElementById('userRange').value;
            const link = document.createElement('a');
            link.href = `/dashboard/user-analytics/export/file?range=${range}`;
            link.setAttribute('download', '');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });



        const paymentCtx = document.getElementById('userPaymentChart').getContext('2d');
        let paymentChart;

        function fetchPaymentAnalytics(range = '7days') {
            fetch(`/dashboard/user-payment-analytics/${range}`)
                .then(res => res.json())
                .then(data => {
                    const labels = data.map(item => item.date);
                    const courseCounts = data.map(item => item.courses);
                    const coinCounts = data.map(item => item.coins);

                    if (paymentChart) paymentChart.destroy();

                    paymentChart = new Chart(paymentCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Courses Purchased',
                                    data: courseCounts,
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    tension: 0.4,
                                    fill: true,
                                },
                                {
                                    label: 'Coins Added',
                                    data: coinCounts,
                                    borderColor: 'rgba(75, 192, 75, 1)',
                                    backgroundColor: 'rgba(75, 192, 75, 0.2)',
                                    tension: 0.4,
                                    fill: true,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            stacked: false,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: (context) => `${context.dataset.label}: ${context.parsed.y}`
                                    }
                                },
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                },
                                x: {
                                    ticks: {
                                        autoSkip: true,
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }
                            }
                        }
                    });
                });
        }

        fetchPaymentAnalytics();
        document.getElementById('paymentRange').addEventListener('change', function() {
            fetchPaymentAnalytics(this.value);
        });



    });
</script>
@endpush