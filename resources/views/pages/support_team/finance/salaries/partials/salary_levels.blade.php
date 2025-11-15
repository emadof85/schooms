<div class="card {{ $is_rtl ?? false ? 'text-right' : '' }}" dir="{{ $is_rtl ?? false ? 'rtl' : 'ltr' }}">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ __('salary.salary_levels') }}</h5>
        <div class="header-elements">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSalaryLevelModal">
                @if($is_rtl ?? false)
                    {{ __('salary.add_salary_level') }} <i class="icon-plus3 ml-1"></i>
                @else
                    <i class="icon-plus3 mr-1"></i> {{ __('salary.add_salary_level') }}
                @endif
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- User Type Filter -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="user_type_filter">{{ __('salary.filter_by_user_type') }}</label>
                <select class="form-control" id="user_type_filter" onchange="filterByUserType(this.value)">
                    <option value="">{{ __('salary.all_user_types') }}</option>
                    @foreach($user_types as $user_type)
                        <option value="{{ $user_type->id }}">{{ $user_type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="status_filter">{{ __('salary.filter_by_status') }}</label>
                <select class="form-control" id="status_filter" onchange="filterByStatus(this.value)">
                    <option value="">{{ __('salary.all_status') }}</option>
                    <option value="1">{{ __('salary.active') }}</option>
                    <option value="0">{{ __('salary.inactive') }}</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-secondary btn-sm" onclick="resetFilters()">
                    @if($is_rtl ?? false)
                        {{ __('salary.reset_filters') }} <i class="icon-reset ml-1"></i>
                    @else
                        <i class="icon-reset mr-1"></i> {{ __('salary.reset_filters') }}
                    @endif
                </button>
            </div>
        </div>

        <div class="table-responsive" id="salaryLevelsTable">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('salary.level_name') }}</th>
                        <th>{{ __('salary.user_type') }}</th>
                        <th>{{ __('salary.base_salary') }}</th>
                        <th>{{ __('salary.description') }}</th>
                        <th>{{ __('salary.status') }}</th>
                        <th>{{ __('salary.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salary_levels as $level)
                    <tr id="salary-level-{{ $level->id }}">
                        <td>
                            <strong>{{ $level->name }}</strong>
                            @if($level->salaryStructures && $level->salaryStructures->count() > 0)
                                <span class="badge badge-info {{ ($is_rtl ?? false) ? 'mr-1' : 'ml-1' }}" title="Has salary structures">
                                    {{ $level->salaryStructures->count() }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-primary">{{ $level->userType->name ?? 'N/A' }}</span>
                        </td>
                        <td>${{ number_format($level->base_salary, 2) }}</td>
                        <td>{{ $level->description ? substr($level->description, 0, 50) . (strlen($level->description) > 50 ? '...' : '') : 'N/A' }}</td>
                        <td>
                            <span class="badge badge-{{ $level->is_active ? 'success' : 'danger' }}">
                                {{ $level->is_active ? __('salary.active') : __('salary.inactive') }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning btn-sm" onclick="editSalaryLevel({{ $level->id }})" title="{{ __('salary.edit_level') }}">
                                    <i class="icon-pencil7"></i>
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="viewStructures({{ $level->id }})" title="{{ __('salary.view_structures') }}">
                                    <i class="icon-list"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSalaryLevel({{ $level->id }})" title="{{ __('salary.delete_level') }}">
                                    <i class="icon-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Bulk Assignment Section -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="card-title">{{ __('salary.bulk_assignment') }}</h6>
            </div>
            <div class="card-body">
                <form id="bulkAssignForm" action="{{ route('finance.salaries.levels.bulk_assign') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="user_type_id">{{ __('salary.user_type') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="user_type_id" name="user_type_id" required>
                                    <option value="">{{ __('salary.select_user_type') }}</option>
                                    @foreach($user_types as $user_type)
                                        <option value="{{ $user_type->id }}">{{ $user_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="salary_level_id">{{ __('salary.salary_level') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="salary_level_id" name="salary_level_id" required>
                                    <option value="">{{ __('salary.select_salary_level') }}</option>
                                    @foreach($salary_levels as $level)
                                        <option value="{{ $level->id }}" data-user-type="{{ $level->user_type_id }}" class="level-option" style="display: none;">
                                            {{ $level->name }} (${{ number_format($level->base_salary, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success btn-block">
                                @if($is_rtl ?? false)
                                    {{ __('salary.assign_to_all') }} <i class="icon-users ml-1"></i>
                                @else
                                    <i class="icon-users mr-1"></i> {{ __('salary.assign_to_all') }}
                                @endif
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">
                        @if($is_rtl ?? false)
                            {{ __('salary.bulk_assign_help') }} <i class="icon-info22 ml-1"></i>
                        @else
                            <i class="icon-info22 mr-1"></i> {{ __('salary.bulk_assign_help') }}
                        @endif
                    </small>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Your Working Add Modal (with RTL support) -->
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

<!-- Edit Salary Level Modal -->
<div class="modal fade" id="editSalaryLevelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content {{ $is_rtl ?? false ? 'text-right' : '' }}" dir="{{ $is_rtl ?? false ? 'rtl' : 'ltr' }}">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('salary.edit_salary_level') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editModalBody">
                <!-- Content will be loaded via fetch -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Get RTL status from PHP with safe default
    const isRTL = {{ $is_rtl ?? 'false' }};
    
    document.addEventListener('DOMContentLoaded', function() {
        // Filter salary levels by user type
        window.filterByUserType = function(userTypeId) {
            const rows = document.querySelectorAll('#salaryLevelsTable tbody tr');
            rows.forEach(row => {
                const rowUserType = row.querySelector('td:nth-child(2) .badge').textContent;
                const userTypeName = document.querySelector(`#user_type_filter option[value="${userTypeId}"]`)?.textContent;
                
                if (!userTypeId || rowUserType === userTypeName) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        };

        // Filter by status
        window.filterByStatus = function(status) {
            const rows = document.querySelectorAll('#salaryLevelsTable tbody tr');
            rows.forEach(row => {
                const statusBadge = row.querySelector('td:nth-child(5) .badge');
                const isActive = statusBadge.classList.contains('badge-success');
                
                if (!status || 
                    (status === '1' && isActive) || 
                    (status === '0' && !isActive)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        };

        // Reset filters
        window.resetFilters = function() {
            document.getElementById('user_type_filter').value = '';
            document.getElementById('status_filter').value = '';
            const rows = document.querySelectorAll('#salaryLevelsTable tbody tr');
            rows.forEach(row => row.style.display = '');
        };

        // Bulk assignment form handling - Keep your working jQuery version
        $('#user_type_id').on('change', function() {
            const userTypeId = $(this).val();
            $('.level-option').hide();
            $(`.level-option[data-user-type="${userTypeId}"]`).show();
            $('#salary_level_id').val('');
        });

        // Your working add form submission - KEEP THIS EXACTLY AS IS
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
        
        // Update level preview when fields change
        $('#name, #user_type_id, #base_salary').on('input change', updateLevelPreview);
        
        // Initialize level preview
        updateLevelPreview();
        
        // Reset form when modal is closed
        $('#addSalaryLevelModal').on('hidden.bs.modal', function () {
            $('#addSalaryLevelForm')[0].reset();
            updateLevelPreview();
        });

        // Bulk assign form submission - Keep your working version
        $('#bulkAssignForm').on('submit', function(e) {
            e.preventDefault();
            
            swal({
                title: "{{ __('salary.confirm_bulk_assign') }}",
                text: "{{ __('salary.bulk_assign_warning') }}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "{{ __('salary.yes_assign') }}",
                cancelButtonText: "{{ __('salary.cancel') }}",
                customClass: isRTL ? 'swal-rtl' : ''
            }).then(function(result) {
                if (result.value) {
                    const formData = new FormData(document.getElementById('bulkAssignForm'));
                    
                    $.ajax({
                        url: $('#bulkAssignForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                swal({
                                    title: "{{ __('salary.success') }}!",
                                    text: response.message,
                                    type: "success",
                                    confirmButtonText: "{{ __('salary.ok') }}",
                                    customClass: isRTL ? 'swal-rtl' : ''
                                });
                                $('#bulkAssignForm')[0].reset();
                                $('.level-option').hide();
                            } else {
                                swal({
                                    title: "{{ __('salary.error') }}!",
                                    text: response.message,
                                    type: "error",
                                    confirmButtonText: "{{ __('salary.try_again') }}",
                                    customClass: isRTL ? 'swal-rtl' : ''
                                });
                            }
                        },
                        error: function(xhr) {
                            let message = '{{ __('salary.bulk_assign_error') }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            swal({
                                title: "{{ __('salary.error') }}!",
                                text: message,
                                type: "error",
                                confirmButtonText: "{{ __('salary.try_again') }}",
                                customClass: isRTL ? 'swal-rtl' : ''
                            });
                        }
                    });
                }
            });
        });
    });

    // Edit Salary Level - NEW FUNCTIONALITY
    function editSalaryLevel(id) {
        
        if (!id) {
            showAlert('error', 'Invalid salary level ID');
            return;
        }
        const editId=id;
        // const url = "{{ route('finance.salaries.levels.destroy', ':levelId') }}".replace(':levelId', id);
        const url = "{{ route('finance.salaries.levels.edit', ':editId') }}".replace(':editId', id);
             //levelsEdit
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.html) {
                document.getElementById('editModalBody').innerHTML = data.html;
                $('#editSalaryLevelModal').modal('show');
            } else {
                showAlert('error', data.message || 'Failed to load edit form');
            }
        })
        .catch(error => {
            console.error('Error loading edit modal:', error);
            showAlert('error', 'Failed to load edit form');
        });
    }

    // Delete Salary Level - KEEP YOUR WORKING VERSION
  
function deleteSalaryLevel(id) {
     
    
     // Validate ID
     if (!id || id === 'undefined' || id === 'null') {
         console.error('Invalid ID provided:', id);
         swal({
             title: "Error!",
             text: "Invalid salary level ID",
             type: "error",
             confirmButtonText: "OK"
         });
         return;
     }
     
     swal({
         title: "{{ __('salary.confirm_delete_level') }}",
         text: "This action cannot be undone!",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#3085d6",
         cancelButtonColor: "#d33",
         confirmButtonText: "Yes, delete it!",
         cancelButtonText: "Cancel"
     }).then(function(result) {
          
           
             
             const url = "{{ route('finance.salaries.levels.destroy', ':levelId') }}".replace(':levelId', id);
             
             // Use .then() syntax instead of async/await
             fetch(url, {
                 method: 'DELETE',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
                     'X-Requested-With': 'XMLHttpRequest'
                 }
             })
             .then(response => response.json())
             .then(data => {
               
                 
                 if (data.success) {
                     swal({
                         title: "Deleted!",
                         text: data.message,
                         type: "success",
                         confirmButtonText: "OK"
                     }).then(function() {
                         $('#salary-level-' + id).remove();
                         $('.level-option[value="' + id + '"]').remove();
                     });
                 } else {
                     swal({
                         title: "Error!",
                         text: data.message,
                         type: "error",
                         confirmButtonText: "OK"
                     });
                 }
             })
             .catch(error => {
                 console.error('Fetch error:', error);
                 swal({
                     title: "Error!",
                     text: 'An error occurred while deleting the salary level',
                     type: "error",
                     confirmButtonText: "OK"
                 });
             });
         
     });
 }
     // Function to refresh salary levels table after adding new level
     function refreshSalaryLevelsTable() {
         $.ajax({
             url: '{{ route("finance.salaries.levels") }}',
             method: 'GET',
             data: { partial: true },
             success: function(response) {
                 $('#salaryLevelsTable').html($(response).find('#salaryLevelsTable').html());
                 // Also update the bulk assignment dropdown
                 const newOptions = $(response).find('.level-option');
                 $('#salary_level_id').html('<option value="">{{ __("salary.select_salary_level") }}</option>');
                 newOptions.each(function() {
                     $('#salary_level_id').append($(this).clone().show());
                 });
             },
             error: function() {
                 console.log('Table refresh failed');
             }
         });
     }

    function viewStructures(levelId) {
        window.location.href = "{{ route('finance.salaries.structures') }}?level_id=" + levelId;
    }

    // Your existing functions - KEEP THESE EXACTLY AS IS
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

    // Helper function to show alerts
    function showAlert(type, message) {
        swal({
            title: type === 'success' ? '{{ __('salary.success') }}!' : '{{ __('salary.error') }}!',
            text: message,
            type: type,
            confirmButtonText: '{{ __('salary.ok') }}',
            customClass: isRTL ? 'swal-rtl' : ''
        });
    }

 //////////////////////////////////////////

 document.addEventListener('DOMContentLoaded', function() {
        // Update level preview when fields change
        $('#name, #user_type_id, #base_salary').on('input change', updateLevelPreview);
        
        // Form submission handler
        $('#addSalaryLevelForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitButton = $('#submitButton');
            const originalText = submitButton.html();
            
            // Show loading state
          
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
                        refreshSalaryLevelsTable();
                        // Close modal
                        $('#editSalaryLevelForm').modal('hide');
                        //$('#addSalaryLevelModal').modal('hide');
                        // Reset form
                        $('#editSalaryLevelForm')[0].reset();
                        updateLevelPreview();
                        
                        // Refresh the salary levels table and dropdowns
                        refreshSalaryLevelsTable();
                    } else {
                        showErrorToast(response.message);
                    }
                },
                error: function(xhr) {
                    // Reset button state
                    submitButton.prop('disabled', false).html(originalText);
                    
                    let message = '{{ __('salary.save_error') }}';
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
        // Refresh the main table
        $.ajax({
            url: '{{ route("finance.salaries.levels") }}',
            method: 'GET',
            data: { 
                partial: true,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Update the table
                $('#salaryLevelsTable').html($(response).find('#salaryLevelsTable').html());
                
                // Update the bulk assignment dropdown
                const newOptions = $(response).find('.level-option');
                $('#salary_level_id').html('<option value="">{{ __("salary.select_salary_level") }}</option>');
                newOptions.each(function() {
                    $('#salary_level_id').append($(this).clone().show());
                });
                
                // Re-attach filter event listeners
                reattachFilterListeners();
            },
            error: function(xhr, status, error) {
                console.log('Table refresh failed:', error);
                // Fallback: reload the page
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        });
    }
    
    function reattachFilterListeners() {
        // Re-attach filter functionality after table refresh
        $('#user_type_filter').off('change').on('change', function() {
            filterByUserType(this.value);
        });
        
        $('#status_filter').off('change').on('change', function() {
            filterByStatus(this.value);
        });
    }
    
    function filterByUserType(userTypeId) {
        const rows = document.querySelectorAll('#salaryLevelsTable tbody tr');
        const userTypeName = document.querySelector(`#user_type_filter option[value="${userTypeId}"]`)?.textContent;
        
        rows.forEach(row => {
            const rowUserType = row.querySelector('td:nth-child(2) .badge').textContent;
            
            if (!userTypeId || rowUserType === userTypeName) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function filterByStatus(status) {
        const rows = document.querySelectorAll('#salaryLevelsTable tbody tr');
        rows.forEach(row => {
            const statusBadge = row.querySelector('td:nth-child(5) .badge');
            const isActive = statusBadge.classList.contains('badge-success');
            
            if (!status || 
                (status === '1' && isActive) || 
                (status === '0' && !isActive)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function showSuccessToast(message) {
        // Use your existing toast system or create a simple one
        if (typeof showToast === 'function') {
            showToast('success', message);
        } else {
            // Simple notification with RTL support
            const toastClass = isRTL ? 'text-right' : '';
            const iconPosition = isRTL ? 'ml-2' : 'mr-2';
            
            const toast = `<div class="alert alert-success alert-dismissible fade show ${toastClass}" dir="${isRTL ? 'rtl' : 'ltr'}" role="alert">
                <i class="icon-check ${iconPosition}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`;
            $('.card-body').prepend(toast);
            
            // Auto remove after 5 seconds
           /* setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);*/
        }
    }
    
    function showErrorToast(message) {
        if (typeof showToast === 'function') {
            showToast('error', message);
        } else {
            const toastClass = isRTL ? 'text-right' : '';
            const iconPosition = isRTL ? 'ml-2' : 'mr-2';
            
            const toast = `<div class="alert alert-danger alert-dismissible fade show ${toastClass}" dir="${isRTL ? 'rtl' : 'ltr'}" role="alert">
                <i class="icon-cross ${iconPosition}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`;
            $('.card-body').prepend(toast);
            
            // Auto remove after 7 seconds for errors
            setTimeout(() => {
                $('.alert').alert('close');
            }, 7000);
        }
    }

    //////////////////////////////////////////
    function updateSalaryLevel(event, levelId) {
   
    event.preventDefault();
    
    console.log('🔧 updateSalaryLevel called', { levelId, formData: new FormData(document.getElementById('editSalaryLevelForm')) });
    
    const form = document.getElementById('editSalaryLevelForm');
    const submitButton = document.getElementById('editSubmitButton');
    const originalText = submitButton.innerHTML;
    
    // Show loading state
   
    submitButton.disabled = true;
    
    const formData = new FormData(form);
    const updateId = levelId;
    const url = "{{ route('finance.salaries.levels.update', ':updateId') }}".replace(':updateId', updateId);
    
    console.log('📤 Sending update request', { url, levelId, formData: Object.fromEntries(formData) });
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('📥 Received response', { status: response.status, ok: response.ok });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Update response data', data);
        refreshSalaryLevelsTable();
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        if (data.success) {
            $('#editSalaryLevelModal').modal('hide');
            Swal.fire({
                title: "{{ __('salary.success') }}!",
                text: data.message,
                icon: "success",
                confirmButtonText: "{{ __('salary.ok') }}",
                customClass: isRTL ? 'swal-rtl' : ''
            }).then(() => {
              
                $('#editSalaryLevelModal').modal('hide');
                // Use refresh instead of reload for better UX
                refreshSalaryLevelsTable();
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
                confirmButtonText: "{{ __('salary.ok') }}",
                customClass: isRTL ? 'swal-rtl' : ''
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
            confirmButtonText: "{{ __('salary.ok') }}",
            customClass: isRTL ? 'swal-rtl' : ''
        });
    });
}

// Update preview when fields change
document.addEventListener('DOMContentLoaded', function() {
    $('#edit_name, #edit_user_type_id, #edit_base_salary').on('input change', function() {
        updateEditLevelPreview();
    });
});

function updateEditLevelPreview() {
    const name = $('#edit_name').val() || '[Level Name]';
    const userType = $('#edit_user_type_id option:selected').text() || '[User Type]';
    const baseSalary = parseFloat($('#edit_base_salary').val()) || 0;
    const currencySymbol = '$';
    
    $('#editLevelPreview').text(`${name} - ${userType} - ${currencySymbol}${baseSalary.toFixed(2)}`);
}
</script>

<style>
    /* RTL support for SweetAlert */
    .swal-rtl {
        text-align: right;
        direction: rtl;
    }
    
    .swal-rtl .swal-footer {
        text-align: left;
    }
    
    /* RTL support for modals */
    .modal.rtl .modal-header,
    .modal.rtl .modal-body,
    .modal.rtl .modal-footer {
        text-align: right;
        direction: rtl;
    }

    
</style>

@endpush