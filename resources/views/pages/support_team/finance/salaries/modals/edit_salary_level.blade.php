<form id="editSalaryLevelForm" onsubmit="updateSalaryLevel(event, {{ $level->id }})">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="edit_name" class="font-weight-semibold">{{ __('salary.level_name') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="edit_name" name="name" value="{{ old('name', $level->name) }}" required>
        <small class="form-text text-muted">{{ __('salary.level_name_help') }}</small>
    </div>

    <div class="form-group">
        <label for="edit_user_type_id" class="font-weight-semibold">{{ __('salary.user_type') }} <span class="text-danger">*</span></label>
        <select class="form-control" id="edit_user_type_id" name="user_type_id" required>
            <option value="">{{ __('salary.select_user_type') }}</option>
            @foreach($user_types as $user_type)
                <option value="{{ $user_type->id }}" {{ $level->user_type_id == $user_type->id ? 'selected' : '' }}>
                    {{ $user_type->name }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">{{ __('salary.user_type_help') }}</small>
    </div>

    <div class="form-group">
        <label for="edit_base_salary" class="font-weight-semibold">{{ __('salary.base_salary') }} <span class="text-danger">*</span></label>
        <div class="input-group">
            @if($is_rtl ?? false)
                <input type="number" class="form-control" id="edit_base_salary" name="base_salary" 
                       value="{{ old('base_salary', $level->base_salary) }}" step="0.01" min="0" required>
                <div class="input-group-append">
                    <span class="input-group-text">$</span>
                </div>
            @else
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                <input type="number" class="form-control" id="edit_base_salary" name="base_salary" 
                       value="{{ old('base_salary', $level->base_salary) }}" step="0.01" min="0" required>
            @endif
        </div>
        <small class="form-text text-muted">{{ __('salary.base_salary_help') }}</small>
    </div>

    <div class="form-group">
        <label for="edit_description" class="font-weight-semibold">{{ __('salary.description') }}</label>
        <textarea class="form-control" id="edit_description" name="description" rows="3" 
                  placeholder="{{ __('salary.description_placeholder') }}">{{ old('description', $level->description) }}</textarea>
    </div>

    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active" value="1" 
                   {{ old('is_active', $level->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="edit_is_active">{{ __('salary.active') }}</label>
        </div>
        <small class="form-text text-muted">{{ __('salary.active_status_help') }}</small>
    </div>

    <!-- Update Preview -->
    <div class="alert alert-info py-2 mt-3">
        <small>
            @if($is_rtl ?? false)
                <span id="editLevelPreview">{{ $level->name }} - {{ $level->userType->name ?? 'N/A' }} - ${{ number_format($level->base_salary, 2) }}</span> 
                <strong>{{ __('salary.level_preview') }}: </strong><i class="icon-info22 ml-1"></i>
            @else
                <i class="icon-info22 mr-1"></i><strong>{{ __('salary.level_preview') }}: </strong>
                <span id="editLevelPreview">{{ $level->name }} - {{ $level->userType->name ?? 'N/A' }} - ${{ number_format($level->base_salary, 2) }}</span>
            @endif
        </small>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            @if($is_rtl ?? false)
                {{ __('salary.cancel') }} <i class="icon-cross ml-2"></i>
            @else
                <i class="icon-cross mr-2"></i> {{ __('salary.cancel') }}
            @endif
        </button>
        <button type="submit" class="btn btn-primary" id="editSubmitButton">
            @if($is_rtl ?? false)
                {{ __('salary.update_level') }} <i class="icon-check ml-2"></i>
            @else
                <i class="icon-check mr-2"></i> {{ __('salary.update_level') }}
            @endif
        </button>
    </div>
</form>
@push('scripts')
<style>
    /* RTL specific styles */
    [dir="rtl"] .input-group-append + .form-control {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    [dir="rtl"] .form-control + .input-group-append {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    /* SweetAlert RTL */
    .swal-rtl {
        text-align: right;
        direction: rtl;
    }
    
    .swal-rtl .swal-footer {
        text-align: left;
    }

    /* Additional RTL styles for form elements */
    [dir="rtl"] .form-check {
        padding-right: 1.25rem;
        padding-left: 0;
    }
    
    [dir="rtl"] .form-check-input {
        margin-right: -1.25rem;
        margin-left: 0;
    }
    
    [dir="rtl"] .modal-footer {
        justify-content: flex-start;
    }
    
    [dir="rtl"] .text-right {
        text-align: left !important;
    }
    
    [dir="rtl"] .text-left {
        text-align: right !important;
    }
    
    [dir="rtl"] .mr-1 {
        margin-right: 0 !important;
        margin-left: 0.25rem !important;
    }
    
    [dir="rtl"] .ml-1 {
        margin-left: 0 !important;
        margin-right: 0.25rem !important;
    }
    
    [dir="rtl"] .mr-2 {
        margin-right: 0 !important;
        margin-left: 0.5rem !important;
    }
    
    [dir="rtl"] .ml-2 {
        margin-left: 0 !important;
        margin-right: 0.5rem !important;
    }
</style>
@endpush