@extends('layouts.master')

@section('page_title', __('msg.income_categories'))

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">{{ __('msg.income_categories') }}</h6>
        <div class="header-elements">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addIncomeCategoryModal">
                {{ __('msg.add_income_category') }}
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="income_categories_table">
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

<!-- Add Income Category Modal -->
<div class="modal fade" id="addIncomeCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('msg.add_income_category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addIncomeCategoryForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('msg.name') }} *</label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter category name">
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.description') }}</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Optional description"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                            <label class="form-check-label" for="is_active">{{ __('msg.active') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('msg.close') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-check mr-2"></i> {{ __('msg.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Income Category Modal -->
<div class="modal fade" id="editIncomeCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('msg.edit_income_category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editIncomeCategoryForm">
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
                <div class="modal-footer">
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
<script>
const CSRF_TOKEN = "{{ csrf_token() }}";
const isRTL = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    loadIncomeCategories();
    document.getElementById('addIncomeCategoryForm').addEventListener('submit', addIncomeCategory);
    document.getElementById('editIncomeCategoryForm').addEventListener('submit', updateIncomeCategory);
});

function loadIncomeCategories() {
    const tbody = document.querySelector('#income_categories_table tbody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center">{{ __("msg.loading") }}...</td></tr>';

    fetch('/finance/categories/income/data', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res => {
        if (res.success) {
            renderIncomeCategoriesTable(res.data || []);
        } else {
            showNotification('error', res.message || 'Error loading categories');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', 'Error loading categories: ' + err.message);
    });
}

function renderIncomeCategoriesTable(categories) {
    const tbody = document.querySelector('#income_categories_table tbody');
    
    if (!categories || categories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">{{ __("msg.no_data_found") }}</td></tr>';
        return;
    }
    
    let html = '';
    categories.forEach((category, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${category.name || 'N/A'}</td>
                <td>${category.description || 'N/A'}</td>
                <td>
                    <span class="badge ${category.is_active ? 'badge-success' : 'badge-danger'}">
                        ${category.is_active ? '{{ __("msg.active") }}' : '{{ __("msg.inactive") }}'}
                    </span>
                </td>
                <td>${formatDate(category.created_at)}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" onclick="editIncomeCategory(${category.id})" title="{{ __('msg.edit') }}">
                            <i class="icon-pencil7"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteIncomeCategory(${category.id})" title="{{ __('msg.delete') }}">
                            <i class="icon-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function addIncomeCategory(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.is_active = data.is_active ? true : false;
    
    if (!data.name) {
        showNotification('error', 'Please enter category name');
        return;
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="icon-spinner4 spinner mr-2"></i> {{ __("msg.saving") }}...';
    submitBtn.disabled = true;
    
    fetch('/finance/categories/income', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res => {
        if (res.success) {
            $('#addIncomeCategoryModal').modal('hide');
            form.reset();
            showNotification('success', res.message || 'Category added successfully');
            loadIncomeCategories();
        } else {
            showNotification('error', res.message || 'Error adding category');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', 'Error adding category: ' + err.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function editIncomeCategory(id) {
    fetch(`/finance/categories/income/${id}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res => {
        if (res.success) {
            populateEditCategoryForm(res.data);
            $('#editIncomeCategoryModal').modal('show');
        } else {
            showNotification('error', res.message || 'Error loading category data');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', 'Error loading category data: ' + err.message);
    });
}

function populateEditCategoryForm(category) {
    document.getElementById('edit_category_id').value = category.id;
    document.getElementById('edit_name').value = category.name || '';
    document.getElementById('edit_description').value = category.description || '';
    document.getElementById('edit_is_active').checked = category.is_active;
}

function updateIncomeCategory(e) {
    e.preventDefault();
    
    const form = e.target;
    const categoryId = document.getElementById('edit_category_id').value;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    delete data._method;
    data.is_active = data.is_active ? true : false;
    
    if (!data.name) {
        showNotification('error', 'Please enter category name');
        return;
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="icon-spinner4 spinner mr-2"></i> {{ __("msg.updating") }}...';
    submitBtn.disabled = true;
    
    fetch(`/finance/categories/income/${categoryId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res => {
        if (res.success) {
            $('#editIncomeCategoryModal').modal('hide');
            form.reset();
            showNotification('success', res.message || 'Category updated successfully');
            loadIncomeCategories();
        } else {
            showNotification('error', res.message || 'Error updating category');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', 'Error updating category: ' + err.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function deleteIncomeCategory(id) {
    if (!confirm('{{ __("msg.confirm_delete_category") }}')) return;
    
    fetch(`/finance/categories/income/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res => {
        if (res.success) {
            showNotification('success', res.message || 'Category deleted successfully');
            loadIncomeCategories();
        } else {
            showNotification('error', res.message || 'Error deleting category');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', 'Error deleting category: ' + err.message);
    });
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
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
$('#addIncomeCategoryModal').on('hidden.bs.modal', function () {
    document.getElementById('addIncomeCategoryForm').reset();
});

$('#editIncomeCategoryModal').on('hidden.bs.modal', function () {
    document.getElementById('editIncomeCategoryForm').reset();
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
</style>
@endsection