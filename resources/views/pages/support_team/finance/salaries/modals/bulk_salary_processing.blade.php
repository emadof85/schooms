<div class="modal fade" id="bulkSalaryModal" tabindex="-1" role="dialog" aria-labelledby="bulkSalaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkSalaryModalLabel">
                    <i class="icon-stack-check mr-2"></i> {{ __('salary.bulk_salary_processing') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="bulkSalaryForm" action="{{ route('finance.salaries.bulk_process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Pay Period Selection -->
                    <div class="form-group">
                        <label for="bulk_pay_period" class="font-weight-semibold">{{ __('salary.pay_period') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="bulk_pay_period" name="pay_period" required>
                            <option value="">{{ __('salary.filter_by_period') }}</option>
                            @foreach($pay_periods as $period)
                                <option value="{{ $period }}">{{ $period }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Date -->
                    <div class="form-group">
                        <label for="bulk_payment_date" class="font-weight-semibold">{{ __('salary.payment_date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="bulk_payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Processing Method Selection -->
                    <div class="form-group">
                        <label class="font-weight-semibold">Processing Method <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card method-card" data-method="auto">
                                    <div class="card-body text-center">
                                        <i class="icon-calculator icon-2x text-primary mb-2"></i>
                                        <h6>Auto Calculate</h6>
                                        <small class="text-muted">Calculate salaries based on employee levels and structures</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card method-card" data-method="manual">
                                    <div class="card-body text-center">
                                        <i class="icon-upload4 icon-2x text-success mb-2"></i>
                                        <h6>Upload CSV</h6>
                                        <small class="text-muted">Upload pre-calculated salaries via CSV file</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="processing_method" name="processing_method" value="auto">
                    </div>

                    <!-- Auto Calculation Section -->
                    <div id="autoCalculationSection" class="processing-section">
                        <div class="alert alert-info">
                            <i class="icon-info22 mr-2"></i>
                            Salaries will be calculated automatically based on employee salary levels and structures.
                        </div>
                        
                        <div class="form-group">
                            <label>Include Employees</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="include_active" value="1" checked> Active Employees
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="include_drivers" value="1" checked> Drivers Only
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="include_staff" value="1" checked> Staff Only
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Preview of employees to be processed -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Employees to Process</h6>
                            </div>
                            <div class="card-body">
                                <div id="employeePreview" class="small text-muted">
                                    Loading employees...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CSV Upload Section -->
                    <div id="csvUploadSection" class="processing-section" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="icon-warning22 mr-2"></i>
                            Upload a CSV file with employee salaries. 
                            <a href="{{ route('finance.salaries.csv_template') }}" class="alert-link">Download CSV Template</a>
                        </div>

                        <div class="form-group">
                            <label for="csv_file">CSV File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control-file" id="csv_file" name="csv_file" accept=".csv">
                            <small class="form-text text-muted">
                                Required columns: employee_id, basic_salary, deductions, bonuses
                            </small>
                        </div>

                        <!-- CSV Preview -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">CSV Format Preview</h6>
                            </div>
                            <div class="card-body">
                                <pre class="small"><code>employee_id,basic_salary,deductions,bonuses,remarks
1,2500.00,200.00,150.00,"Performance bonus"
2,3000.00,250.00,200.00,"Attendance bonus"</code></pre>
                            </div>
                        </div>
                    </div>

                    <!-- Processing Summary -->
                    <div class="card bg-light mt-3">
                        <div class="card-header">
                            <h6 class="card-title">Processing Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="border rounded p-2">
                                        <div class="h5 mb-0" id="summaryEmployeeCount">0</div>
                                        <small class="text-muted">Employees</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-2">
                                        <div class="h5 mb-0" id="summaryTotalSalary">$0.00</div>
                                        <small class="text-muted">Total Salary</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-2">
                                        <div class="h5 mb-0" id="summaryPayPeriod">-</div>
                                        <small class="text-muted">Pay Period</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-2">
                                        <div class="h5 mb-0" id="summaryStatus">Ready</div>
                                        <small class="text-muted">Status</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar (initially hidden) -->
                    <div id="processingProgress" style="display: none;">
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-center">
                            <span id="progressText">Processing...</span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="icon-cross mr-2"></i> {{ __('msg.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="processButton">
                        <i class="icon-stack-check mr-2"></i> Process Salaries
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Method selection
        $('.method-card').on('click', function() {
            $('.method-card').removeClass('border-primary');
            $(this).addClass('border-primary');
            const method = $(this).data('method');
            $('#processing_method').val(method);
            
            // Show/hide sections
            $('.processing-section').hide();
            if (method === 'auto') {
                $('#autoCalculationSection').show();
                loadEmployeePreview();
            } else {
                $('#csvUploadSection').show();
            }
        });

        // Set default method
        $('.method-card[data-method="auto"]').click();

        // Update summary when pay period changes
        $('#bulk_pay_period, #bulk_payment_date').on('change', updateSummary);

        // Form submission
        $('#bulkSalaryForm').on('submit', function(e) {
            e.preventDefault();
            
            const method = $('#processing_method').val();
            const payPeriod = $('#bulk_pay_period').val();
            
            if (!payPeriod) {
                alert('Please select a pay period');
                return;
            }
            
            if (method === 'manual' && !$('#csv_file').val()) {
                alert('Please select a CSV file');
                return;
            }
            
            // Show progress bar
            $('#processingProgress').show();
            updateProgress(0, 'Starting bulk salary processing...');
            
            // Submit form via AJAX
            const formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = evt.loaded / evt.total * 100;
                            updateProgress(percentComplete, 'Uploading data...');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    updateProgress(100, 'Processing completed!');
                    
                    if (response.success) {
                        showToast('success', response.message);
                        
                        // Show results summary
                        if (response.results) {
                            showResultsSummary(response.results);
                        }
                        
                        // Close modal after delay
                        setTimeout(function() {
                            $('#bulkSalaryModal').modal('hide');
                            // Reload salary records
                            if (typeof loadSalaryRecords === 'function') {
                                loadSalaryRecords();
                            }
                        }, 2000);
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr) {
                    updateProgress(0, 'Processing failed');
                    let message = 'An error occurred during bulk processing';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast('error', message);
                },
                complete: function() {
                    setTimeout(function() {
                        $('#processingProgress').hide();
                    }, 3000);
                }
            });
        });

        // Load employee preview for auto calculation
        function loadEmployeePreview() {
            $.ajax({
                url: '{{ route("finance.salaries.employees_for_processing") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const employees = response.employees;
                        let html = `<div class="mb-2"><strong>${employees.length} employees found:</strong></div>`;
                        html += '<div class="row">';
                        
                        employees.slice(0, 10).forEach(employee => {
                            html += `<div class="col-md-6 mb-1">• ${employee.name}</div>`;
                        });
                        
                        if (employees.length > 10) {
                            html += `<div class="col-12"><em>... and ${employees.length - 10} more</em></div>`;
                        }
                        
                        html += '</div>';
                        $('#employeePreview').html(html);
                        
                        // Update summary
                        $('#summaryEmployeeCount').text(employees.length);
                    }
                }
            });
        }

        function updateProgress(percent, text) {
            $('.progress-bar').css('width', percent + '%').attr('aria-valuenow', percent);
            $('#progressText').text(text);
        }

        function updateSummary() {
            const payPeriod = $('#bulk_pay_period').val();
            const paymentDate = $('#bulk_payment_date').val();
            
            $('#summaryPayPeriod').text(payPeriod || '-');
            $('#summaryStatus').text(payPeriod ? 'Ready to Process' : 'Select Pay Period');
        }

        function showResultsSummary(results) {
            const summary = `
                <div class="alert alert-success">
                    <h6>Bulk Processing Completed</h6>
                    <div class="row">
                        <div class="col-md-6">Successful: ${results.successful?.length || 0}</div>
                        <div class="col-md-6">Failed: ${results.failed?.length || 0}</div>
                    </div>
                </div>
            `;
            $('#processingSummary').html(summary);
        }

        function showToast(type, message) {
            // Implement your toast notification system
            alert(message); // Temporary fallback
        }

        // Initialize summary
        updateSummary();
    });
</script>

<style>
.method-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.method-card:hover {
    border-color: #007bff;
    transform: translateY(-2px);
}
.method-card.border-primary {
    border-color: #007bff !important;
    background-color: #f8f9fa;
}
.processing-section {
    transition: all 0.3s ease;
}
</style>
@endpush