<x-layout>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="dashboard-header">
            <div class="header-text">
                <h1>Dashboard Overview</h1>
                <p class="welcome-text">Welcome back! Here's what's happening with your store today.</p>
            </div>
            <div class="last-updated">
                <span>Last updated: {{ now()->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card blue-gradient">
                <div class="stat-content">
                    <div>
                        <p class="stat-label">Total Orders</p>
                        <p class="stat-value">{{ $totalOrders }}</p>
                    </div>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card green-gradient">
                <div class="stat-content">
                    <div>
                        <p class="stat-label">Products</p>
                        <p class="stat-value">{{ $totalProducts }}</p>
                    </div>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card purple-gradient">
                <div class="stat-content">
                    <div>
                        <p class="stat-label">Customers</p>
                        <p class="stat-value">{{ $totalUsers }}</p>
                    </div>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card orange-gradient">
                <div class="stat-content">
                    <div>
                        <p class="stat-label">Total Revenue</p>
                        <p class="stat-value">${{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3>Orders Per Day</h3>
                <div class="chart-container">
                    <canvas id="ordersChart" height="150"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>Order Status Distribution</h3>
                <div class="chart-container">
                    <canvas id="statusChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="table-card">
            <h3>Recent Orders</h3>
            <div class="table-container">
                <table id="mytable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Status</th>
                            <th>Customer ID</th>
                            <th>Product ID</th>
                            <th>Total Price</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order['id'] }}</td>
                                <td><span class="status-badge">{{ $order['status'] }}</span></td>
                                <td>{{ $order['buyer_id'] }}</td>
                                <td>{{ $order['product_infos']['product_id'] ?? 'N/A' }}</td>
                                <td>${{ number_format($order['total_amount'], 2) }}</td>
                                <td>{{ $order['seller_location'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Orders Per Day Chart (changed from sales/revenue)
        const ordersLabels = @json(array_keys($ordersPerDay ?? []));
        const ordersData = @json(array_values($ordersPerDay ?? []));

        new Chart(document.getElementById('ordersChart'), {
            type: 'bar',
            data: {
                labels: ordersLabels,
                datasets: [{
                    label: 'Number of Orders',
                    data: ordersData,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Orders'
                        },
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });

        // Status Distribution Chart (unchanged)
        const statusData = @json($orderStatusCounts ?? []);

        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    label: 'Orders by Status',
                    data: Object.values(statusData),
                    backgroundColor: [
                        '#2196F3', '#FF9800', '#4CAF50', '#F44336', '#9C27B0', '#E91E63'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                },
                cutout: '70%'
            }
        });
    </script>

    <style>
        /* Base Styles */
        .dashboard-container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }

        /* Header Styles */
        .dashboard-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-text h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            color: #2d3748;
        }

        .welcome-text {
            color: #718096;
            margin-top: 5px;
            font-size: 14px;
        }

        .last-updated span {
            font-size: 13px;
            color: #718096;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            border-radius: 12px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .blue-gradient {
            background: linear-gradient(135deg, #3B82F6, #1D4ED8);
        }

        .green-gradient {
            background: linear-gradient(135deg, #10B981, #059669);
        }

        .purple-gradient {
            background: linear-gradient(135deg, #8B5CF6, #7C3AED);
        }

        .orange-gradient {
            background: linear-gradient(135deg, #F59E0B, #D97706);
        }

        .stat-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin: 5px 0 0 0;
        }

        .stat-icon {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 12px;
            display: flex;
        }

        .stat-icon svg {
            width: 24px;
            height: 24px;
        }

        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .chart-card h3 {
            margin: 0 0 20px 0;
            font-size: 18px;
            color: #2d3748;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Table Styles */
      

        tr:hover {
            background-color:rgb(88, 129, 170);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background-color: #e2e8f0;
            color: #4a5568;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .last-updated {
                margin-top: 10px;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-layout>