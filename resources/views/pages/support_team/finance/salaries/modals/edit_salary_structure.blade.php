<form id="editSalaryStructureForm">
    @csrf
    @method('PUT')
    
    <div class="modal-body">
        <input type="hidden" name="structure_id" value="{{ $structure->id }}">
        
        <div class="row">
            <div class="col-md-6">
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
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="edit_effective_date" class="font-weight-semibold">{{ __('salary.effective_date') }} *</label>
                    <input type="date" class="form-control" id="edit_effective_date" name="effective_date" 
                           value="{{ $structure->effective_date ? $structure->effective_date->format('Y-m-d') : '' }}" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="edit_basic_salary" class="font-weight-semibold">{{ __('salary.basic_salary') }} *</label>
                    <div class="input-group">
                        @if($is_rtl)
                            <input type="number" class="form-control" id="edit_basic_salary" name="basic_salary" 
                                   value="{{ old('basic_salary', $structure->basic_salary) }}" step="0.01" min="0" required>
                            <div class="input-group-append">
                                <span class="input-group-text">$</span>
                            </div>
                        @else
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="edit_basic_salary" name="basic_salary" 
                                   value="{{ old('basic_salary', $structure->basic_salary) }}" step="0.01" min="0" required>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="edit_housing_allowance" class="font-weight-semibold">{{ __('salary.housing_allowance') }}</label>
                    <div class="input-group">
                        @if($is_rtl)
                            <input type="number" class="form-control" id="edit_housing_allowance" name="housing_allowance" 
                                   value="{{ old('housing_allowance', $structure->housing_allowance) }}" step="0.01" min="0">
                            <div class="input-group-append">
                                <span class="input-group-text">$</span>
                            </div>
                        @else
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="edit_housing_allowance" name="housing_allowance" 
                                   value="{{ old('housing_allowance', $structure->housing_allowance) }}" step="0.01" min="0">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="edit_transport_allowance" class="font-weight-semibold">{{ __('salary.transport_allowance') }}</label>
                    <div class="input-group">
                        @if($is_rtl)
                            <input type="number" class="form-control" id="edit_transport_allowance" name="transport_allowance" 
                                   value="{{ old('transport_allowance', $structure->transport_allowance) }}" step="0.01" min="0">
                            <div class="input-group-append">
                                <span class="input-group-text">$</span>
                            </div>
                        @else
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="edit_transport_allowance" name="transport_allowance" 
                                   value="{{ old('transport_allowance', $structure->transport_allowance) }}" step="0.01" min="0">
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="edit_medical_allowance" class="font-weight-semibold">{{ __('salary.medical_allowance') }}</label>
                    <div class="input-group">
                        @if($is_rtl)
                            <input type="number" class="form-control" id="edit_medical_allowance" name="medical_allowance" 
                                   value="{{ old('medical_allowance', $structure->medical_allowance) }}" step="0.01" min="0">
                            <div class="input-group-append">
                                <span class="input-group-text">$</span>
                            </div>
                        @else
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="edit_medical_allowance" name="medical_allowance" 
                                   value="{{ old('medical_allowance', $structure->medical_allowance) }}" step="0.01" min="0">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="edit_other_allowances" class="font-weight-semibold">{{ __('salary.other_allowances') }}</label>
                    <div class="input-group">
                        @if($is_rtl)
                            <input type="number" class="form-control" id="edit_other_allowances" name="other_allowances" 
                                   value="{{ old('other_allowances', $structure->other_allowances) }}" step="0.01" min="0">
                            <div class="input-group-append">
                                <span class="input-group-text">$</span>
                            </div>
                        @else
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="edit_other_allowances" name="other_allowances" 
                                   value="{{ old('other_allowances', $structure->other_allowances) }}" step="0.01" min="0">
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="edit_is_active" class="font-weight-semibold">{{ __('salary.status') }} *</label>
                    <select class="form-control" id="edit_is_active" name="is_active" required>
                        <option value="1" {{ $structure->is_active ? 'selected' : '' }}>{{ __('salary.active') }}</option>
                        <option value="0" {{ !$structure->is_active ? 'selected' : '' }}>{{ __('salary.inactive') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <h6 class="alert-heading">{{ __('salary.total_salary_calculation') }}</h6>
            <p class="mb-0" id="editTotalSalaryPreview">
                {{ __('salary.total_salary') }}: ${{ number_format($structure->total_salary, 2) }}
            </p>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            @if($is_rtl)
                {{ __('msg.cancel') }} <i class="icon-cross ml-2"></i>
            @else
                <i class="icon-cross mr-2"></i> {{ __('msg.cancel') }}
            @endif
        </button>
        <button type="button" class="btn btn-primary" onclick="updateSalaryStructure({{ $structure->id }})" id="editStructureSubmitButton">
            @if($is_rtl)
                {{ __('salary.update') }} <i class="icon-check ml-2"></i>
            @else
                <i class="icon-check mr-2"></i> {{ __('salary.update') }}
            @endif
        </button>
    </div>
</form>

<script>
// Get RTL status from PHP
const isRTL = {{ $is_rtl ? 'true' : 'false' }};

console.log('🔧 Edit salary structure script loaded for structure: {{ $structure->id }}');

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

/* RTL input group styles */
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

/* SweetAlert RTL */
body.rtl .swal2-popup {
    text-align: right;
    direction: rtl;
}

body.rtl .swal2-actions {
    justify-content: flex-start;
}

/* RTL margin utilities */
body.rtl .mr-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

body.rtl .ml-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}

body.rtl .mr-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

body.rtl .ml-1 {
    margin-left: 0 !important;
    margin-right: 0.25rem !important;
}

/* Form alignment for RTL */
body.rtl .form-control {
    text-align: right;
}

body.rtl .input-group-text {
    border-radius: 0;
}

/* Modal RTL adjustments */
body.rtl .modal-header .close {
    margin: -1rem auto -1rem -1rem;
}

body.rtl .modal-footer {
    justify-content: flex-start;
}
</style>