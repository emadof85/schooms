<div class="modal fade" id="addSalaryStructureModal" tabindex="-1" role="dialog" aria-labelledby="addSalaryStructureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content {{ $is_rtl ?? '' ?? false ? 'text-right' : '' }}" dir="{{ $is_rtl ?? '' ?? false ? 'rtl' : 'ltr' }}">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalaryStructureModalLabel">
                    @if($is_rtl ?? '' ?? false)
                        {{ __('salary.add_salary_structure') }} <i class="icon-plus3 ml-2"></i>
                    @else
                        <i class="icon-plus3 mr-2"></i> {{ __('salary.add_salary_structure') }}
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addSalaryStructureForm">
                @csrf
                <div class="modal-body">
                    <!-- Salary Level Selection -->
                    <div class="form-group">
                        <label for="salary_level_id" class="font-weight-semibold">{{ __('salary.salary_levels') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="salary_level_id" name="salary_level_id" required>
                            <option value="">{{ __('salary.select_salary_level') }}</option>
                            @foreach($salary_levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }} ({{ format_currency($level->base_salary) }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ __('salary.salary_level_help') }}</small>
                    </div>

                    <!-- Component Name -->
                    <div class="form-group">
                        <label for="component_name" class="font-weight-semibold">{{ __('salary.component_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="component_name" name="component_name" 
                               required placeholder="{{ __('salary.component_name_placeholder') }}">
                        <small class="form-text text-muted">{{ __('salary.component_name_help') }}</small>
                    </div>

                    <!-- Component Type -->
                    <div class="form-group">
                        <label for="component_type" class="font-weight-semibold">{{ __('salary.component_type') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="component_type" name="component_type" required>
                            <option value="">{{ __('salary.select_component_type') }}</option>
                            <option value="basic">{{ __('salary.basic') }}</option>
                            <option value="allowance">{{ __('salary.allowance') }}</option>
                            <option value="deduction">{{ __('salary.deduction') }}</option>
                            <option value="bonus">{{ __('salary.bonus') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ __('salary.component_type_help') }}</small>
                    </div>

                    <!-- Calculation Type -->
                    <div class="form-group">
                        <label for="calculation_type" class="font-weight-semibold">{{ __('salary.calculation_type') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="calculation_type" name="calculation_type" required>
                            <option value="">{{ __('salary.select_calculation_type') }}</option>
                            <option value="fixed">{{ __('salary.fixed') }}</option>
                            <option value="percentage">{{ __('salary.percentage') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ __('salary.calculation_type_help') }}</small>
                    </div>

                    <!-- Amount -->
                    <div class="form-group">
                        <label for="amount" class="font-weight-semibold">{{ __('salary.amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            @if($is_rtl ?? '' ?? false)
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       step="0.01" min="0" required placeholder="0.00">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="amountSymbol">$</span>
                                </div>
                            @else
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="amountSymbol">$</span>
                                </div>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       step="0.01" min="0" required placeholder="0.00">
                            @endif
                        </div>
                        <small class="form-text text-muted" id="amountHelp">{{ __('salary.amount_help_fixed') }}</small>
                    </div>

                    <!-- Percentage Of (Conditional) -->
                    <div class="form-group" id="percentage_of_group" style="display: none;">
                        <label for="percentage_of" class="font-weight-semibold">{{ __('salary.percentage_of') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="percentage_of" name="percentage_of" 
                               placeholder="{{ __('salary.percentage_of_placeholder') }}">
                        <small class="form-text text-muted">{{ __('salary.percentage_of_help') }}</small>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="is_active" class="font-weight-semibold">{{ __('salary.status') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="is_active" name="is_active" required>
                            <option value="1">{{ __('salary.active') }}</option>
                            <option value="0">{{ __('salary.inactive') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ __('salary.status_help') }}</small>
                    </div>

                    <!-- Structure Preview -->
                    <div class="alert alert-info py-2 mt-3">
                        <small>
                            @if($is_rtl ?? '' ?? false)
                                <span id="structurePreview">[{{ __('salary.component_name') }}] - [{{ __('salary.salary_levels') }}] - [{{ __('salary.amount') }}]</span> 
                                <strong>{{ __('salary.structure_preview') }}: </strong><i class="icon-info22 ml-1"></i>
                            @else
                                <i class="icon-info22 mr-1"></i><strong>{{ __('salary.structure_preview') }}: </strong>
                                <span id="structurePreview">[{{ __('salary.component_name') }}] - [{{ __('salary.salary_levels') }}] - [{{ __('salary.amount') }}]</span>
                            @endif
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        @if($is_rtl ?? '' ?? false)
                            {{ __('msg.cancel') }} <i class="icon-cross ml-2"></i>
                        @else
                            <i class="icon-cross mr-2"></i> {{ __('msg.cancel') }}
                        @endif
                    </button>
                    <button type="submit" class="btn btn-primary" id="addStructureSubmitButton">
                        @if($is_rtl ?? '' ?? false)
                            {{ __('salary.add_salary_structure') }} <i class="icon-check ml-2"></i>
                        @else
                            <i class="icon-check mr-2"></i> {{ __('salary.add_salary_structure') }}
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Get RTL status from PHP
const isRTL = {{ $is_rtl ?? '' ? 'true' : 'false' }};

console.log('🔧 Add salary structure script loaded');

// Initialize form when modal is shown
document.addEventListener('DOMContentLoaded', function() {
    // Add form submission handler
    const addForm = document.getElementById('addSalaryStructureForm');
    if (addForm) {
        addForm.addEventListener('submit', function(event) {
            event.preventDefault();
            addSalaryStructure();
        });
    }

    // Calculation type change handler
    const calculationTypeSelect = document.getElementById('calculation_type');
    const percentageGroup = document.getElementById('percentage_of_group');
    const amountHelp = document.getElementById('amountHelp');
    const amountSymbol = document.getElementById('amountSymbol');

    if (calculationTypeSelect && percentageGroup && amountHelp && amountSymbol) {
        calculationTypeSelect.addEventListener('change', function() {
            toggleCalculationType(this.value);
        });
    }

    // Update preview when fields change
    const previewFields = ['salary_level_id', 'component_name', 'component_type', 'calculation_type', 'amount'];
    previewFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updateStructurePreview);
            field.addEventListener('change', updateStructurePreview);
        }
    });

    // Reset form when modal is closed
    $('#addSalaryStructureModal').on('hidden.bs.modal', function () {
        resetAddStructureForm();
    });

    // Initial preview update
    updateStructurePreview();
});

// Toggle calculation type fields
function toggleCalculationType(calculationType) {
    const percentageGroup = document.getElementById('percentage_of_group');
    const amountHelp = document.getElementById('amountHelp');
    const amountSymbol = document.getElementById('amountSymbol');

    if (calculationType === 'percentage') {
        percentageGroup.style.display = 'block';
        amountHelp.textContent = '{{ __("salary.amount_help_percentage") }}';
        amountSymbol.textContent = '%';
    } else {
        percentageGroup.style.display = 'none';
        amountHelp.textContent = '{{ __("salary.amount_help_fixed") }}';
        amountSymbol.textContent = '$';
        
        // Clear percentage field when switching to fixed
        const percentageOfField = document.getElementById('percentage_of');
        if (percentageOfField) {
            percentageOfField.value = '';
        }
    }
    
    updateStructurePreview();
}

// Update structure preview
function updateStructurePreview() {
    const componentName = document.getElementById('component_name')?.value || '[{{ __("salary.component_name") }}]';
    const salaryLevelSelect = document.getElementById('salary_level_id');
    const salaryLevel = salaryLevelSelect ? salaryLevelSelect.options[salaryLevelSelect.selectedIndex]?.text.split(' (')[0] : '[{{ __("salary.salary_levels") }}]';
    const componentTypeSelect = document.getElementById('component_type');
    const componentType = componentTypeSelect ? componentTypeSelect.options[componentTypeSelect.selectedIndex]?.text : '[{{ __("salary.component_type") }}]';
    const calculationTypeSelect = document.getElementById('calculation_type');
    const calculationType = calculationTypeSelect ? calculationTypeSelect.value : 'fixed';
    const amount = parseFloat(document.getElementById('amount')?.value) || 0;
    
    let amountDisplay = '';
    if (calculationType === 'percentage') {
        amountDisplay = `${amount}%`;
        const percentageOf = document.getElementById('percentage_of')?.value;
        if (percentageOf) {
            amountDisplay += ` {{ __("salary.of") }} ${percentageOf}`;
        }
    } else {
        amountDisplay = `$${amount.toFixed(2)}`;
    }
    
    const previewElement = document.getElementById('structurePreview');
    if (previewElement) {
        previewElement.textContent = `${componentName} (${componentType}) - ${salaryLevel} - ${amountDisplay}`;
    }
}

// Reset add structure form
function resetAddStructureForm() {
    const form = document.getElementById('addSalaryStructureForm');
    if (form) {
        form.reset();
        
        // Reset dynamic fields
        const percentageGroup = document.getElementById('percentage_of_group');
        const amountHelp = document.getElementById('amountHelp');
        const amountSymbol = document.getElementById('amountSymbol');
        
        if (percentageGroup) percentageGroup.style.display = 'none';
        if (amountHelp) amountHelp.textContent = '{{ __("salary.amount_help_fixed") }}';
        if (amountSymbol) amountSymbol.textContent = '$';
        
        // Reset button state
        const submitButton = document.getElementById('addStructureSubmitButton');
        if (submitButton) {
            if (isRTL) {
                submitButton.innerHTML = '{{ __("salary.add_salary_structure") }} <i class="icon-check ml-2"></i>';
            } else {
                submitButton.innerHTML = '<i class="icon-check mr-2"></i> {{ __("salary.add_salary_structure") }}';
            }
            submitButton.disabled = false;
        }
        
        updateStructurePreview();
    }
}

// Add salary structure function
function addSalaryStructure() {
    console.log('🔧 Adding salary structure');
    
    const form = document.getElementById('addSalaryStructureForm');
    const submitButton = document.getElementById('addStructureSubmitButton');
    
    if (!form || !submitButton) {
        console.error('❌ Form or submit button not found');
        showAlert('error', '{{ __("salary.form_error") }}');
        return;
    }
    
    // Validate required fields
    const requiredFields = ['salary_level_id', 'component_name', 'component_type', 'calculation_type', 'amount'];
    for (const fieldId of requiredFields) {
        const field = document.getElementById(fieldId);
        if (field && !field.value.trim()) {
            showAlert('error', `{{ __("salary.field_required") }}: ${field.labels[0]?.textContent || fieldId}`);
            field.focus();
            return;
        }
    }
    
    // Validate percentage field if calculation type is percentage
    const calculationType = document.getElementById('calculation_type')?.value;
    if (calculationType === 'percentage') {
        const percentageOf = document.getElementById('percentage_of')?.value;
        if (!percentageOf?.trim()) {
            showAlert('error', '{{ __("salary.percentage_of_required") }}');
            document.getElementById('percentage_of').focus();
            return;
        }
    }
    
    const originalText = submitButton.innerHTML;
    
    // Show loading state with RTL support
    if (isRTL) {
        submitButton.innerHTML = '{{ __("salary.adding") }} <i class="icon-spinner2 spinner ml-2"></i>';
    } else {
        submitButton.innerHTML = '<i class="icon-spinner2 spinner mr-2"></i> {{ __("salary.adding") }}';
    }
    submitButton.disabled = true;
    
    const formData = new FormData(form);
    const url = "{{ route('finance.salaries.structures.store') }}";
    
    console.log('📤 Sending add request to:', url);
    console.log('📝 Form data:', Object.fromEntries(formData));
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        console.log('📥 Received response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Add response data:', data);
        
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        if (data.success) {
            Swal.fire({
                title: "{{ __('salary.success') }}!",
                text: data.message,
                icon: "success",
                confirmButtonText: "{{ __('salary.ok') }}",
                customClass: isRTL ? 'swal-rtl' : ''
            }).then(() => {
                $('#addSalaryStructureModal').modal('hide');
                resetAddStructureForm();
                
                // Refresh the structures table
                if (typeof filterStructures === 'function') {
                    filterStructures();
                } else if (typeof refreshSalaryStructuresTable === 'function') {
                    refreshSalaryStructuresTable();
                } else {
                    location.reload();
                }
            });
        } else {
            let errorMessage = data.message;
            if (data.errors) {
                // Format validation errors
                const errorMessages = [];
                for (const [field, errors] of Object.entries(data.errors)) {
                    errorMessages.push(...errors);
                }
                errorMessage = errorMessages.join('\n');
            }
            
            showAlert('error', errorMessage);
        }
    })
    .catch(error => {
        console.error('❌ Add error:', error);
        
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        showAlert('error', '{{ __("salary.add_error") }}: ' + error.message);
    });
}

// Helper function to show alerts
function showAlert(type, message) {
    Swal.fire({
        title: type === 'success' ? '{{ __("salary.success") }}!' : '{{ __("salary.error") }}!',
        text: message,
        icon: type,
        confirmButtonText: '{{ __("salary.ok") }}',
        customClass: isRTL ? 'swal-rtl' : ''
    });
}
</script>

<style>
.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* RTL specific styles */
.modal-content.text-right .form-group label {
    text-align: right;
    display: block;
}

.modal-content.text-right .close {
    margin: -1rem -1rem -1rem auto;
}

body.rtl .mr-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

body.rtl .ml-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}

/* Input group RTL styles */
body.rtl .input-group-append + .form-control {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

body.rtl .form-control + .input-group-append {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Form validation styles */
.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.is-invalid ~ .invalid-feedback {
    display: block;
}

/* SweetAlert RTL */
body.rtl .swal2-popup {
    text-align: right;
    direction: rtl;
}

body.rtl .swal2-actions {
    justify-content: flex-start;
}

/* Smooth transitions */
.modal-content {
    transition: all 0.3s ease;
}

.form-control {
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

/* Focus styles */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Preview styles */
#structurePreview {
    font-weight: 500;
    color: #2c3e50;
}
</style>
@endpush