<div>
    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-body bg-blue-400 has-bg-image">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0">{{ $totalStudents }}</h3>
                        <span class="text-uppercase font-size-xs font-weight-bold">{{ __('msg.total_students') }}</span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <i class="icon-users4 icon-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body bg-danger-400 has-bg-image">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0">{{ $totalTeachers }}</h3>
                        <span class="text-uppercase font-size-xs">{{ __('msg.total_teachers') }}</span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <i class="icon-users2 icon-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body bg-success-400 has-bg-image">
                <div class="media">
                    <div class="mr-3 align-self-center">
                        <i class="icon-pointer icon-3x opacity-75"></i>
                    </div>

                    <div class="media-body text-right">
                        <h3 class="mb-0">{{ $totalAdmins }}</h3>
                        <span class="text-uppercase font-size-xs">{{ __('msg.total_administrators') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body bg-indigo-400 has-bg-image">
                <div class="media">
                    <div class="mr-3 align-self-center">
                        <i class="icon-user icon-3x opacity-75"></i>
                    </div>

                    <div class="media-body text-right">
                        <h3 class="mb-0">{{ $totalParents }}</h3>
                        <span class="text-uppercase font-size-xs">{{ __('msg.total_parents') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Charts Section--}}
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">{{ __('msg.students_per_grade') }}</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>

                <div class="card-body">
                    <div wire:ignore>
                        <canvas id="studentsPerGradeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">{{ __('msg.user_types_distribution') }}</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>

                <div class="card-body">
                    <div wire:ignore>
                        <canvas id="userTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Events Calendar Begins--}}
    <div class="card mt-3">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('msg.school_events_calendar') }}</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="fullcalendar-basic"></div>
        </div>
    </div>
    {{--Events Calendar Ends--}}

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        console.log('Chart.js script loaded');

        // Initialize charts after DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOMContentLoaded fired');

            setTimeout(function() {
                console.log('Timeout executed, initializing charts');

                // Students per Grade Chart
                const ctx1 = document.getElementById('studentsPerGradeChart');
                console.log('Canvas element:', ctx1);

                if (ctx1) {
                    const gradeData = @json($studentsPerGrade);
                    console.log('Grade Data:', gradeData);

                    if (gradeData && gradeData.length > 0) {
                        console.log('Creating bar chart');
                        new Chart(ctx1, {
                            type: 'bar',
                            data: {
                                labels: gradeData.map(item => item.name),
                                datasets: [{
                                    label: '{{ __("msg.number_of_students") }}',
                                    data: gradeData.map(item => item.students_count),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)',
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)',
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                        console.log('Bar chart created successfully');
                    } else {
                        console.log('No grade data available');
                    }
                } else {
                    console.log('Canvas element studentsPerGradeChart not found');
                }

                // User Types Distribution Chart
                const ctx2 = document.getElementById('userTypesChart');
                console.log('Pie canvas element:', ctx2);

                if (ctx2) {
                    const userTypesData = @json($userTypesData);
                    console.log('User Types Data:', userTypesData);

                    if (userTypesData && userTypesData.length > 0) {
                        console.log('Creating pie chart');
                        new Chart(ctx2, {
                            type: 'pie',
                            data: {
                                labels: userTypesData.map(item => item.label),
                                datasets: [{
                                    label: '{{ __("msg.number_of_users") }}',
                                    data: userTypesData.map(item => item.count),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.8)',
                                        'rgba(54, 162, 235, 0.8)',
                                        'rgba(255, 206, 86, 0.8)',
                                        'rgba(75, 192, 192, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                        console.log('Pie chart created successfully');
                    } else {
                        console.log('No user types data available');
                    }
                } else {
                    console.log('Canvas element userTypesChart not found');
                }
            }, 1000); // Increased delay
        });
    </script>
    @endsection
</div>
