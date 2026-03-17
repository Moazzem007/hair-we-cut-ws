@extends('layouts.adminapp')

@section('Main-content')
    <div class="wrapper wrapper-content">
        <!-- Stat Cards -->
        <div class="row">
            <div class="col-lg-3">
                <div class="widget-stat stat-revenue">
                    <span class="stat-label">Total Revenue</span>
                    <h2 class="no-margins"><i class="fa fa-gbp"></i> {{ number_format($total_revenue, 2) }}</h2>
                    <div class="m-t-sm">
                        <span class="label" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 4px 12px;">Comm: <i class="fa fa-gbp"></i> {{ number_format($total_commission, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget-stat stat-users">
                    <span class="stat-label">Total Customers</span>
                    <h2 class="no-margins">{{ number_format($customers_count) }}</h2>
                    <div class="m-t-sm" style="font-size: 13px; opacity: 0.8; font-weight: 500;">Active users on platform</div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget-stat stat-barbers">
                    <span class="stat-label">Active Barbers</span>
                    <h2 class="no-margins">{{ number_format($barbers_count) }}</h2>
                    <div class="m-t-sm" style="font-size: 13px; opacity: 0.8; font-weight: 500;">Verified salon partners</div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget-stat stat-pending">
                    <span class="stat-label">Pending Approvals</span>
                    <h2 class="no-margins">{{ number_format(App\Models\Barber::whereIn('status', ['Pending', 'Pendding'])->count()) }}</h2>
                    <div class="m-t-sm">
                        <a href="{{ route('barbers.index', ['status' => 'Pending']) }}" class="btn btn-xs btn-white" style="color: var(--secondary); font-weight: 700; border-radius: 20px; padding: 5px 15px; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">Review Now</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-t-lg">
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title" style="background: transparent; border: none; padding: 20px 25px;">
                        <h5 style="font-weight: 700; color: var(--secondary);">Monthly Revenue</h5>
                    </div>
                    <div class="ibox-content" style="background: transparent; border: none;">
                        <div style="height: 250px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title" style="background: transparent; border: none; padding: 20px 25px;">
                        <h5 style="font-weight: 700; color: var(--secondary);">Booking Status Distribution</h5>
                    </div>
                    <div class="ibox-content" style="background: transparent; border: none;">
                        <div style="height: 250px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Previous Action Center relocated here -->
                <div class="premium-card m-b-lg" style="padding: 30px; background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%); color: white; border: none;">
                    <div class="row">
                        <div class="col-md-8">
                            <h2 style="font-weight: 700; margin-top: 0;">Partner Action Center</h2>
                            <p style="opacity: 0.9; font-size: 15px;">There are <strong>{{ App\Models\Barber::whereIn('status', ['Pending', 'Pendding'])->count() }}</strong> partners awaiting your review. Quick approval helps grow the platform faster.</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('barbers.index', ['status' => 'Pending']) }}" class="btn btn-lg btn-white" style="color: var(--primary); font-weight: 700; border-radius: 12px; padding: 12px 30px; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.2); margin-top: 10px;">Review Now</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="premium-card p-m" style="padding: 25px;">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-users fa-3x" style="color: var(--primary); opacity: 0.8;"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <span style="display: block; color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Total Users</span>
                                    <h2 class="font-bold" style="margin: 5px 0; font-size: 28px;">{{ $customers_count }}</h2>
                                    <small class="text-navy"><i class="fa fa-level-up"></i> {{ $new_users_30d }} new this month</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="premium-card p-m" style="padding: 25px;">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-cut fa-3x" style="color: #f59e0b; opacity: 0.8;"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <span style="display: block; color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Total Partners</span>
                                    <h2 class="font-bold" style="margin: 5px 0; font-size: 28px;">{{ $barbers_count }}</h2>
                                    <small class="text-warning"><i class="fa fa-clock-o"></i> {{ $new_barbers_30d }} recently joined</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Recent Messages</h5>
                        <span class="badge badge-info pull-right">{{ App\Models\Contactus::where('status', 1)->count() }}</span>
                    </div>
                    <div class="ibox-content">
                        <div class="feed-activity-list">
                            @foreach ($contactus as $message)
                                <div class="feed-element">
                                    <div class="media-body ">
                                        <small class="pull-right text-navy">{{ $message->created_at->diffForHumans() }}</small>
                                        <strong>{{ $message->name }}</strong> <br>
                                        <small class="text-muted">{{ $message->email }}</small>
                                        <div class="well m-t-xs" style="padding: 10px; border-radius: 8px; font-size: 12px; background: #f9f9f9; border: 1px solid #eee;">
                                            {{ Str::limit($message->message, 80) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('contactus.index') }}" class="btn btn-primary btn-block m-t">View All Messages</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script_code')
    <script>
        $(document).ready(function() {
            // Revenue Chart
            var revCtx = document.getElementById("revenueChart").getContext("2d");
            var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            var revenueData = {!! json_encode($monthly_revenue) !!};
            var revChartData = months.map(function(m, index) { return revenueData[index + 1] || 0; });

            new Chart(revCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: "Revenue (£)",
                        backgroundColor: "rgba(99, 102, 241, 0.1)",
                        borderColor: "#6366f1",
                        pointBackgroundColor: "#6366f1",
                        pointBorderColor: "#fff",
                        data: revChartData,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Status Distribution Chart
            var statusCtx = document.getElementById("statusChart").getContext("2d");
            var statusData = {!! json_encode($appointment_stats) !!};
            var labels = Object.keys(statusData);
            var values = Object.values(statusData);
            
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            '#6366f1', // Confirmed
                            '#10b981', // Completed
                            '#f59e0b', // Pending
                            '#ef4444', // Canceled
                            '#64748b'  // Others
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
@endsection
