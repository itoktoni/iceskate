<x-layout>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="font-weight-bold mb-2">3.605</h2>
                            <div id="detail">Click Here</div>
                        </div>
                        <div>
                            <span class="dashboard-pie-1" style="display: none;">2/5</span><svg class="peity" height="60" width="60"><path d="M 30.000000000000004 0 A 30 30 0 0 1 47.633557568774194 54.270509831248425 L 30 30" data-value="2" fill="rgba(21, 101, 192, 0.3)"></path><path d="M 47.633557568774194 54.270509831248425 A 30 30 0 1 1 29.999999999999993 0 L 30 30" data-value="3" fill="rgb(21, 101, 192)"></path></svg>
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
                            <h2 class="font-weight-bold mb-2">3.137</h2>
                            <div>View Through</div>
                        </div>
                        <div>
                            <span class="dashboard-pie-2" style="display: none;">4/5</span><svg class="peity" height="60" width="60"><path d="M 30.000000000000004 0 A 30 30 0 1 1 1.4683045111453907 20.729490168751582 L 30 30" data-value="4" fill="rgba(0, 200, 81, 0.3)"></path><path d="M 1.4683045111453907 20.729490168751582 A 30 30 0 0 1 29.999999999999993 0 L 30 30" data-value="1" fill="rgb(0, 200, 81)"></path></svg>
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
                            <h2 class="font-weight-bold mb-2">8.765</h2>
                            <div id="detail">Conversions</div>
                        </div>
                        <div>
                            <span class="dashboard-pie-3" style="display: none;">1/5</span><svg class="peity" height="60" width="60"><path d="M 30.000000000000004 0 A 30 30 0 0 1 58.53169548885461 20.72949016875158 L 30 30" data-value="1" fill="rgba(255, 187, 51, 0.3)"></path><path d="M 58.53169548885461 20.72949016875158 A 30 30 0 1 1 29.999999999999993 0 L 30 30" data-value="4" fill="rgb(255, 187, 51)"></path></svg>
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
                            <h2 class="font-weight-bold mb-2">68%</h2>
                            <div id="reader">Retention</div>
                        </div>
                        <div>
                            <span class="dashboard-pie-4" style="display: none;">2/5</span><svg class="peity" height="60" width="60"><path d="M 30.000000000000004 0 A 30 30 0 0 1 47.633557568774194 54.270509831248425 L 30 30" data-value="2" fill="rgba(51, 181, 229, 0.3)"></path><path d="M 47.633557568774194 54.270509831248425 A 30 30 0 1 1 29.999999999999993 0 L 30 30" data-value="3" fill="rgb(51, 181, 229)"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <x-form-select col="6" id="user" name="user" label="User" :options="$user" />
                </div>
                <div class="card-body">
                    <canvas id="dashboardChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('footer')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle user selection and add query string
            function handleUserSelection() {
                const userSelect = document.getElementById('user');
                if (userSelect) {
                    // Set current selected value if exists in URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const selectedUser = urlParams.get('user');
                    if (selectedUser) {
                        userSelect.value = selectedUser;
                    }

                    userSelect.addEventListener('change', function() {
                        const selectedValue = this.value;
                        const currentUrl = new URL(window.location);

                        if (selectedValue && selectedValue !== '') {
                            currentUrl.searchParams.set('user', selectedValue);
                        } else {
                            currentUrl.searchParams.delete('user');
                        }

                        window.location.href = currentUrl.toString();
                    });
                }
            }

            // Initialize user selection handler
            handleUserSelection();

            // Initialize Chart.js
            const ctx = document.getElementById('dashboardChart').getContext('2d');

            // Chart data from PHP
            const chartData = @json($chart ?? []);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels || [],
                    datasets: chartData.datasets || []
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Performance Atlet'
                        },
                        subtitle: {
                            display: false,
                            text: 'Bimo vs Asian Open 2025'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            reverse: true // This inverts the Y-axis - smaller numbers at top
                        },
                        x: {
                            beginAtZero: false
                        }
                    }
                }
            });
        });
    </script>
    @endpush

</x-layout>
