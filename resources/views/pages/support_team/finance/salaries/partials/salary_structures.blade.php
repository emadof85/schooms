<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ __('salary.salary_structures') }}</h5>
        <div class="header-elements">
            <button type="button" class="btn btn-primary btn-sm" onclick="openAddStructureModal()">
                @if($is_rtl ?? false)
                    {{ __('salary.add_salary_structure') }} <i class="icon-plus3 ml-1"></i>
                @else
                    <i class="icon-plus3 mr-1"></i> {{ __('salary.add_salary_structure') }}
                @endif
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="filterSalaryLevel">{{ __('salary.filter_by_level') }}</label>
                <select class="form-control" id="filterSalaryLevel" onchange="filterStructures()">
                    <option value="">{{ __('salary.all_salary_levels') }}</option>
                    @foreach($salary_levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="filterStatus">{{ __('salary.filter_by_status') }}</label>
                <select class="form-control" id="filterStatus" onchange="filterStructures()">
                    <option value="">{{ __('salary.all_status') }}</option>
                    <option value="1">{{ __('salary.active') }}</option>
                    <option value="0">{{ __('salary.inactive') }}</option>
                </select>
            </div>
        </div>

        <!-- Salary Structures Table -->
        <div class="table-responsive" id="salaryStructuresTable">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('salary.salary_level') }}</th>
                        <th>{{ __('salary.basic_salary') }}</th>
                        <th>{{ __('salary.housing_allowance') }}</th>
                        <th>{{ __('salary.transport_allowance') }}</th>
                        <th>{{ __('salary.medical_allowance') }}</th>
                        <th>{{ __('salary.other_allowances') }}</th>
                        <th>{{ __('salary.total_salary') }}</th>
                        <th>{{ __('salary.effective_date') }}</th>
                        <th>{{ __('salary.status') }}</th>
                        <th>{{ __('msg.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salary_structures ?? [] as $structure)
                    <tr id="structure-row-{{ $structure->id }}">
                        <td>
                            <span class="badge badge-primary">{{ $structure->salaryLevel->name ?? 'N/A' }}</span>
                        </td>
                        <td>${{ number_format($structure->basic_salary, 2) }}</td>
                        <td>${{ number_format($structure->housing_allowance, 2) }}</td>
                        <td>${{ number_format($structure->transport_allowance, 2) }}</td>
                        <td>${{ number_format($structure->medical_allowance, 2) }}</td>
                        <td>${{ number_format($structure->other_allowances, 2) }}</td>
                        <td><strong>${{ number_format($structure->total_salary, 2) }}</strong></td>
                        <td>{{ $structure->effective_date ? $structure->effective_date->format('Y-m-d') : 'N/A' }}</td>
                        <td>
                            <span class="badge badge-{{ $structure->is_active ? 'success' : 'danger' }}">
                                {{ $structure->is_active ? __('salary.active') : __('salary.inactive') }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning btn-sm" onclick="editSalaryStructure({{ $structure->id }})" title="{{ __('msg.edit') }}">
                                    <i class="icon-pencil7"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSalaryStructure({{ $structure->id }})" title="{{ __('msg.delete') }}">
                                    <i class="icon-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">
                            <i class="icon-info22 mr-2"></i> {{ __('msg.no_records_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Add Salary Structure Modal -->

<div class="modal fade" id="addSalaryStructureModal" tabindex="-1" role="dialog" aria-labelledby="addSalaryStructureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content {{ $is_rtl ?? false ? 'text-right' : '' }}" dir="{{ $is_rtl ?? false ? 'rtl' : 'ltr' }}">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalaryStructureModalLabel">
                    @if($is_rtl ?? false)
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="salary_level_id" class="font-weight-semibold">{{ __('salary.salary_levels') }} *</label>
                                <select class="form-control" id="salary_level_id" name="salary_level_id" required>
                                    <option value="">{{ __('salary.select_salary_level') }}</option>
                                    @foreach($salary_levels as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="effective_date" class="font-weight-semibold">{{ __('salary.effective_date') }} *</label>
                                <input type="date" class="form-control" id="effective_date" name="effective_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="basic_salary" class="font-weight-semibold">{{ __('salary.basic_salary') }} *</label>
                                <div class="input-group">
                                    @if($is_rtl ?? false)
                                        <input type="number" class="form-control" id="basic_salary" name="basic_salary" step="0.01" min="0" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">$</span>
                                        </div>
                                    @else
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="basic_salary" name="basic_salary" step="0.01" min="0" required>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="housing_allowance" class="font-weight-semibold">{{ __('salary.housing_allowance') }}</label>
                                <div class="input-group">
                                    @if($is_rtl ?? false)
                                        <input type="number" class="form-control" id="housing_allowance" name="housing_allowance" step="0.01" min="0" value="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text">$</span>
                                        </div>
                                    @else
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="housing_allowance" name="housing_allowance" step="0.01" min="0" value="0">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transport_allowance" class="font-weight-semibold">{{ __('salary.transport_allowance') }}</label>
                                <div class="input-group">
                                    @if($is_rtl ?? false)
                                        <input type="number" class="form-control" id="transport_allowance" name="transport_allowance" step="0.01" min="0" value="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text">$</span>
                                        </div>
                                    @else
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="transport_allowance" name="transport_allowance" step="0.01" min="0" value="0">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="medical_allowance" class="font-weight-semibold">{{ __('salary.medical_allowance') }}</label>
                                <div class="input-group">
                                    @if($is_rtl ?? false)
                                        <input type="number" class="form-control" id="medical_allowance" name="medical_allowance" step="0.01" min="0" value="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text">$</span>
                                        </div>
                                    @else
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="medical_allowance" name="medical_allowance" step="0.01" min="0" value="0">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="other_allowances" class="font-weight-semibold">{{ __('salary.other_allowances') }}</label>
                                <div class="input-group">
                                    @if($is_rtl ?? false)
                                        <input type="number" class="form-control" id="other_allowances" name="other_allowances" step="0.01" min="0" value="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text">$</span>
                                        </div>
                                    @else
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="other_allowances" name="other_allowances" step="0.01" min="0" value="0">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="is_active" class="font-weight-semibold">{{ __('salary.status') }} *</label>
                                <select class="form-control" id="is_active" name="is_active" required>
                                    <option value="1">{{ __('salary.active') }}</option>
                                    <option value="0">{{ __('salary.inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading">{{ __('salary.total_salary_calculation') }}</h6>
                        <p class="mb-0" id="totalSalaryPreview">
                            {{ __('salary.total_will_be_calculated') }}
                        </p>
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
                    <button type="submit" class="btn btn-primary" id="addStructureSubmitButton">
                        @if($is_rtl ?? false)
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

<!-- Edit Salary Structure Modal -->
<div class="modal fade" id="editSalaryStructureModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content {{ $is_rtl ?? false ? 'text-right' : '' }}" dir="{{ $is_rtl ?? false ? 'rtl' : 'ltr' }}">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if($is_rtl ?? false)
                        {{ __('salary.edit_salary_structure') }} <i class="icon-pencil7 ml-2"></i>
                    @else
                        <i class="icon-pencil7 mr-2"></i> {{ __('salary.edit_salary_structure') }}
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="editStructureModalBody">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div> 
 
<script>
// Get RTL status from PHP
const isRTL = {{ $is_rtl ?? false ? 'true' : 'false' }};

console.log('🔧 Salary structures script loaded, RTL:', isRTL);

// Safe alert function to prevent SweetAlert2 scroll errors
function showAlert(type, message, showCancel = false) {
    return new Promise((resolve) => {
        try {
            const swalOptions = {
                title: type === 'success' ? '{{ __("salary.success") }}!' : 
                       type === 'warning' ? '{{ __("msg.confirm_delete") }}' : '{{ __("salary.error") }}!',
                text: message,
                icon: type,
                showCancelButton: showCancel,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: showCancel ? "{{ __('salary.delete') }}" : "{{ __('salary.ok') }}",
                cancelButtonText: "{{ __('salary.cancel') }}",
                customClass: isRTL ? 'swal-rtl' : '',
                allowOutsideClick: false,
                allowEscapeKey: true,
                allowEnterKey: true,
                // Prevent scroll issues
                scrollbarPadding: false
            };

            Swal.fire(swalOptions).then(resolve);
        } catch (error) {
            console.error('❌ Alert error:', error);
            // Fallback to browser confirm/alert
            if (showCancel) {
                resolve({ isConfirmed: confirm(message) });
            } else {
                alert(message);
                resolve({ isConfirmed: true });
            }
        }
    });
}

// Get all structures
 
function getAllStructures() {
    
    // Show loading state
    const tableContainer = document.getElementById('salaryStructuresTable');
    if (tableContainer) {
        tableContainer.innerHTML = `
            <div class="text-center py-4">
                <i class="icon-spinner2 spinner mr-2"></i> {{ __('salary.loading') }}
            </div>
        `;
    }

    fetch(`{{ route("finance.salaries.structures") }}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Filter response:', data);
        if (data.success && data.html) {
            document.getElementById('salaryStructuresTable').innerHTML = data.html;
        } else {
            throw new Error(data.message || 'Filter failed');
        }
    })
    .catch(error => {
        console.error('❌ Filter error:', error);
        showAlert('error', '{{ __("salary.filter_error") }}: ' + error.message);
        
        // Reload the page as fallback
        window.location.reload();
    });
}
// call getAllStructures()
getAllStructures();
// Safe loading function
function showLoading(title = '{{ __("salary.loading") }}...', text = '') {
    try {
        return Swal.fire({
            title: title,
            text: text,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            scrollbarPadding: false
        });
    } catch (error) {
        console.error('❌ Loading alert error:', error);
        return null;
    }
}

// Open add structure modal
function openAddStructureModal() {
    console.log('🔧 Opening add structure modal');
    try {
        $('#addSalaryStructureModal').modal('show');
    } catch (error) {
        console.error('❌ Error opening modal:', error);
        showAlert('error', 'Failed to open form');
    }
}

// Filter structures with fetch
// Filter structures with fetch
function filterStructures() {
    console.log('🔧 Filtering structures');
    
    const levelId = document.getElementById('filterSalaryLevel')?.value || '';
    const status = document.getElementById('filterStatus')?.value || '';

    // Show loading state
    const tableContainer = document.getElementById('salaryStructuresTable');
    if (tableContainer) {
        tableContainer.innerHTML = `
            <div class="text-center py-4">
                <i class="icon-spinner2 spinner mr-2"></i> {{ __('salary.loading') }}
            </div>
        `;
    }

    const params = new URLSearchParams({
        salary_level_id: levelId,
        is_active: status,
        partial: true
    });

    fetch(`{{ route("finance.salaries.structures.filter") }}?${params}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Filter response:', data);
        if (data.success && data.html) {
            document.getElementById('salaryStructuresTable').innerHTML = data.html;
        } else {
            throw new Error(data.message || 'Filter failed');
        }
    })
    .catch(error => {
        console.error('❌ Filter error:', error);
        showAlert('error', '{{ __("salary.filter_error") }}: ' + error.message);
        
        // Reload the page as fallback
        window.location.reload();
    });
}


// Edit salary structure with fetch
function editSalaryStructure(id) {
    console.log('🔧 editSalaryStructure called with ID:', id);
    
    if (!id) {
        console.error('❌ Invalid ID provided:', id);
        showAlert('error', 'Invalid salary structure ID');
        return;
    }

    // Show loading state
    const loadingAlert = showLoading('{{ __("salary.loading") }}...', '{{ __("salary.loading_form") }}');
    const structureEditId= id;
    const url = `{{ route("finance.salaries.structures.edit", ":structureEditId") }}`.replace(':structureEditId', structureEditId);
    console.log('📤 Fetching edit form from:', url);

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        console.log('📥 Response status:', response.status, 'ok:', response.ok);
        
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }).catch(() => {
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Edit form response:', data);
        
        if (loadingAlert) {
            Swal.close();
        }
        
        if (data.success && data.html) {
            // Inject the HTML into the modal
            document.getElementById('editStructureModalBody').innerHTML = data.html;
            
            // Show the modal
            $('#editSalaryStructureModal').modal('show');
            
            console.log('✅ Edit structure modal loaded successfully');
        } else {
            throw new Error(data.message || 'Failed to load edit form');
        }
    })
    .catch(error => {
        console.error('❌ Error loading edit structure modal:', error);
        
        if (loadingAlert) {
            Swal.close();
        }
        
        showAlert('error', '{{ __("salary.load_form_error") }}: ' + error.message);
    });
}

// Delete salary structure with fetch
function deleteSalaryStructure(id) {
    
    const structuresId = id;
    showAlert('warning', "{{ __('salary.delete_structure_warning') }}", true)
    .then((result) => {
        if (result.isConfirmed) {
            const url = `{{ route("finance.salaries.structures.destroy", ":structuresId") }}`.replace(':structuresId', structuresId);
            
            // Show loading
            const deleteLoading = showLoading('{{ __("salary.deleting") }}...');
            
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (deleteLoading) {
                    Swal.close();
                }
                
                if (data.success) {
                    showAlert('success', data.message || '{{ __("salary.deleted") }}!')
                    .then(() => {
                        // Remove the row from table
                        const row = document.getElementById(`structure-row-${id}`);
                        if (row) {
                            row.remove();
                        }
                        // Refresh filters to update counts
                        filterStructures();
                    });
                } else {
                    showAlert('error', data.message || '{{ __("salary.delete_failed") }}');
                }
            })
            .catch(error => {
                console.error('❌ Delete error:', error);
                if (deleteLoading) {
                    Swal.close();
                }
                showAlert('error', '{{ __("salary.delete_error") }}: ' + error.message);
            });
        }
    });
}

// Add salary structure form submission
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Initializing salary structures page');
    
    // Add structure form submission
    const addForm = document.getElementById('addSalaryStructureForm');
    if (addForm) {
        addForm.addEventListener('submit', function(event) {
            event.preventDefault();
            addSalaryStructure();
        });
    }

    // Calculate total salary when any amount field changes
    const amountFields = ['basic_salary', 'housing_allowance', 'transport_allowance', 'medical_allowance', 'other_allowances'];
    amountFields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            element.addEventListener('input', calculateTotalSalary);
        }
    });

    // Reset add form when modal is closed
    $('#addSalaryStructureModal').on('hidden.bs.modal', function () {
        const form = document.getElementById('addSalaryStructureForm');
        if (form) {
            form.reset();
            const preview = document.getElementById('totalSalaryPreview');
            if (preview) {
                preview.textContent = '{{ __("salary.total_will_be_calculated") }}';
            }
        }
    });
});
// update structure

// Calculate total salary for preview
function calculateEditTotalSalary() {
    const basic = parseFloat(document.getElementById('edit_basic_salary')?.value) || 0;
    const housing = parseFloat(document.getElementById('edit_housing_allowance')?.value) || 0;
    const transport = parseFloat(document.getElementById('edit_transport_allowance')?.value) || 0;
    const medical = parseFloat(document.getElementById('edit_medical_allowance')?.value) || 0;
    const other = parseFloat(document.getElementById('edit_other_allowances')?.value) || 0;
    
    const total = basic + housing + transport + medical + other;
    
    const preview = document.getElementById('editTotalSalaryPreview');
    if (preview) {
        preview.textContent = `{{ __('salary.total_salary') }}: $${total.toFixed(2)}`;
    }
    
    return total;
}

// Update salary structure function
function updateSalaryStructure(structureId) {
    
    const form = document.getElementById('editSalaryStructureForm');
    const submitButton = document.getElementById('editStructureSubmitButton');
    const originalText = submitButton.innerHTML;
    const structuresUpdateId = structureId;
    if (!form) {
        
        showAlert('error', '{{ __("salary.form_not_found") }}');
        return;
    }
    
    // Calculate total salary before submitting
    const totalSalary = calculateEditTotalSalary();
    
    // Add total salary to form data
    const totalInput = document.createElement('input');
    totalInput.type = 'hidden';
    totalInput.name = 'total_salary';
    totalInput.value = totalSalary;
    form.appendChild(totalInput);
    
    // Show loading state with RTL support
    if (isRTL) {
        submitButton.innerHTML = '{{ __("salary.updating") }} <i class="icon-spinner2 spinner ml-2"></i>';
    } else {
        submitButton.innerHTML = '<i class="icon-spinner2 spinner mr-2"></i> {{ __("salary.updating") }}';
    }
    submitButton.disabled = true;
    
    const formData = new FormData(form);
    const url = "{{ route('finance.salaries.structures.update', ':structuresUpdateId') }}".replace(':structuresUpdateId', structuresUpdateId);
    
     
    
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
            showAlert('success', data.message || '{{ __("salary.structure_updated") }}')
            .then(() => {
                $('#editSalaryStructureModal').modal('hide');
                // Refresh the structures table
                if (typeof filterStructures === 'function') {
                    filterStructures();
                } else {
                    location.reload();
                }
            });
        } else {
            let errorMessage = data.message || '{{ __("salary.update_failed") }}';
            if (data.errors) {
                errorMessage += '\n' + Object.values(data.errors).flat().join('\n');
            }
            
            showAlert('error', errorMessage);
        }
    })
    .catch(error => {
        console.error('❌ Update error:', error);
        
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        showAlert('error', '{{ __("salary.update_error") }}: ' + error.message);
    });
}

