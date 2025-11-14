@extends('layouts.master')

@section('page_title', __('salary.salary_management'))

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            
            <!-- Page Header -->
            <div class="page-header page-header-light">
                <div class="page-header-content header-elements-md-inline">
                    <div class="page-title d-flex">
                        <h4>
                            <i class="icon-cash mr-2"></i>
                            <span class="font-weight-semibold">{{ __('salary.salary_management') }}</span>
                        </h4>
                        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
                    </div>

                    <div class="header-elements d-none">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSalaryRecordModal">
                            <i class="icon-plus3 mr-2"></i> {{ __('salary.add_salary_record') }}
                        </button>
                        <!--  <button type="button" class="btn btn-success ml-2" data-toggle="modal" data-target="#bulkSalaryModal">
                            <i class="icon-stack-check mr-2"></i> {{ __('salary.bulk_salary_processing') }}
                        </button>-->
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- Tabs Navigation -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">{{ __('salary.salary_management') }}</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                        <li class="nav-item">
                            <a href="#salary-records" class="nav-link active" data-toggle="tab">
                                <i class="icon-list mr-2"></i> {{ __('salary.salary_records') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#salary-levels" class="nav-link" data-toggle="tab">
                                <i class="icon-stack mr-2"></i> {{ __('salary.salary_levels') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#salary-structures" class="nav-link" data-toggle="tab">
                                <i class="icon-cog3 mr-2"></i> {{ __('salary.salary_structures') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#deductions-bonuses" class="nav-link" data-toggle="tab">
                                <i class="icon-percent mr-2"></i> {{ __('salary.deductions_bonuses') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- /Tabs Navigation -->

            <!-- Tabs Content -->
            <div class="tab-content">
                
                <!-- Salary Records Tab -->
                <div class="tab-pane fade show active" id="salary-records">
                    @include('pages.support_team.finance.salaries.partials.salary_records')
                </div>
                <!-- /Salary Records Tab -->

                <!-- Salary Levels Tab -->
                <div class="tab-pane fade" id="salary-levels">
                    @include('pages.support_team.finance.salaries.partials.salary_levels')
                </div>
                <!-- /Salary Levels Tab -->

                <!-- Salary Structures Tab -->
                <div class="tab-pane fade" id="salary-structures">
                    @include('pages.support_team.finance.salaries.partials.salary_structures')
                </div>
                <!-- /Salary Structures Tab -->

                <!-- Deductions & Bonuses Tab -->
                <div class="tab-pane fade" id="deductions-bonuses">
                    @include('pages.support_team.finance.salaries.partials.deductions_bonuses')
                </div>
                <!-- /Deductions & Bonuses Tab -->

            </div>
            <!-- /Tabs Content -->

        </div>
    </div>
</div>

<!-- Add Salary Record Modal -->
@include('pages.support_team.finance.salaries.modals.add_salary_record')

<!-- Add Salary Level Modal -->
@include('pages.support_team.finance.salaries.modals.add_salary_level')

<!-- Add Salary Structure Modal -->
@include('pages.support_team.finance.salaries.modals.add_salary_structure')

<!-- Add Deduction Bonus Modal -->
@include('pages.support_team.finance.salaries.modals.add_deduction_bonus')



@endsection

@push('scripts')
<script>
    // Initialize salary management
    document.addEventListener('DOMContentLoaded', function() {
        // Tab change handler
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('href');
            
            // Refresh data based on active tab
            switch(target) {
                case '#salary-records':
                    loadSalaryRecords();
                    break;
                case '#salary-levels':
                    loadSalaryLevels();
                    break;
                case '#salary-structures':
                    loadSalaryStructures();
                    break;
                case '#deductions-bonuses':
                    loadDeductionsBonuses();
                    break;
            }
        });

        // Load initial data
        loadSalaryRecords();

        // Calculate salary on input change
        $('#calculateSalaryForm').on('input', function() {
            calculateNetSalary();
        });
    });

    // Load salary records
    function loadSalaryRecords() {
        $.ajax({
            url: '{{ route("finance.salaries.filter") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#salaryRecordsTable').html(response.html);
                    initializeDataTable('#salaryRecordsTable');
                }
            }
        });
    }

    // Load salary levels
    function loadSalaryLevels() {
        $.ajax({
            url: '{{ route("finance.salaries.levels") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#salaryLevelsTable').html(response.html);
                }
            }
        });
    }

    // Calculate net salary
    function calculateNetSalary() {
        const formData = new FormData(document.getElementById('calculateSalaryForm'));
        
        $.ajax({
            url: '{{ route("finance.salaries.calculate_net_salary") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#netSalaryResult').html(`
                        <div class="alert alert-success">
                            <strong>${response.net_salary}</strong>
                        </div>
                    `);
                }
            }
        });
    }

    // Generate payslip
    function generatePayslip(recordId) {
        $.ajax({
            url: `{{ url('finance/salaries') }}/${recordId}/payslip`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Show payslip in modal or new window
                    showPayslipModal(response.payslip);
                }
            }
        });
    }

    // Show payslip modal
    function showPayslipModal(payslip) {
        // Implementation for showing payslip
        console.log('Payslip:', payslip);
    }
</script>
@endpush