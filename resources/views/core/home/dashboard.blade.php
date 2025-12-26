<x-layout>

    <!-- User Information Section -->

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="font-weight-bold mb-2">
                                @if(isset($user))
                                    {{ count($user) }}
                                @else
                                    0
                                @endif
                            </h2>
                            <div>Total Members</div>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="font-weight-bold mb-2">
                                @if(isset($performance))
                                    {{ $performance->count() }}
                                @else
                                    0
                                @endif
                            </h2>
                            <div>Total Performance</div>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="font-weight-bold mb-2">
                                @if(isset($payment))
                                    Rp {{ number_format($payment->sum('amount') ?? 0, 0, ',', '.') }}
                                @else
                                    Rp 0
                                @endif
                            </h2>
                            <div>Total Payment</div>
                        </div>
                        <div>
                            <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="font-weight-bold mb-2">
                                @if(isset($jadwal))
                                    {{ $jadwal->count() }}
                                @else
                                    0
                                @endif
                            </h2>
                            <div>Total Jadwal</div>
                        </div>
                        <div>
                            <i class="fas fa-calendar-alt fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Performance by Distance</h4>
                        <small class="text-muted">Select a specific user or choose "All Users" to view all performance data</small>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="form-group mb-0">
                            <select id="user" name="user" class="form-control" style="min-width: 150px;">
                                <option value="">All Users</option>
                                    @foreach($user as $userId => $userName)
                                        <option value="{{ $userId }}">{{ $userName }}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($performance) && $performance->count() > 0)
                        @php
                            // Group performance data by distance (jarak) like PublicController
                            $groupedPerformance = $performance->groupBy('jarak_nama');
                        @endphp

                        @foreach($groupedPerformance as $distance => $records)
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ $distance }} Performance Chart</h5>
                                        <small class="text-muted">{{ $records->count() }} records</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                                            <canvas id="dashboardChart-{{ Str::slug($distance) }}"></canvas>
                                        </div>
                                        <div class="row mt-3 text-center">
                                            <div class="col-4">
                                                <small class="text-muted">Total Records</small>
                                                <div class="h6" id="total-{{ Str::slug($distance) }}">{{ $records->count() }}</div>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted">Average Time</small>
                                                <div class="h6" id="avg-{{ Str::slug($distance) }}">{{ number_format($records->avg('race_waktu'), 2) }}s</div>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted">Best Time</small>
                                                <div class="h6" id="best-{{ Str::slug($distance) }}">{{ number_format($records->min('race_waktu'), 2) }}s</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Performance Data Available</h5>
                            <p class="text-muted">Performance charts will appear here when data is available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    @push('footer')

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Chart.js and plugins -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    <script>
        let charts = {};
        let performanceData = {};

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all components
            initializeCharts();
            handleUserSelection();
            initializePeityCharts();
        });

        function initializePeityCharts() {
            // Initialize the existing peity charts with better styling
            if (typeof $ !== 'undefined' && typeof $.fn.peity !== 'undefined') {
                $('.peity').each(function() {
                    const $this = $(this);
                    const type = $this.data('type') || 'pie';
                    $this.peity(type, {
                        radius: 30,
                        innerRadius: 20,
                        fill: ['#3f51b5', '#f44336', '#ff9800', '#4caf50', '#2196f3', '#9c27b0']
                    });
                });
            }
        }

        function handleUserSelection() {
            const userSelect = document.getElementById('user');
            if (userSelect) {
                // Set current selected value if exists in URL
                const urlParams = new URLSearchParams(window.location.search);
                const selectedUser = urlParams.get('user');
                if (selectedUser) {
                    userSelect.value = selectedUser;
                } else {
                    // Default to "All Users" if no user parameter in URL
                    userSelect.value = '';
                }

                userSelect.addEventListener('change', function() {
                    const selectedValue = this.value;

                    // Update URL without reloading page
                    const currentUrl = new URL(window.location);
                    if (selectedValue && selectedValue !== '') {
                        currentUrl.searchParams.set('user', selectedValue);
                    } else {
                        currentUrl.searchParams.delete('user');
                    }
                    window.history.replaceState({}, '', currentUrl);

                    // Update charts and statistics dynamically
                    updateChartsForUser(selectedValue);
                    updateStatistics(selectedValue);
                });
            }
        }

        function initializeCharts() {
            try {
                // Performance data from PHP grouped by distance
                performanceData = @json($performance->groupBy('jarak_nama') ?? []);

                // Create charts for each distance group (using line charts)
                Object.keys(performanceData).forEach(distance => {
                    createChartForDistance(distance);
                });

            } catch (error) {
                console.error('Error initializing charts:', error);
            }
        }

        function createChartForDistance(distance, chartType = 'line') {
            const canvasId = `dashboardChart-${distance.toLowerCase().replace(/\s+/g, '-')}`;
            const ctx = document.getElementById(canvasId);

            if (!ctx) return;

            // Destroy existing chart
            if (charts[distance]) {
                charts[distance].destroy();
            }

            const records = performanceData[distance] || [];

            // Get unique dates for x-axis labels
            const uniqueDates = records.length > 0 ? [...new Set(records.map(record => record.race_tanggal))] : [];

            // Group records by user for multi-user display
            const userGroups = {};
            records.forEach(record => {
                const userId = record.race_user_id;
                const userName = record.name || 'User ' + userId;
                if (!userGroups[userName]) {
                    userGroups[userName] = [];
                }
                userGroups[userName].push(record);
            });

            // Prepare datasets
            const datasets = [];
            const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'];
            let colorIndex = 0;

            Object.keys(userGroups).forEach(userName => {
                const userRecords = userGroups[userName];
                // Create data array that matches uniqueDates, using null for dates without data
                const userTimes = uniqueDates.map(date => {
                    const recordForDate = userRecords.find(r => r.race_tanggal === date);
                    return recordForDate ? parseFloat(recordForDate.race_waktu) : null;
                });

                datasets.push({
                    label: userName,
                    data: userTimes,
                    borderColor: colors[colorIndex % colors.length],
                    backgroundColor: colors[colorIndex % colors.length] + '20',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: colors[colorIndex % colors.length],
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                });

                colorIndex++;
            });

            // Add target lines if available
            const labels = uniqueDates;
            if (records.length > 0) {
                // Create target arrays that match uniqueDates
                const asianTarget = Array(uniqueDates.length).fill(parseFloat(records[0].jarak_asian || 0));
                const australiaTarget = Array(uniqueDates.length).fill(parseFloat(records[0].jarak_australia || 0));
                const asianTrophy = Array(uniqueDates.length).fill(parseFloat(records[0].jarak_asian_trophy || 0));
                const asianOpen = Array(uniqueDates.length).fill(parseFloat(records[0].jarak_asian_open || 0));

                if (asianTarget.some(val => val > 0)) {
                    datasets.push({
                        label: 'ISU Qualifying',
                        data: asianTarget,
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.1
                    });
                }

                if (asianTrophy.some(val => val > 0)) {
                    datasets.push({
                        label: 'Sea Trophy',
                        data: asianTrophy,
                        borderColor: '#20c997',
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        borderWidth: 2,
                        borderDash: [15, 5],
                        fill: false,
                        tension: 0.1
                    });
                }

                if (asianOpen.some(val => val > 0)) {
                    datasets.push({
                        label: 'Asian Open',
                        data: asianOpen,
                        borderColor: '#045bf0',
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        borderWidth: 2,
                        borderDash: [15, 5],
                        fill: false,
                        tension: 0.1
                    });
                }

                if (australiaTarget.some(val => val > 0)) {
                    datasets.push({
                        label: 'Melbourne Open',
                        data: australiaTarget,
                        borderColor: '#6c757d',
                        backgroundColor: 'rgba(108, 117, 125, 0.1)',
                        borderWidth: 2,
                        borderDash: [10, 5],
                        fill: false,
                        tension: 0.1
                    });
                }
            }

            const config = {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        title: {
                            display: false
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#007bff',
                            borderWidth: 1,
                            cornerRadius: 6,
                            displayColors: true,
                            callbacks: {
                                title: function(context) {
                                    return 'Date: ' + context[0].label;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' seconds';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            reverse: true,
                            title: {
                                display: true,
                                text: 'Time (seconds)',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            };

            charts[distance] = new Chart(ctx, config);
        }

        function updateChartsForUser(userId) {
            console.log('Filtering charts for user:', userId);

            // Filter data based on selected user
            Object.keys(charts).forEach(distance => {
                const records = performanceData[distance] || [];

                let filteredRecords = records;
                if (userId && userId !== '') {
                    filteredRecords = records.filter(record => record.race_user_id == userId);
                }

                // Recreate chart with filtered data
                createChartForDistance(distance, filteredRecords);
            });
        }

        function updateStatistics(userId) {
            // Update statistics for each distance
            Object.keys(performanceData).forEach(distance => {
                const records = performanceData[distance] || [];

                let filteredRecords = records;
                if (userId && userId !== '') {
                    filteredRecords = records.filter(record => record.race_user_id == userId);
                }

                // Update DOM elements
                const totalElement = document.getElementById(`total-${distance.toLowerCase().replace(/\s+/g, '-')}`);
                const avgElement = document.getElementById(`avg-${distance.toLowerCase().replace(/\s+/g, '-')}`);
                const bestElement = document.getElementById(`best-${distance.toLowerCase().replace(/\s+/g, '-')}`);

                if (totalElement) totalElement.textContent = filteredRecords.length;
                if (avgElement && filteredRecords.length > 0) {
                    const avg = filteredRecords.reduce((sum, r) => sum + parseFloat(r.race_waktu), 0) / filteredRecords.length;
                    avgElement.textContent = avg.toFixed(2) + 's';
                }
                if (bestElement && filteredRecords.length > 0) {
                    const best = Math.min(...filteredRecords.map(r => parseFloat(r.race_waktu)));
                    bestElement.textContent = best.toFixed(2) + 's';
                }
            });
        }


        // Resize charts on window resize
        window.addEventListener('resize', function() {
            Object.values(charts).forEach(chart => chart.resize());
        });
    </script>

    <style>
        .chart-container {
            min-height: 400px;
        }

        .btn-group .btn.active {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }

        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        @media (max-width: 768px) {
            .chart-container {
                min-height: 300px;
            }

            .btn-group {
                margin-top: 10px;
            }
        }
    </style>
    @endpush

</x-layout>
