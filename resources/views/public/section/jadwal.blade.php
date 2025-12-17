@if(!empty($data->image->guid))
<section class="text-light relative" data-bgimage="url('{{ $data->image->guid ?? null }}') top">
    <div class="container relative z-2">
        <div class="row g-4">
            <div class="col-lg-12 text-center">
                <div class="spacer-double"></div>
                <h1 class="mb-0">{{ $page->title ?? $data->title }}</h1>
                 {!! nl2br($data->content) ?? '' !!}

                <div class="spacer-double"></div>
            </div>
        </div>
    </div>
    <div class="sw-overlay op-8"></div>
    <div class="gradient-edge-bottom"></div>
</section>
@endif

<section>
    <div class="container">
        @if(empty($data->image->guid))
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h1 class="mb-0">{{ $data->title ?? 'Jadwal' }}</h1>
                 {!! nl2br($data->content) ?? '' !!}
            </div>
        </div>
        @endif

        <!-- Calendar Container -->
        <div class="row">
            <div class="col-lg-8">
                <div class="calendar-container">
                    <div class="calendar-header">
                        <button type="button" class="calendar-nav-btn" onclick="previousMonth()">&larr;</button>
                        <h3 id="calendarMonthYear">December 2025</h3>
                        <button type="button" class="calendar-nav-btn" onclick="nextMonth()">&rarr;</button>
                    </div>
                    <div class="calendar-grid">
                        <div class="calendar-day-header">Sun</div>
                        <div class="calendar-day-header">Mon</div>
                        <div class="calendar-day-header">Tue</div>
                        <div class="calendar-day-header">Wed</div>
                        <div class="calendar-day-header">Thu</div>
                        <div class="calendar-day-header">Fri</div>
                        <div class="calendar-day-header">Sat</div>
                        <div id="calendarDays" class="calendar-days"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="schedule-details">
                    <h4 id="selectedDate">Select a Date</h4>
                    <div id="scheduleList" class="schedule-list">
                        <p class="text-muted">Click on a date to view schedule details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Calendar JavaScript -->
<script>
    let currentDate = new Date();
    let scheduleData = [];

    // Initialize calendar
    function initCalendar() {
        // Get schedule data from PHP
        @if(isset($jadwal))
            scheduleData = @json($jadwal);
        @endif

        renderCalendar();
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Update month/year display
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                          'July', 'August', 'September', 'October', 'November', 'December'];
        document.getElementById('calendarMonthYear').textContent = `${monthNames[month]} ${year}`;

        // Clear previous days
        const calendarDays = document.getElementById('calendarDays');
        calendarDays.innerHTML = '';

        // Get first day of month and number of days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Add empty cells for days before month starts
        for (let i = 0; i < firstDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day empty';
            calendarDays.appendChild(emptyDay);
        }

        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;
            dayElement.onclick = () => selectDate(year, month, day);

            // Check if this date has schedule
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const hasSchedule = scheduleData.some(item => item.jadwal_tanggal === dateStr);

            if (hasSchedule) {
                dayElement.classList.add('has-schedule');
                dayElement.title = 'Has schedule';
            }

            // Highlight today
            const today = new Date();
            if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                dayElement.classList.add('today');
            }

            calendarDays.appendChild(dayElement);
        }
    }

    function selectDate(year, month, day) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const selectedDate = new Date(year, month, day);

        // Format date for display
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('selectedDate').textContent = selectedDate.toLocaleDateString('id-ID', options);

        // Get schedule for this date
        const daySchedule = scheduleData.filter(item => item.jadwal_tanggal === dateStr);
        displaySchedule(daySchedule);
    }

    function displaySchedule(schedule) {
        const scheduleList = document.getElementById('scheduleList');

        if (schedule.length === 0) {
            scheduleList.innerHTML = '<p class="text-muted">No schedule for this date</p>';
            return;
        }

        scheduleList.innerHTML = '';

        schedule.forEach(item => {
            const scheduleItem = document.createElement('div');
            scheduleItem.className = 'schedule-item';
            scheduleItem.innerHTML = `
                <h6 class="schedule-title">${item.jadwal_nama}</h6>
                <p class="schedule-time">${item.jadwal_tanggal}</p>
                <p class="schedule-description">${item.jadwal_keterangan || 'No description'}</p>
            `;
            scheduleList.appendChild(scheduleItem);
        });
    }

    function previousMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initCalendar();
    });
</script>

<!-- Calendar CSS -->
<style>
    .calendar-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .calendar-header {
        background: #36b5f5;
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .calendar-header h3{
        color: #f8f9fa;
    }

    .calendar-nav-btn {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 4px;
    }

    .calendar-nav-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }

    .calendar-day-header {
        background: #f8f9fa;
        padding: 10px;
        text-align: center;
        font-weight: bold;
        border-bottom: 1px solid #dee2e6;
    }

    .calendar-days {
        display: contents;
    }

    .calendar-day {
        padding: 15px;
        text-align: center;
        cursor: pointer;
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        min-height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .calendar-day:nth-child(7n) {
        border-right: none;
    }

    .calendar-day:hover {
        background: #e9ecef;
    }

    .calendar-day.empty {
        background: #f8f9fa;
        cursor: default;
    }

    .calendar-day.today {
        background: #36b5f5;
        color: white;
        font-weight: bold;
    }

    .calendar-day.has-schedule {
        background: #28a745;
        color: white;
        font-weight: bold;
    }

    .calendar-day.has-schedule:hover {
        background: #218838;
    }

    .schedule-details {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 20px;
    }

    .schedule-item {
        border-left: 4px solid #36b5f5;
        padding: 15px;
        margin-bottom: 15px;
        background: #f8f9fa;
        border-radius: 0 4px 4px 0;
    }

    .schedule-title {
        margin: 0 0 5px 0;
        color: #36b5f5;
    }

    .schedule-time {
        margin: 0 0 5px 0;
        font-size: 14px;
        color: #6c757d;
    }

    .schedule-description {
        margin: 0;
        font-size: 14px;
        color: #495057;
    }

    .text-muted {
        color: #6c757d !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .calendar-day {
            padding: 8px;
            min-height: 40px;
            font-size: 14px;
        }

        .calendar-header {
            padding: 15px;
        }

        .schedule-details {
            margin-top: 20px;
        }
    }
</style>
