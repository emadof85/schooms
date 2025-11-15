@extends('layouts.master')

@section('page_title', __('msg.bus_management'))

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12">

            <!-- Page Header -->
            <div class="page-header page-header-light">
                <div class="page-header-content header-elements-md-inline">
                    <div class="page-title d-flex">
                        <h4>
                            <i class="icon-bus mr-2"></i>
                            <span class="font-weight-semibold">{{ __('msg.bus_management') }}</span>
                        </h4>
                        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
                    </div>

                    <div class="header-elements d-none">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBusModal">
                            <i class="icon-plus3 mr-2"></i> {{ __('msg.add_bus') }}
                        </button>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- Tabs Navigation -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">{{ __('msg.bus_management') }}</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                        <li class="nav-item">
                            <a href="#buses" class="nav-link active" data-toggle="tab">
                                <i class="icon-bus mr-2"></i> {{ __('msg.buses') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#bus-drivers" class="nav-link" data-toggle="tab">
                                <i class="icon-user-tie mr-2"></i> {{ __('msg.bus_drivers') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#bus-routes" class="nav-link" data-toggle="tab">
                                <i class="icon-road mr-2"></i> {{ __('msg.bus_routes') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#bus-stops" class="nav-link" data-toggle="tab">
                                <i class="icon-location3 mr-2"></i> {{ __('msg.bus_stops') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#bus-assignments" class="nav-link" data-toggle="tab">
                                <i class="icon-clipboard3 mr-2"></i> {{ __('msg.bus_assignments') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#student-bus-assignments" class="nav-link" data-toggle="tab">
                                <i class="icon-users mr-2"></i> {{ __('msg.student_bus_assignments') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- /Tabs Navigation -->

            <!-- Tabs Content -->
            <div class="tab-content">

                <!-- Buses Tab -->
                <div class="tab-pane fade show active" id="buses">
                    @include('pages.support_team.bus_management.partials.buses')
                </div>
                <!-- /Buses Tab -->

                <!-- Bus Drivers Tab -->
                <div class="tab-pane fade" id="bus-drivers">
                    @include('pages.support_team.bus_management.partials.bus_drivers')
                </div>
                <!-- /Bus Drivers Tab -->

                <!-- Bus Routes Tab -->
                <div class="tab-pane fade" id="bus-routes">
                    @include('pages.support_team.bus_management.partials.bus_routes')
                </div>
                <!-- /Bus Routes Tab -->

                <!-- Bus Stops Tab -->
                <div class="tab-pane fade" id="bus-stops">
                    @include('pages.support_team.bus_management.partials.bus_stops')
                </div>
                <!-- /Bus Stops Tab -->

                <!-- Bus Assignments Tab -->
                <div class="tab-pane fade" id="bus-assignments">
                    @include('pages.support_team.bus_management.partials.bus_assignments')
                </div>
                <!-- /Bus Assignments Tab -->

                <!-- Student Bus Assignments Tab -->
                <div class="tab-pane fade" id="student-bus-assignments">
                    @include('pages.support_team.bus_management.partials.student_bus_assignments')
                </div>
                <!-- /Student Bus Assignments Tab -->

            </div>
            <!-- /Tabs Content -->

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Initialize bus management
    document.addEventListener('DOMContentLoaded', function() {
        // Tab change handler
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('href');

            // Refresh data based on active tab
            switch(target) {
                case '#buses':
                    loadBuses();
                    break;
                case '#bus-drivers':
                    loadBusDrivers();
                    break;
                case '#bus-routes':
                    loadBusRoutes();
                    break;
                case '#bus-stops':
                    loadBusStops();
                    break;
                case '#bus-assignments':
                    loadBusAssignments();
                    break;
                case '#student-bus-assignments':
                    loadStudentBusAssignments();
                    break;
            }
        });

        // Load initial data
        loadBuses();
    });

    // Load buses
    function loadBuses() {
        // Implementation for loading buses
        console.log('Loading buses...');
    }

    // Load bus drivers
    function loadBusDrivers() {
        // Implementation for loading bus drivers
        console.log('Loading bus drivers...');
    }

    // Load bus routes
    function loadBusRoutes() {
        // Implementation for loading bus routes
        console.log('Loading bus routes...');
    }

    // Load bus stops
    function loadBusStops() {
        // Implementation for loading bus stops
        console.log('Loading bus stops...');
    }

    // Load bus assignments
    function loadBusAssignments() {
        // Implementation for loading bus assignments
        console.log('Loading bus assignments...');
    }

    // Load student bus assignments
    function loadStudentBusAssignments() {
        // Implementation for loading student bus assignments
        console.log('Loading student bus assignments...');
    }
</script>
@endpush