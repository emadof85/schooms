
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
