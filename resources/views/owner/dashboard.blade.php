@extends('layouts.appOwner')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Owner Dashboard</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Widget Total Penjualan Hari Ini -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalSalesToday }}</h3>
                        <p>Orders Today</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                </div>
            </div>

            <!-- Widget Total Penjualan Bulan Ini -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalSalesMonth }}</h3>
                        <p>Orders This Month</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                </div>
            </div>

            <!-- Widget Pendapatan Hari Ini -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>Rp{{ number_format($revenueToday, 0, ',', '.') }}</h3>
                        <p>Revenue Today</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-cash"></i>
                    </div>
                </div>
            </div>

            <!-- Widget Pendapatan Bulan Ini -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>Rp{{ number_format($revenueMonth, 0, ',', '.') }}</h3>
                        <p>Revenue This Month</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-cash"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Penjualan Harian -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daily Sales</h3>
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('dailySalesChart').getContext('2d');
            var data = @json($dailySales);

            // Log data untuk debugging
            console.log('Daily Sales Data:', data);

            var labels = data.map(function(item) {
                return item.date;
            });
            var counts = data.map(function(item) {
                return item.count;
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Sales',
                        data: counts,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Sales'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
