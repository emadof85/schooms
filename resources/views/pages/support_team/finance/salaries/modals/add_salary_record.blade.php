<div class="modal fade" id="addSalaryRecordModal" tabindex="-1" role="dialog" aria-labelledby="addSalaryRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalaryRecordModalLabel">
                    <i class="icon-plus3 mr-2"></i> {{ __('salary.add_salary_record') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="addSalaryRecordForm" action="{{ route('finance.salaries.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Employee Selection -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id" class="font-weight-semibold">{{ __('salary.employee') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="employee_id" name="employee_id" required data-placeholder="Select Employee">
                                    <option value=""></option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" data-salary-level="{{ $employee->salary_level_id }}">
                                            {{ $employee->user->name ?? 'N/A' }} 
                                            @if($employee->user->email)
                                                ({{ $employee->user->email }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Pay Period -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pay_period" class="font-weight-semibold">{{ __('salary.pay_period') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="pay_period" name="pay_period" required>
                                    <option value="">{{ __('salary.filter_by_period') }}</option>
                                    @foreach($pay_periods as $period)
                                        <option value="{{ $period }}">{{ $period }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Format: YYYY-MM (e.g., 2024-01)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Basic Salary -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="basic_salary" class="font-weight-semibold">{{ __('salary.basic_salary') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" class="form-control" id="basic_salary" name="basic_salary" 
                                           step="0.01" min="0" required placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_date" class="font-weight-semibold">{{ __('salary.payment_date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions Section -->
                    <div class="card bg-light">
                        <div class="card-header">
                            <h6 class="card-title text-uppercase font-weight-semibold text-danger">
                                <i class="icon-minus-circle mr-2"></i> {{ __('salary.deductions') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_deductions">Tax Deductions</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" class="form-control deduction-field" id="tax_deductions" 
                                                   name="tax_deductions" step="0.01" min="0" value="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="insurance_deductions">Insurance Deductions</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" class="form-control deduction-field" id="insurance_deductions" 
                                                   name="insurance_deductions" step="0.01" min="0" value="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="other_deductions">Other Deductions</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" class="form-control deduction-field" id="other_deductions" 
                                                   name="other_deductions" step="0.01" min="0" value="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total Deductions</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control bg-light" id="total_deductions" 
                                                   value="0.00" readonly style="font-weight: bold; color: #dc3545;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bonuses Section -->
                    <div class="card bg-light mt-3">
                        <div class="card-header">
                            <h6 class="card-title text-uppercase font-weight-semibold text-success">
                                <i class="icon-plus-circle mr-2"></i> {{ __('salary.bonuses') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="performance_bonus">Performance Bonus</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" class="form-control bonus-field" id="performance_bonus" 
                                                   name="performance_bonus" step="0.01" min="0" value="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="attendance_bonus">Attendance Bonus</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" class="form-control bonus-field" id="attendance_bonus" 
                                                   name="attendance_bonus" step="0.01" min="0" value="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="other_bonuses">Other Bonuses</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" class="form-control bonus-field" id="other_bonuses" 
                                                   name="other_bonuses" step="0.01" min="0" value="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Overtime Hours</label>
                                        <input type="number" class="form-control" id="overtime_hours" 
                                               name="overtime_hours" step="0.1" min="0" value="0" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total Bonuses</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control bg-light" id="total_bonuses" 
                                                   value="0.00" readonly style="font-weight: bold; color: #28a745;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="card bg-primary text-white mt-3">
                        <div class="card-header">
                            <h6 class="card-title text-uppercase font-weight-semibold">
                                <i class="icon-calculator mr-2"></i> Salary Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label class="mb-0">Basic Salary</label>
                                        <h5 class="mb-0" id="summary_basic_salary">$0.00</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label class="mb-0 text-danger">Total Deductions</label>
                                        <h5 class="mb-0 text-danger" id="summary_total_deductions">$0.00</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label class="mb-0 text-success">Total Bonuses</label>
                                        <h5 class="mb-0 text-success" id="summary_total_bonuses">$0.00</h5>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2 bg-white">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <label class="mb-0 font-weight-semibold">Net Salary</label>
                                        <h3 class="mb-0 font-weight-bold" id="summary_net_salary" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">$0.00</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_status" class="font-weight-semibold">{{ __('salary.payment_status') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="payment_status" name="payment_status" required>
                                    <option value="pending">{{ __('salary.pending') }}</option>
                                    <option value="paid">{{ __('salary.paid') }}</option>
                                    <option value="failed">{{ __('salary.failed') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="overtime_pay">Overtime Pay</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" class="form-control bonus-field" id="overtime_pay" 
                                           name="overtime_pay" step="0.01" min="0" value="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="form-group">
                        <label for="remarks">{{ __('salary.remarks') }}</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                  placeholder="Additional notes or comments..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="icon-cross mr-2"></i> {{ __('msg.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-check mr-2"></i> {{ __('salary.add_salary_record') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('#addSalaryRecordModal')
        });

        // Calculate totals when any financial field changes
        $('.deduction-field, .bonus-field, #basic_salary, #overtime_pay').on('input', calculateTotals);

        // Auto-calculate overtime pay based on hours
        $('#overtime_hours').on('input', function() {
            const hours = parseFloat($(this).val()) || 0;
            const hourlyRate = (parseFloat($('#basic_salary').val()) || 0) / 160; // Assuming 160 hours per month
            const overtimePay = hours * hourlyRate * 1.5; // 1.5x for overtime
            $('#overtime_pay').val(overtimePay.toFixed(2));
            calculateTotals();
        });

        // Auto-fill basic salary based on employee's salary level
        $('#employee_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const salaryLevelId = selectedOption.data('salary-level');
            
            if (salaryLevelId) {
                // You can implement logic to fetch the base salary for this level
                // For now, we'll just reset the basic salary field
                $('#basic_salary').val('').focus();
            }
        });

        // Set default pay period to current month
        const currentDate = new Date();
        const currentMonth = currentDate.toISOString().slice(0, 7); // YYYY-MM format
        $('#pay_period').val(currentMonth);
    });

    function calculateTotals() {
        // Get values from input fields
        const basicSalary = parseFloat($('#basic_salary').val()) || 0;
        
        // Deductions
        const taxDeductions = parseFloat($('#tax_deductions').val()) || 0;
        const insuranceDeductions = parseFloat($('#insurance_deductions').val()) || 0;
        const otherDeductions = parseFloat($('#other_deductions').val()) || 0;
        const totalDeductions = taxDeductions + insuranceDeductions + otherDeductions;
        
        // Bonuses
        const performanceBonus = parseFloat($('#performance_bonus').val()) || 0;
        const attendanceBonus = parseFloat($('#attendance_bonus').val()) || 0;
        const otherBonuses = parseFloat($('#other_bonuses').val()) || 0;
        const overtimePay = parseFloat($('#overtime_pay').val()) || 0;
        const totalBonuses = performanceBonus + attendanceBonus + otherBonuses + overtimePay;
        
        // Calculate net salary
        const netSalary = basicSalary - totalDeductions + totalBonuses;
        
        // Update display fields
        $('#total_deductions').val(totalDeductions.toFixed(2));
        $('#total_bonuses').val(totalBonuses.toFixed(2));
        
        // Update summary section
        $('#summary_basic_salary').text('$' + basicSalary.toFixed(2));
        $('#summary_total_deductions').text('$' + totalDeductions.toFixed(2));
        $('#summary_total_bonuses').text('$' + totalBonuses.toFixed(2));
        $('#summary_net_salary').text('$' + netSalary.toFixed(2));
        
        // Update hidden net_salary field if exists
        if ($('#net_salary').length) {
            $('#net_salary').val(netSalary.toFixed(2));
        }
    }

    // Form submission handler
    $('#addSalaryRecordForm').on('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const employeeId = $('#employee_id').val();
        const payPeriod = $('#pay_period').val();
        const basicSalary = $('#basic_salary').val();
        
        if (!employeeId || !payPeriod || !basicSalary) {
            alert('Please fill in all required fields');
            return;
        }
        
        // Submit form via AJAX
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Close modal and refresh table
                    $('#addSalaryRecordModal').modal('hide');
                    showToast('success', response.message);
                    
                    // Reload salary records table
                    if (typeof loadSalaryRecords === 'function') {
                        loadSalaryRecords();
                    }
                    
                    // Reset form
                    $('#addSalaryRecordForm')[0].reset();
                    calculateTotals();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                showToast('error', 'An error occurred while saving the salary record');
            }
        });
    });

    // Reset form when modal is closed
    $('#addSalaryRecordModal').on('hidden.bs.modal', function() {
        $('#addSalaryRecordForm')[0].reset();
        calculateTotals();
        $('.select2').val(null).trigger('change');
    });

    // Initialize calculations on page load
    calculateTotals();
</script>
@endpush