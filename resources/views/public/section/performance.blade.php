@if (!empty($data->image->guid))
    <section class="text-light relative" data-bgimage="url('{{ $data->image->guid ?? null }}') top">
        <div class="container relative z-2">
            <div class="row g-4">
                <div class="col-lg-12 text-center">
                    <div class="spacer-double"></div>
                    <h1 class="mb-0">{{ $page->title ?? $data->title }}</h1>
                    <div class="spacer-double"></div>
                </div>
            </div>
        </div>
        <div class="sw-overlay op-8"></div>
        <div class="gradient-edge-bottom"></div>
    </section>
@endif

<section>
    <div class="container mb-5">

        <!-- User Selection and Performance Charts -->
        <div class="row mb-4">

            @if (auth()->user()->role == 'admin')

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <label for="userSelect" class="form-label">Select User</label>
                            <select class="form-select" id="userSelect" onchange="filterPerformanceData()">
                                <option value="all">All Users</option>
                                @if (isset($performance) && $performance->count() > 0)
                                    @php
                                        $uniqueUsers = $performance->unique('race_user_id')->values();
                                    @endphp
                                    @foreach ($uniqueUsers as $userRecord)
                                        <option value="{{ $userRecord->race_user_id }}">
                                            {{ $userRecord->name ?? 'User ' . $userRecord->race_user_id }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Performance Summary</h5>
                            <div id="performanceSummary">
                                <p class="text-muted">Select a user to view performance summary</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- disini user --}}

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ auth()->user()->name ?? '' }}</h5>
                            <div id="">
                                <h6>Personal Information</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Category</small>
                                        <div class="h5">{{ $user->has_category->field_name ?? '' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Age</small>
                                        <div class="h5">{{ \Carbon\Carbon::parse($user->birthday)->age }} Th</div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <small class="text-muted">Phone</small>
                                        <div class="h5">{{ auth()->user()->phone ?? '' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Join Date</small>
                                        <div class="h5">{{ formatDate(auth()->user()->created_at) ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Performance Summary</h5>
                            <div id="performanceSummary">
                                <p class="text-muted">Select a user to view performance summary</p>
                            </div>
                        </div>
                    </div>
                </div>

            @endif


        </div>

        <div class="row">
            <div class="col-lg-12">
                @if (isset($performance) && $performance->count() > 0)
                    @php
                        // Group performance data by distance
                        $groupedPerformance = $performance->groupBy('jarak_nama');
                    @endphp

                    @foreach ($groupedPerformance as $distance => $records)
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="mb-0">{{ $distance }} Performance Chart</h4>
                                        <small class="text-muted">{{ $records->count() }} records</small>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="chart-{{ Str::slug($distance) }}" width="400"
                                            height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center">
                        <p class="text-muted">No performance data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Performance Statistics -->
        @if (isset($performance) && $performance->count() > 0)
            <div class="row mb-5">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Performance Summary</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="performanceTable">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Distance</th>
                                            <th>Date</th>
                                            <th>Time (seconds)</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($performance as $record)
                                            <tr data-user-id="{{ $record->race_user_id }}">
                                                <td>{{ $record->name ?? 'User ' . $record->race_user_id }}</td>
                                                <td>{{ $record->jarak_nama }}</td>
                                                <td>{{ $record->race_tanggal }}</td>
                                                <td>{{ number_format($record->race_waktu, 2) }}</td>
                                                <td>{{ $record->race_notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </div>


</section>

<div class="spacer" style="clear:both;padding-top:15rem;margin-top:20rem"></div>

<!-- Chart.js for Performance Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let dataTable = null;
    let useDataTables = false;
    const recordsPerPage = 10;
    let currentPage = 1;
    let totalPages = 1;

    // Load dependencies with retry mechanism
    function loadDependencies(callback) {
        console.log('Loading dependencies...');

        // Check if jQuery is already loaded
        if (typeof $ !== 'undefined') {
            console.log('jQuery already loaded');
            loadDataTables(callback);
            return;
        }

        // Load jQuery
        const jqueryScript = document.createElement('script');
        jqueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        jqueryScript.onload = function() {
            console.log('jQuery loaded successfully');
            loadDataTables(callback);
        };
        jqueryScript.onerror = function() {
            console.error('Failed to load jQuery, falling back to simple pagination');
            callback(false);
        };
        document.head.appendChild(jqueryScript);
    }

    function loadDataTables(callback) {
        // Check if DataTables is already loaded
        if (typeof $.fn.DataTable !== 'undefined') {
            console.log('DataTables already loaded');
            callback(true);
            return;
        }

        // Load DataTables CSS
        const dtCss = document.createElement('link');
        dtCss.rel = 'stylesheet';
        dtCss.href = 'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css';
        document.head.appendChild(dtCss);

        // Load DataTables
        const dtScript = document.createElement('script');
        dtScript.src = 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js';
        dtScript.onload = function() {
            console.log('DataTables loaded successfully');
            // Small delay to ensure everything is ready
            setTimeout(() => callback(true), 100);
        };
        dtScript.onerror = function() {
            console.error('Failed to load DataTables, falling back to simple pagination');
            callback(false);
        };
        document.head.appendChild(dtScript);
    }

    // Initialize application
    function initializeApp(useDataTables) {
        console.log('Initializing app with DataTables:', useDataTables);

        // Performance data from PHP
        const performanceData = @json($performance ?? []);

        // Store original data
        window.originalPerformanceData = performanceData;

        // Group data by distance
        const groupedData = {};
        performanceData.forEach(record => {
            const distance = record.jarak_nama;
            if (!groupedData[distance]) {
                groupedData[distance] = [];
            }
            groupedData[distance].push(record);
        });

        // Store grouped data
        window.groupedPerformanceData = groupedData;

        // Create charts for all distances
        createAllCharts();

        // Initialize performance summary
        updatePerformanceSummary('all');

        if (useDataTables) {
            initializeDataTable();
        } else {
            initializeSimplePagination();
        }
    }

    function initializeDataTable() {
        try {
            if (dataTable) {
                dataTable.destroy();
            }

            dataTable = $('#performanceTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true,
                "language": {
                    "search": "Search records:",
                    "lengthMenu": "Show _MENU_ records per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                    "infoEmpty": "Showing 0 to 0 of 0 records",
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "emptyTable": "No performance data available",
                    "zeroRecords": "No matching records found"
                }
            });

            console.log('DataTable initialized successfully');
        } catch (error) {
            console.error('Error initializing DataTable:', error);
            console.log('Falling back to simple pagination');
            initializeSimplePagination();
        }
    }

    function initializeSimplePagination() {
        console.log('Initializing simple pagination');
        updatePaginationInfo();
        showPage(1);
    }

    function createAllCharts() {
        const groupedData = window.groupedPerformanceData || {};

        // Clear existing charts
        Object.keys(groupedData).forEach(distance => {
            const canvasId = 'chart-' + distance.toLowerCase().replace(/\s+/g, '-');
            const canvas = document.getElementById(canvasId);
            if (canvas) {
                const ctx = canvas.getContext('2d');

                // Destroy existing chart if any
                if (window[canvasId + 'Chart']) {
                    window[canvasId + 'Chart'].destroy();
                }

                createChartForDistance(distance, groupedData[distance], ctx, canvasId);
            }
        });
    }

    function createChartForDistance(distance, records, ctx, canvasId) {
        // Sort records by date
        records.sort((a, b) => new Date(a.race_tanggal) - new Date(b.race_tanggal));

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

        // Add user performance lines
        Object.keys(userGroups).forEach(userName => {
            const userRecords = userGroups[userName];
            const userTimes = userRecords.map(record => parseFloat(record.race_waktu));
            const dates = userRecords.map(record => record.race_tanggal);

            datasets.push({
                label: userName,
                data: userTimes,
                borderColor: colors[colorIndex % colors.length],
                backgroundColor: colors[colorIndex % colors.length] + '20',
                borderWidth: 2,
                fill: false,
                tension: 0.1
            });

            colorIndex++;
        });

        // Add target lines (only once per distance)
        if (records.length > 0) {
            const labels = records.map(record => record.race_tanggal);
            const asianTarget = records.map(record => parseFloat(record.jarak_asian));
            const australiaTarget = records.map(record => parseFloat(record.jarak_australia));
            const asianTrophy = records.map(record => parseFloat(record.jarak_asian_trophy));
            const asianOpen = records.map(record => parseFloat(record.jarak_asian_open));

            datasets.push({
                label: 'Asian Target',
                data: asianTarget,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                borderWidth: 2,
                borderDash: [5, 5],
                fill: false,
                tension: 0.1
            });

            datasets.push({
                label: 'Asian Trophy',
                data: asianTrophy,
                borderColor: '#20c997',
                backgroundColor: 'rgba(32, 201, 151, 0.1)',
                borderWidth: 2,
                borderDash: [15, 5],
                fill: false,
                tension: 0.1
            });

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


            datasets.push({
                label: 'Australia Target',
                data: australiaTarget,
                borderColor: '#6c757d',
                backgroundColor: 'rgba(108, 117, 125, 0.1)',
                borderWidth: 2,
                borderDash: [10, 5],
                fill: false,
                tension: 0.1
            });
        }

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: records.map(record => record.race_tanggal),
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: distance + ' Performance'
                    },
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        reverse: true, // Invert Y-axis for time (lower is better)
                        title: {
                            display: true,
                            text: 'Time (seconds)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        // Store chart reference
        window[canvasId + 'Chart'] = chart;
    }

    function filterPerformanceData() {
        const selectedUser = document.getElementById('userSelect').value;

        if (useDataTables && dataTable) {
            // Filter DataTable rows
            if (selectedUser === 'all') {
                dataTable.column(0).search('').draw();
            } else {
                dataTable.column(0).search('User ' + selectedUser).draw();
            }
        } else {
            // Simple table filtering
            const tableRows = document.querySelectorAll('#performanceTable tbody tr');
            tableRows.forEach(row => {
                const userId = row.getAttribute('data-user-id');
                if (selectedUser === 'all' || userId === selectedUser) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            currentPage = 1;
            setTimeout(() => showPage(1), 100);
        }

        // Update performance summary
        updatePerformanceSummary(selectedUser);

        // Update charts
        updateCharts(selectedUser);
    }

    function updatePerformanceSummary(selectedUser) {
        const summaryDiv = document.getElementById('performanceSummary');
        const originalData = window.originalPerformanceData || [];

        let filteredData = originalData;
        if (selectedUser !== 'all') {
            filteredData = originalData.filter(record => record.race_user_id == selectedUser);
        }

        if (filteredData.length === 0) {
            summaryDiv.innerHTML = '<p class="text-muted">No performance data found</p>';
            return;
        }

        // Calculate statistics
        const totalRecords = filteredData.length;
        const uniqueDistances = [...new Set(filteredData.map(r => r.jarak_nama))].length;
        const avgTime = (filteredData.reduce((sum, r) => sum + parseFloat(r.race_waktu), 0) / totalRecords).toFixed(2);
        const bestTime = Math.min(...filteredData.map(r => parseFloat(r.race_waktu))).toFixed(2);

        const userName = selectedUser === 'all' ? 'All Users' : (filteredData[0]?.name || 'User ' + selectedUser);

        summaryDiv.innerHTML = `
            <h6>${userName} Performance</h6>
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Total Records</small>
                    <div class="h5">${totalRecords}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Distances</small>
                    <div class="h5">${uniqueDistances}</div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <small class="text-muted">Average Time</small>
                    <div class="h5">${avgTime}s</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Best Time</small>
                    <div class="h5">${bestTime}s</div>
                </div>
            </div>
        `;
    }

    function updateCharts(selectedUser) {
        const groupedData = window.groupedPerformanceData || {};

        // Update each chart
        Object.keys(groupedData).forEach(distance => {
            const canvasId = 'chart-' + distance.toLowerCase().replace(/\s+/g, '-');
            const canvas = document.getElementById(canvasId);

            if (canvas) {
                const ctx = canvas.getContext('2d');

                // Destroy existing chart
                if (window[canvasId + 'Chart']) {
                    window[canvasId + 'Chart'].destroy();
                }

                // Filter data for selected user
                let filteredRecords = groupedData[distance];
                if (selectedUser !== 'all') {
                    filteredRecords = groupedData[distance].filter(record => record.race_user_id ==
                        selectedUser);
                }

                if (filteredRecords.length > 0) {
                    createChartForDistance(distance, filteredRecords, ctx, canvasId);
                } else {
                    // Clear canvas if no data
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#6c757d';
                    ctx.font = '16px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText('No data for selected user', canvas.width / 2, canvas.height / 2);
                }
            }
        });
    }

    // Simple Pagination Functions
    function updatePaginationInfo() {
        const tableBody = document.getElementById('performanceTableBody');
        if (!tableBody) return;

        const visibleRows = Array.from(tableBody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
        const totalRecords = visibleRows.length;

        if (totalRecords === 0) {
            document.getElementById('paginationInfo').textContent = 'Showing 0 to 0 of 0 entries';
            document.getElementById('prevPage').classList.add('disabled');
            document.getElementById('nextPage').classList.add('disabled');
            return;
        }

        const startRecord = (currentPage - 1) * recordsPerPage + 1;
        const endRecord = Math.min(currentPage * recordsPerPage, totalRecords);

        document.getElementById('paginationInfo').textContent =
            `Showing ${startRecord} to ${endRecord} of ${totalRecords} entries`;

        // Update pagination buttons
        document.getElementById('prevPage').classList.toggle('disabled', currentPage === 1);
        document.getElementById('nextPage').classList.toggle('disabled', currentPage >= totalPages);
    }

    function showPage(pageNumber) {
        const tableBody = document.getElementById('performanceTableBody');
        if (!tableBody) return;

        const visibleRows = Array.from(tableBody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
        const totalRecords = visibleRows.length;

        if (totalRecords === 0) return;

        totalPages = Math.ceil(totalRecords / recordsPerPage);

        // Hide all rows
        visibleRows.forEach(row => row.style.display = 'none');

        // Show rows for current page
        const startIndex = (pageNumber - 1) * recordsPerPage;
        const endIndex = Math.min(startIndex + recordsPerPage, totalRecords);

        for (let i = startIndex; i < endIndex; i++) {
            if (visibleRows[i]) {
                visibleRows[i].style.display = '';
            }
        }

        updatePaginationInfo();
    }

    function previousPage() {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    }

    function nextPage() {
        const tableBody = document.getElementById('performanceTableBody');
        if (!tableBody) return;

        const visibleRows = Array.from(tableBody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
        const totalRecords = visibleRows.length;
        totalPages = Math.ceil(totalRecords / recordsPerPage);

        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded fired');
        loadDependencies(function(success) {
            useDataTables = success;
            initializeApp(useDataTables);
        });
    });
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 15px 20px;
    }

    .card-body {
        padding: 20px;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background: #f8f9fa;
        border-top: none;
        font-weight: 600;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    /* Simple pagination styles */
    .pagination .page-link {
        color: #007bff;
        border-color: #dee2e6;
    }

    .pagination .page-link:hover {
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 15px;
        }

        .table-responsive {
            font-size: 14px;
        }

        .pagination {
            font-size: 12px;
        }
    }
</style>