// Safe alert function for RTL support


// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 DOM loaded for edit structure form');
    
    // Add event listeners for salary calculation
    const amountFields = [
        'edit_basic_salary', 
        'edit_housing_allowance', 
        'edit_transport_allowance', 
        'edit_medical_allowance', 
        'edit_other_allowances'
    ];
    
    amountFields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            element.addEventListener('input', calculateEditTotalSalary);
        }
    });
    
    // Initial calculation
    calculateEditTotalSalary();
});

// Calculate total salary for preview
function calculateTotalSalary() {
    const basic = parseFloat(document.getElementById('basic_salary')?.value) || 0;
    const housing = parseFloat(document.getElementById('housing_allowance')?.value) || 0;
    const transport = parseFloat(document.getElementById('transport_allowance')?.value) || 0;
    const medical = parseFloat(document.getElementById('medical_allowance')?.value) || 0;
    const other = parseFloat(document.getElementById('other_allowances')?.value) || 0;
    
    const total = basic + housing + transport + medical + other;
    
    const preview = document.getElementById('totalSalaryPreview');
    if (preview) {
        preview.textContent = `{{ __('salary.total_salary') }}: $${total.toFixed(2)}`;
    }
}

// Add salary structure function
function addSalaryStructure() {
    console.log('🔧 Adding salary structure');
    
    const form = document.getElementById('addSalaryStructureForm');
    const submitButton = document.getElementById('addStructureSubmitButton');
    const originalText = submitButton?.innerHTML;
    
    if (!form) {
        console.error('❌ Form not found');
        showAlert('error', 'Form not found');
        return;
    }
    
    // Calculate total salary before submitting
    const basic = parseFloat(document.getElementById('basic_salary')?.value) || 0;
    const housing = parseFloat(document.getElementById('housing_allowance')?.value) || 0;
    const transport = parseFloat(document.getElementById('transport_allowance')?.value) || 0;
    const medical = parseFloat(document.getElementById('medical_allowance')?.value) || 0;
    const other = parseFloat(document.getElementById('other_allowances')?.value) || 0;
    const total = basic + housing + transport + medical + other;
    
    // Add total salary to form data
    const totalInput = document.createElement('input');
    totalInput.type = 'hidden';
    totalInput.name = 'total_salary';
    totalInput.value = total;
    form.appendChild(totalInput);
    
    // Show loading state with RTL support
    if (submitButton) {
        if (isRTL) {
            submitButton.innerHTML = '{{ __("salary.adding") }} <i class="icon-spinner2 spinner ml-2"></i>';
        } else {
            submitButton.innerHTML = '<i class="icon-spinner2 spinner mr-2"></i> {{ __("salary.adding") }}';
        }
        submitButton.disabled = true;
    }
    
    const formData = new FormData(form);
    const url = "{{ route('finance.salaries.structures.store') }}";
    
    console.log('📤 Sending add request to:', url);
    
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
        if (submitButton && originalText) {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
        
        if (data.success) {
            showAlert('success', data.message || '{{ __("salary.structure_added") }}')
            .then(() => {
                $('#addSalaryStructureModal').modal('hide');
                // Refresh the structures table
                filterStructures();
            });
        } else {
            let errorMessage = data.message || '{{ __("salary.add_failed") }}';
            if (data.errors) {
                errorMessage += '\n' + Object.values(data.errors).flat().join('\n');
            }
            
            showAlert('error', errorMessage);
        }
    })
    .catch(error => {
        console.error('❌ Add error:', error);
        
        // Reset button state
        if (submitButton && originalText) {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
        
        showAlert('error', '{{ __("salary.add_error") }}: ' + error.message);
    });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Salary structures page initialized');
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

.rtl .btn-group {
    direction: ltr;
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

body.rtl .mr-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

body.rtl .ml-1 {
    margin-left: 0 !important;
    margin-right: 0.25rem !important;
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

/* SweetAlert RTL */
body.rtl .swal2-popup {
    text-align: right;
    direction: rtl;
}

body.rtl .swal2-actions {
    justify-content: flex-start;
}

/* Table responsive styles */
.table-responsive {
    overflow-x: auto;
}

/* Badge styles */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Loading animation */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.fade-in {
    animation: fadeIn 0.3s ease-in;
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden;
    padding-right: 0 !important;
}
</style>
 