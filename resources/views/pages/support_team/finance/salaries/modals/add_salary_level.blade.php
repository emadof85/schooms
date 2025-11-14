<div class="modal fade" id="addSalaryLevelModal" tabindex="-1" role="dialog" aria-labelledby="addSalaryLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalaryLevelModalLabel">
                    <i class="icon-plus3 mr-2"></i> {{ __('salary.add_salary_level') }}
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
                               required placeholder="e.g., Junior Staff, Senior Teacher, Manager">
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
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="base_salary" name="base_salary" 
                                   step="0.01" min="0" required placeholder="0.00">
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
                            <i class="icon-info22 mr-1"></i>
                            <strong>{{ __('salary.level_preview') }}: </strong>
                            <span id="levelPreview">[Level Name] - [User Type] - $0.00</span>
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="icon-cross mr-2"></i> {{ __('msg.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <i class="icon-check mr-2"></i> {{ __('salary.add_salary_level') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update level preview when fields change
        $('#name, #user_type_id, #base_salary').on('input change', updateLevelPreview);
        
        // Form submission handler
        $('#addSalaryLevelForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitButton = $('#submitButton');
            const originalText = submitButton.html();
            
            // Show loading state
            submitButton.prop('disabled', true).html('<i class="icon-spinner2 spinner mr-2"></i> Adding...');
            
            const formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Reset button state
                    submitButton.prop('disabled', false).html(originalText);
                    
                    if (response.success) {
                        // Show success message
                        showSuccessToast(response.message);
                        
                        // Close modal
                        $('#addSalaryLevelModal').modal('hide');
                        
                        // Reset form
                        $('#addSalaryLevelForm')[0].reset();
                        updateLevelPreview();
                        
                        // Refresh the salary levels table without page reload
                        refreshSalaryLevelsTable();
                    } else {
                        showErrorToast(response.message);
                    }
                },
                error: function(xhr) {
                    // Reset button state
                    submitButton.prop('disabled', false).html(originalText);
                    
                    let message = 'An error occurred while saving the salary level';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Display validation errors
                        const errors = xhr.responseJSON.errors;
                        message = Object.values(errors).flat().join(', ');
                    }
                    showErrorToast(message);
                }
            });
        });
        
        // Initialize level preview
        updateLevelPreview();
        
        // Reset form when modal is closed
        $('#addSalaryLevelModal').on('hidden.bs.modal', function () {
            $('#addSalaryLevelForm')[0].reset();
            updateLevelPreview();
        });
    });
    
    function updateLevelPreview() {
        const name = $('#name').val() || '[Level Name]';
        const userType = $('#user_type_id option:selected').text() || '[User Type]';
        const baseSalary = parseFloat($('#base_salary').val()) || 0;
        const currencySymbol = '$';
        
        $('#levelPreview').text(`${name} - ${userType} - ${currencySymbol}${baseSalary.toFixed(2)}`);
    }
    
    function refreshSalaryLevelsTable() {
        // You can either:
        // 1. Reload the table via AJAX (recommended)
        // 2. Reload a specific part of the page
        // 3. Use your existing loadSalaryLevels function if it exists
        
        if (typeof loadSalaryLevels === 'function') {
            loadSalaryLevels();
        } else {
            // Simple solution: reload only the table part via AJAX
            $.ajax({
                url: '{{ route("finance.salaries.levels") }}',
                method: 'GET',
                data: { partial: true }, // You can add this parameter to return only the table
                success: function(response) {
                    $('#salaryLevelsTable').html($(response).find('#salaryLevelsTable').html());
                },
                error: function() {
                    // Fallback: show message but don't reload
                    console.log('Table refresh failed, but salary level was created');
                }
            });
        }
    }
    
    function showSuccessToast(message) {
        // Use your existing toast system or create a simple one
        if (typeof showToast === 'function') {
            showToast('success', message);
        } else {
            // Simple notification
            const toast = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icon-check mr-2"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`;
            $('.card-body').prepend(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    }
    
    function showErrorToast(message) {
        if (typeof showToast === 'function') {
            showToast('error', message);
        } else {
            const toast = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="icon-cross mr-2"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`;
            $('.card-body').prepend(toast);
        }
    }
</script>
@endpush