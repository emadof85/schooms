@extends('layouts.master')

@section('page_title', __('msg.expense_categories'))

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">{{ __('msg.expense_categories') }}</h6>
        <div class="header-elements">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addExpenseCategoryModal">
                {{ __('msg.add_expense_category') }}
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="expense_categories_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('msg.name') }}</th>
                        <th>{{ __('msg.description') }}</th>
                        <th>{{ __('msg.status') }}</th>
                        <th>{{ __('msg.created_at') }}</th>
                        <th>{{ __('msg.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center">{{ __('msg.loading') }}...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Expense Category Modal -->
<div class="modal fade" id="addExpenseCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('msg.add_expense_category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addExpenseCategoryForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('msg.name') }} *</label>
                        <input type="text" name="name" class="form-control" required placeholder="{{ __('msg.enter_category_name') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.description') }}</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="{{ __('msg.optional_description') }}"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                            <label class="form-check-label" for="is_active">{{ __('msg.active') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer {{ app()->getLocale() === 'ar' ? 'justify-content-start' : '' }}">
                    <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('msg.close') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-check mr-2"></i> {{ __('msg.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Expense Category Modal -->
<div class="modal fade" id="editExpenseCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('msg.edit_expense_category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editExpenseCategoryForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_category_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('msg.name') }} *</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.description') }}</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="edit_is_active">
                            <label class="form-check-label" for="edit_is_active">{{ __('msg.active') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer {{ app()->getLocale() === 'ar' ? 'justify-content-start' : '' }}">
                    <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('msg.close') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-check mr-2"></i> {{ __('msg.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 350px;"></div>

@endsection

@section('scripts')
<!-- SweetAlert CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script>
const CSRF_TOKEN = "{{ csrf_token() }}";
const isRTL = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
const currentLocale = "{{ app()->getLocale() }}";

// RTL Support for modals
if (isRTL) {
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.modal-content').forEach(modal => {
            modal.classList.add('text-right');
        });
        document.querySelectorAll('.modal-footer').forEach(footer => {
            footer.classList.add('justify-content-start');
        });
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    loadExpenseCategories();
    document.getElementById('addExpenseCategoryForm').addEventListener('submit', addExpenseCategory);
    document.getElementById('editExpenseCategoryForm').addEventListener('submit', updateExpenseCategory);
});

function loadExpenseCategories() {
    const tbody = document.querySelector('#expense_categories_table tbody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center">{{ __("msg.loading") }}...</td></tr>';

    fetch('/finance/categories/expense/data', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(r => {
        if (!r.ok) throw new Error('{{ __("msg.network_error") }}');
        return r.json();
    })
    .then(res => {
        if (res.success) {
            renderExpenseCategoriesTable(res.data || []);
        } else {
            showNotification('error', res.message || '{{ __("msg.error_loading_categories") }}');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', '{{ __("msg.error_loading_categories") }}: ' + err.message);
    });
}

function renderExpenseCategoriesTable(categories) {
    const tbody = document.querySelector('#expense_categories_table tbody');
    
    if (!categories || categories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">{{ __("msg.no_data_found") }}</td></tr>';
        return;
    }
    
    let html = '';
    categories.forEach((category, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${category.name || '{{ __("msg.n_a") }}'}</td>
                <td>${category.description || '{{ __("msg.n_a") }}'}</td>
                <td>
                    <span class="badge ${category.is_active ? 'badge-success' : 'badge-danger'}">
                        ${category.is_active ? '{{ __("msg.active") }}' : '{{ __("msg.inactive") }}'}
                    </span>
                </td>
                <td>${formatDate(category.created_at)}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" onclick="editExpenseCategory(${category.id})" title="{{ __('msg.edit') }}">
                            <i class="icon-pencil7"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteExpenseCategory(${category.id})" title="{{ __('msg.delete') }}">
                            <i class="icon-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function addExpenseCategory(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.is_active = data.is_active ? true : false;
    
    if (!data.name) {
        showNotification('error', '{{ __("msg.please_enter_category_name") }}');
        return;
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="icon-spinner4 spinner mr-2"></i> {{ __("msg.saving") }}...';
    submitBtn.disabled = true;
    
    fetch('/finance/categories/expense', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        if (!r.ok) throw new Error('{{ __("msg.network_error") }}');
        return r.json();
    })
    .then(res => {
        if (res.success) {
            $('#addExpenseCategoryModal').modal('hide');
            form.reset();
            showNotification('success', res.message || '{{ __("msg.category_added_success") }}');
            loadExpenseCategories();
        } else {
            showNotification('error', res.message || '{{ __("msg.error_adding_category") }}');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', '{{ __("msg.error_adding_category") }}: ' + err.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function editExpenseCategory(id) {
    console.log('Editing expense category ID:', id);
    
    if (!id) {
        showNotification('error', '{{ __("msg.invalid_category_id") }}');
        return;
    }
    
    showNotification('info', '{{ __("msg.loading_category_data") }}');
    
    fetch(`/finance/categories/expense/edit/${id}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => {
        console.log('Edit response status:', r.status);
        if (!r.ok) throw new Error('{{ __("msg.network_error") }}');
        return r.json();
    })
    .then(res => {
        console.log('Edit response data:', res);
        if (res.success) {
            populateEditCategoryForm(res.data);
            $('#editExpenseCategoryModal').modal('show');
            showNotification('success', '{{ __("msg.category_data_loaded") }}');
        } else {
            showNotification('error', res.message || '{{ __("msg.error_loading_category_data") }}');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', '{{ __("msg.error_loading_category_data") }}: ' + err.message);
    });
}

function populateEditCategoryForm(category) {
    console.log('Populating form with category:', category);
    
    if (!category || !category.id) {
        showNotification('error', '{{ __("msg.invalid_category_data") }}');
        return;
    }
    
    document.getElementById('edit_category_id').value = category.id;
    document.getElementById('edit_name').value = category.name || '';
    document.getElementById('edit_description').value = category.description || '';
    document.getElementById('edit_is_active').checked = category.is_active;
    
    console.log('Form populated with ID:', category.id);
}

function updateExpenseCategory(e) {
    e.preventDefault();
    
    const form = e.target;
    const categoryId = document.getElementById('edit_category_id').value;
    
    if (!categoryId) {
        showNotification('error', '{{ __("msg.invalid_category_id") }}');
        return;
    }
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    delete data._method;
    data.is_active = data.is_active ? true : false;
    
    if (!data.name) {
        showNotification('error', '{{ __("msg.please_enter_category_name") }}');
        return;
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="icon-spinner4 spinner mr-2"></i> {{ __("msg.updating") }}...';
    submitBtn.disabled = true;
    
    fetch(`/finance/categories/expense/${categoryId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        if (!r.ok) throw new Error('{{ __("msg.network_error") }}');
        return r.json();
    })
    .then(res => {
        if (res.success) {
            $('#editExpenseCategoryModal').modal('hide');
            form.reset();
            showNotification('success', res.message || '{{ __("msg.category_updated_success") }}');
            loadExpenseCategories();
        } else {
            showNotification('error', res.message || '{{ __("msg.error_updating_category") }}');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', '{{ __("msg.error_updating_category") }}: ' + err.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// SweetAlert Delete Confirmation
function deleteExpenseCategory(id, name = '') {
    console.log('Deleting expense category ID:', id, 'Name:', name);
    
    if (!id) {
        showNotification('error', '{{ __("msg.invalid_category_id") }}');
        return;
    }

    // Get category name for the confirmation message
    const categoryName = name || '{{ __("msg.this_category") }}';
    
    swal({
        title: "{{ __('msg.are_you_sure') }}",
        text: `{{ __('msg.confirm_delete_category') }}\n"${categoryName}"`,
        icon: "warning",
        buttons: {
            cancel: {
                text: "{{ __('msg.cancel') }}",
                value: null,
                visible: true,
                className: "btn btn-light",
                closeModal: true,
            },
            confirm: {
                text: "{{ __('msg.delete') }}",
                value: true,
                visible: true,
                className: "btn btn-danger",
                closeModal: false
            }
        },
        dangerMode: true,
        closeOnClickOutside: false,
    })
    .then((willDelete) => {
        if (willDelete) {
            // Show loading state in SweetAlert
            swal({
                title: "{{ __('msg.deleting') }}...",
                text: "{{ __('msg.please_wait') }}",
                icon: "info",
                buttons: false,
                closeOnClickOutside: false,
                closeOnEsc: false,
            });
            
            // Perform the actual delete
            fetch(`/finance/categories/expense/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            })
            .then(r => {
                console.log('Delete response status:', r.status);
                if (!r.ok) {
                    throw new Error('{{ __("msg.network_error") }}: ' + r.status);
                }
                return r.json();
            })
            .then(res => {
                console.log('Delete response data:', res);
                
                // Close the loading SweetAlert
                swal.close();
                
                if (res.success) {
                    // Show success message
                    swal({
                        title: "{{ __('msg.success') }}!",
                        text: res.message || '{{ __("msg.category_deleted_success") }}',
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    });
                    
                    // Reload the categories after a short delay
                    setTimeout(() => {
                        loadExpenseCategories();
                    }, 500);
                    
                } else {
                    // Show error message
                    swal({
                        title: "{{ __('msg.error') }}!",
                        text: res.message || '{{ __("msg.error_deleting_category") }}',
                        icon: "error",
                        button: "{{ __('msg.ok') }}"
                    });
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                
                // Close the loading SweetAlert
                swal.close();
                
                // Show error message
                swal({
                    title: "{{ __('msg.error') }}!",
                    text: '{{ __("msg.error_deleting_category") }}: ' + err.message,
                    icon: "error",
                    button: "{{ __('msg.ok') }}"
                });
            });
        }
    });
}

// Update the delete button in the table to pass the category name
function renderExpenseCategoriesTable(categories) {
    const tbody = document.querySelector('#expense_categories_table tbody');
    
    if (!categories || categories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">{{ __("msg.no_data_found") }}</td></tr>';
        return;
    }
    
    let html = '';
    categories.forEach((category, index) => {
        // Escape single quotes in name for JavaScript
        const safeName = (category.name || '').replace(/'/g, "\\'");
        
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${category.name || '{{ __("msg.n_a") }}'}</td>
                <td>${category.description || '{{ __("msg.n_a") }}'}</td>
                <td>
                    <span class="badge ${category.is_active ? 'badge-success' : 'badge-danger'}">
                        ${category.is_active ? '{{ __("msg.active") }}' : '{{ __("msg.inactive") }}'}
                    </span>
                </td>
                <td>${formatDate(category.created_at)}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" onclick="editExpenseCategory(${category.id})" title="{{ __('msg.edit') }}">
                            <i class="icon-pencil7"></i>
                        </button>
                    </div>
                        <div class="btn-group mr-2">
                        <button class="btn btn-sm btn-danger" onclick="deleteExpenseCategory(${category.id}, '${safeName}')" title="{{ __('msg.delete') }}">
                            <i class="icon-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function formatDate(dateString) {
    if (!dateString) return '{{ __("msg.n_a") }}';
    const date = new Date(dateString);
    return date.toLocaleDateString(currentLocale, {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function showNotification(type, message) {
    const container = document.getElementById('notification-container');
    const notificationId = 'notification-' + Date.now();
    
    const icons = {
        success: 'icon-checkmark-circle text-success',
        error: 'icon-cross-circle text-danger',
        warning: 'icon-warning22 text-warning',
        info: 'icon-info22 text-info'
    };
    
    const notification = document.createElement('div');
    notification.id = notificationId;
    notification.className = `alert alert-${type} alert-dismissible fade show ${isRTL ? 'text-right' : ''}`;
    notification.style.cssText = 'margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="${icons[type] || 'icon-info22'} mr-2" style="font-size: 1.2rem;"></i>
            <span class="flex-grow-1">${message}</span>
            <button type="button" class="close" onclick="closeNotification('${notificationId}')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    if (isRTL) {
        container.insertBefore(notification, container.firstChild);
    } else {
        container.appendChild(notification);
    }
    
    setTimeout(() => closeNotification(notificationId), 5000);
}

function closeNotification(id) {
    const notification = document.getElementById(id);
    if (notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
}

// Reset form when modal is closed
$('#addExpenseCategoryModal').on('hidden.bs.modal', function () {
    document.getElementById('addExpenseCategoryForm').reset();
});

$('#editExpenseCategoryModal').on('hidden.bs.modal', function () {
    document.getElementById('editExpenseCategoryForm').reset();
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

/* SweetAlert custom styles */
.swal-button--danger {
    background-color: #d33;
}

.swal-button--cancel {
    color: #555;
    background-color: #efefef;
}
</style>
@endsection