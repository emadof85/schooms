<div class="modal fade" id="addSalaryLevelModal" tabindex="-1" role="dialog" aria-labelledby="addSalaryLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content {{ $is_rtl ?? false ? 'text-right' : '' }}" dir="{{ $is_rtl ?? false ? 'rtl' : 'ltr' }}">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalaryLevelModalLabel">
                    @if($is_rtl ?? false)
                        {{ __('salary.add_salary_level') }} <i class="icon-plus3 ml-2"></i>
                    @else
                        <i class="icon-plus3 mr-2"></i> {{ __('salary.add_salary_level') }}
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="addSalaryLevelForm" action="{{ route('finance.salaries.levels.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name" class="font-weight-semibold">{{ __('salary.level_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               required placeholder="{{ __('salary.level_name_placeholder') }}">
                        <small class="form-text text-muted">{{ __('salary.level_name_help') }}</small>
                    </div>

                    <!-- User Type Selection -->
                    <div class="form-group">
                        <label for="user_type_id" class="font-weight-semibold">{{ __('salary.user_type') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="user_type_id" name="user_type_id" required>
                            <option value="">{{ __('salary.select_user_type') }}</option>
                            @foreach($user_types as $user_type)
                                <option value="{{ $user_type->id }}">{{ $user_type->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ __('salary.user_type_help') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="base_salary" class="font-weight-semibold">{{ __('salary.base_salary') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            @if($is_rtl ?? false)
                                <input type="number" class="form-control" id="base_salary" name="base_salary" 
                                       step="0.01" min="0" required placeholder="0.00">
                                <div class="input-group-append">
                                    <span class="input-group-text">$</span>
                                </div>
                            @else
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control" id="base_salary" name="base_salary" 
                                       step="0.01" min="0" required placeholder="0.00">
                            @endif
                        </div>
                        <small class="form-text text-muted">{{ __('salary.base_salary_help') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="description" class="font-weight-semibold">{{ __('salary.description') }}</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="{{ __('salary.description_placeholder') }}"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">{{ __('salary.active') }}</label>
                        </div>
                        <small class="form-text text-muted">{{ __('salary.active_status_help') }}</small>
                    </div>

                    <!-- Salary Level Preview -->
                    <div class="alert alert-info py-2 mt-3">
                        <small>
                            @if($is_rtl ?? false)
                                <span id="levelPreview">[Level Name] - [User Type] - $0.00</span> <strong>{{ __('salary.level_preview') }}: </strong><i class="icon-info22 ml-1"></i>
                            @else
                                <i class="icon-info22 mr-1"></i><strong>{{ __('salary.level_preview') }}: </strong><span id="levelPreview">[Level Name] - [User Type] - $0.00</span>
                            @endif
                        </small>
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
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        @if($is_rtl ?? false)
                            {{ __('salary.add_salary_level') }} <i class="icon-check ml-2"></i>
                        @else
                            <i class="icon-check mr-2"></i> {{ __('salary.add_salary_level') }}
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
    //const isRTL = {{ $is_rtl ?? 'false' }};
   
  
</script>

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
    
    /* Smooth transitions for RTL */
    .modal-content[dir="rtl"] {
        text-align: right;
    }
    
    .modal-content[dir="rtl"] .form-check {
        padding-right: 1.25rem;
        padding-left: 0;
    }
    
    .modal-content[dir="rtl"] .form-check-input {
        margin-right: -1.25rem;
        margin-left: 0;
    }
</style>
@endpush