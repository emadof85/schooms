<div class="modal fade" id="addDeductionsBonusesModal" tabindex="-1" role="dialog" aria-labelledby="addDeductionsBonusesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDeductionsBonusesModalLabel">
                    <i class="icon-plus3 mr-2"></i> {{ __('salary.add_deduction_bonus') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="addDeductionsBonusesForm" action="{{ route('finance.salaries.deductions_bonuses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Employee Selection -->
                    <div class="form-group">
                        <label for="employee_id" class="font-weight-semibold">{{ __('salary.employee') }} <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="employee_id" name="employee_id" required data-placeholder="Select Employee">
                            <option value=""></option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->user->name ?? 'N/A' }} 
                                    @if($employee->user->email)
                                        ({{ $employee->user->email }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Selection -->
                    <div class="form-group">
                        <label for="type" class="font-weight-semibold">{{ __('salary.type') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="deduction">{{ __('salary.deduction') }}</option>
                            <option value="bonus">{{ __('salary.bonus') }}</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="font-weight-semibold">{{ __('salary.description') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required 
                                  placeholder="Enter description for this deduction or bonus..."></textarea>
                    </div>

                    <!-- Amount -->
                    <div class="form-group">
                        <label for="amount" class="font-weight-semibold">{{ __('salary.amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="0" required placeholder="0.00">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Effective Date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="effective_date" class="font-weight-semibold">{{ __('salary.effective_date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="effective_date" name="effective_date" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">{{ __('salary.end_date') }}</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                                <small class="form-text text-muted">Leave empty if no end date</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recurring Frequency -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recurring" class="font-weight-semibold">{{ __('salary.recurring') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="recurring" name="recurring" required>
                                    <option value="one_time">{{ __('salary.one_time') }}</option>
                                    <option value="monthly">{{ __('salary.monthly') }}</option>
                                    <option value="yearly">{{ __('salary.yearly') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="font-weight-semibold">{{ __('salary.status') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active">{{ __('salary.active') }}</option>
                                    <option value="inactive">{{ __('salary.inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Preview -->
                    <div class="alert alert-info" id="previewSection">
                        <h6 class="alert-heading mb-2">Preview:</h6>
                        <div id="typePreview" class="font-weight-bold"></div>
                        <div id="recurringPreview" class="small text-muted"></div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="icon-cross mr-2"></i> {{ __('msg.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-check mr-2"></i> {{ __('salary.add_deduction_bonus') }}
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
            dropdownParent: $('#addDeductionsBonusesModal')
        });

        // Update preview when type or recurring changes
        $('#type, #recurring').on('change', updatePreview);
        
        // Set minimum end date to effective date
        $('#effective_date').on('change', function() {
            $('#end_date').attr('min', $(this).val());
        });

        // Initialize preview
        updatePreview();

        // Form submission handler
        $('#addDeductionsBonusesForm').on('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const employeeId = $('#employee_id').val();
            const description = $('#description').val();
            const amount = $('#amount').val();
            const effectiveDate = $('#effective_date').val();
            
            if (!employeeId || !description || !amount || !effectiveDate) {
                alert('Please fill in all required fields');
                return;
            }

            // Validate end date if provided
            const endDate = $('#end_date').val();
            if (endDate && endDate < effectiveDate) {
                alert('End date cannot be before effective date');
                return;
            }
            
            const formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addDeductionsBonusesModal').modal('hide');
                        showToast('success', response.message);
                        
                        // Reload deductions & bonuses table
                        if (typeof filterDeductionsBonuses === 'function') {
                            filterDeductionsBonuses();
                        }
                        
                        // Reset form
                        $('#addDeductionsBonusesForm')[0].reset();
                        $('.select2').val(null).trigger('change');
                        updatePreview();
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr) {
                    let message = 'An error occurred while saving the deduction/bonus';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Handle validation errors
                        const errors = xhr.responseJSON.errors;
                        message = Object.values(errors)[0][0];
                    }
                    showToast('error', message);
                }
            });
        });
        
        // Reset form when modal is closed
        $('#addDeductionsBonusesModal').on('hidden.bs.modal', function() {
            $('#addDeductionsBonusesForm')[0].reset();
            $('.select2').val(null).trigger('change');
            updatePreview();
        });
    });
    
    function updatePreview() {
        const type = $('#type').val();
        const recurring = $('#recurring').val();
        const amount = $('#amount').val() || '0.00';
        
        // Update type preview with color coding
        const typeBadge = type === 'bonus' ? 
            '<span class="badge badge-success">' + type.charAt(0).toUpperCase() + type.slice(1) + '</span>' :
            '<span class="badge badge-warning">' + type.charAt(0).toUpperCase() + type.slice(1) + '</span>';
        
        $('#typePreview').html(typeBadge + ' - $' + parseFloat(amount).toFixed(2));
        
        // Update recurring preview
        let recurringText = '';
        switch(recurring) {
            case 'one_time':
                recurringText = 'One-time application';
                break;
            case 'monthly':
                recurringText = 'Applied monthly';
                break;
            case 'yearly':
                recurringText = 'Applied yearly';
                break;
        }
        $('#recurringPreview').text(recurringText);
    }
    
    function showToast(type, message) {
        // Implement your toast notification here
        // This could be using your existing notification system
        alert(message); // Temporary simple alert
    }
</script>
@endpush