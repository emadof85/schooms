<form id="editSalaryStructureForm">
    @csrf
    @method('PUT')
    
    <div class="modal-body">
        <div class="form-group">
            <label for="edit_salary_level_id" class="font-weight-semibold">{{ __('salary.salary_levels') }} *</label>
            <select class="form-control" id="edit_salary_level_id" name="salary_level_id" required>
                <option value="">{{ __('salary.select_salary_level') }}</option>
                @foreach($salary_levels as $level)
                    <option value="{{ $level->id }}" {{ $structure->salary_level_id == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="edit_component_name" class="font-weight-semibold">{{ __('salary.component_name') }} *</label>
            <input type="text" class="form-control" id="edit_component_name" name="component_name" 
                   value="{{ old('component_name', $structure->component_name) }}" required>
        </div>

        <div class="form-group">
            <label for="edit_component_type" class="font-weight-semibold">{{ __('salary.component_type') }} *</label>
            <select class="form-control" id="edit_component_type" name="component_type" required>
                <option value="basic" {{ $structure->component_type == 'basic' ? 'selected' : '' }}>{{ __('salary.basic') }}</option>
                <option value="allowance" {{ $structure->component_type == 'allowance' ? 'selected' : '' }}>{{ __('salary.allowance') }}</option>
                <option value="deduction" {{ $structure->component_type == 'deduction' ? 'selected' : '' }}>{{ __('salary.deduction') }}</option>
                <option value="bonus" {{ $structure->component_type == 'bonus' ? 'selected' : '' }}>{{ __('salary.bonus') }}</option>
            </select>
        </div>

        <div class="form-group">
            <label for="edit_calculation_type" class="font-weight-semibold">{{ __('salary.calculation_type') }} *</label>
            <select class="form-control" id="edit_calculation_type" name="calculation_type" required>
                <option value="fixed" {{ $structure->calculation_type == 'fixed' ? 'selected' : '' }}>{{ __('salary.fixed') }}</option>
                <option value="percentage" {{ $structure->calculation_type == 'percentage' ? 'selected' : '' }}>{{ __('salary.percentage') }}</option>
            </select>
        </div>

        <div class="form-group">
            <label for="edit_amount" class="font-weight-semibold">{{ __('salary.amount') }} *</label>
            <input type="number" class="form-control" id="edit_amount" name="amount" 
                   value="{{ old('amount', $structure->amount) }}" step="0.01" min="0" required>
        </div>

        <div class="form-group" id="edit_percentage_of_group" style="{{ $structure->calculation_type == 'percentage' ? '' : 'display: none;' }}">
            <label for="edit_percentage_of">{{ __('salary.percentage_of') }} *</label>
            <input type="text" class="form-control" id="edit_percentage_of" name="percentage_of" 
                   value="{{ old('percentage_of', $structure->percentage_of) }}" placeholder="e.g., Basic Salary">
        </div>

        <div class="form-group">
            <label for="edit_is_active" class="font-weight-semibold">{{ __('salary.status') }} *</label>
            <select class="form-control" id="edit_is_active" name="is_active" required>
                <option value="1" {{ $structure->is_active ? 'selected' : '' }}>{{ __('salary.active') }}</option>
                <option value="0" {{ !$structure->is_active ? 'selected' : '' }}>{{ __('salary.inactive') }}</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            @if($is_rtl ?? false)
                {{ __('msg.cancel') }} <i class="icon-cross ml-2"></i>
            @else
                <i class="icon-cross mr-2"></i> {{ __('msg.cancel') }}
            @endif
        </button>
        <button type="button" class="btn btn-primary" onclick="updateSalaryStructure({{ $structure->id }})" id="editStructureSubmitButton">
            @if($is_rtl ?? false)
                {{ __('salary.update_structure') }} <i class="icon-check ml-2"></i>
            @else
                <i class="icon-check mr-2"></i> {{ __('salary.update_structure') }}
            @endif
        </button>
    </div>
</form>

<script>
// Get RTL status from PHP
const isRTL = {{ $is_rtl ? 'true' : 'false' }};

console.log('🔧 Edit salary structure script loaded for structure: {{ $structure->id }}');

// Toggle percentage field visibility
function togglePercentageField(calculationType) {
    const percentageGroup = document.getElementById('edit_percentage_of_group');
    if (calculationType === 'percentage') {
        percentageGroup.style.display = 'block';
    } else {
        percentageGroup.style.display = 'none';
    }
}

// Update salary structure function
function updateSalaryStructure(structureId) {
    console.log('✅ updateSalaryStructure function called for structure:', structureId);
    
    const form = document.getElementById('editSalaryStructureForm');
    const submitButton = document.getElementById('editStructureSubmitButton');
    const originalText = submitButton.innerHTML;
    
    if (!form) {
        console.error('❌ Form not found');
        Swal.fire({
            title: "Error!",
            text: "Form not found",
            icon: "error",
            confirmButtonText: "OK"
        });
        return;
    }
    
    // Show loading state with RTL support
    if (isRTL) {
        submitButton.innerHTML = '{{ __("salary.updating") }} <i class="icon-spinner2 spinner ml-2"></i>';
    } else {
        submitButton.innerHTML = '<i class="icon-spinner2 spinner mr-2"></i> {{ __("salary.updating") }}';
    }
    submitButton.disabled = true;
    
    const formData = new FormData(form);
    const url = "{{ route('finance.salaries.structures.update', ':id') }}".replace(':id', structureId);
    
    console.log('📤 Sending update request to:', url);
    
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
        console.log('✅ Update response data:', data);
        
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        if (data.success) {
            Swal.fire({
                title: "{{ __('salary.success') }}!",
                text: data.message,
                icon: "success",
                confirmButtonText: "{{ __('salary.ok') }}"
            }).then(() => {
                $('#editSalaryStructureModal').modal('hide');
                // Refresh the structures table
                if (typeof filterStructures === 'function') {
                    filterStructures();
                } else {
                    location.reload();
                }
            });
        } else {
            let errorMessage = data.message;
            if (data.errors) {
                errorMessage += '\n' + Object.values(data.errors).flat().join('\n');
            }
            
            Swal.fire({
                title: "{{ __('salary.error') }}!",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "{{ __('salary.ok') }}"
            });
        }
    })
    .catch(error => {
        console.error('❌ Update error:', error);
        
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        Swal.fire({
            title: "{{ __('salary.error') }}!",
            text: '{{ __("salary.update_error") }}: ' + error.message,
            icon: "error",
            confirmButtonText: "{{ __('salary.ok') }}"
        });
    });
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 DOM loaded for edit structure form');
    
    // Toggle percentage field based on calculation type
    const calculationTypeSelect = document.getElementById('edit_calculation_type');
    if (calculationTypeSelect) {
        calculationTypeSelect.addEventListener('change', function() {
            togglePercentageField(this.value);
        });
        
        // Initial toggle
        togglePercentageField(calculationTypeSelect.value);
    }
});
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

.modal-content.text-right .form-check-label {
    margin-right: 1.5rem;
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
</style>