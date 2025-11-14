@extends('layouts.master')

@section('page_title', __('msg.expense_management'))

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">{{ __('msg.expense_management') }}</h6>
        <div class="header-elements">
            <div class="btn-group mr-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addExpenseModal">
                    {{ __('msg.add_expense') }}
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    {{ __('msg.export') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="#" class="dropdown-item" onclick="exportExpensesToExcel()">
                        <i class="icon-file-excel mr-2"></i> {{ __('msg.export_excel') }}
                    </a>
                    <a href="#" class="dropdown-item" onclick="exportExpensesToPdf()">
                        <i class="icon-file-pdf mr-2"></i> {{ __('msg.export_pdf') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="form-row mb-3">
            <div class="col-md-3">
                <input type="date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}">
            </div>
            <div class="col-md-3">
                <input type="date" id="end_date" class="form-control" value="{{ date('Y-m-t') }}">
            </div>
            <div class="col-md-3">
                <select id="category_filter" class="form-control">
                    <option value="">{{ __('msg.all_categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button id="load_expenses" class="btn btn-primary">{{ __('msg.load') }}</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="expense_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('msg.date') }}</th>
                        <th>{{ __('msg.category') }}</th>
                        <th>{{ __('msg.title') }}</th>
                        <th>{{ __('msg.amount') }}</th>
                        <th>{{ __('msg.payment_method') }}</th>
                        <th>{{ __('msg.paid_to') }}</th>
                        <th>{{ __('msg.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center">{{ __('msg.loading') }}...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('msg.add_expense') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addExpenseForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('msg.category') }} *</label>
                        <select name="category_id" class="form-control select2" required style="width: 100%;">
                            <option value="">{{ __('msg.select_category') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.title') }} *</label>
                        <input type="text" name="title" class="form-control" required placeholder="{{ __('msg.enter_expense_title') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.amount') }} *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.date') }} *</label>
                        <input type="date" name="expense_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.payment_method') }} *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="">{{ __('msg.select_payment_method') }}</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.paid_to') }} *</label>
                        <input type="text" name="paid_to" class="form-control" required placeholder="{{ __('msg.name_of_payee') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.description') }}</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="{{ __('msg.optional_description') }}"></textarea>
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

<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('msg.edit_expense') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editExpenseForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_expense_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('msg.category') }} *</label>
                        <select name="category_id" id="edit_category_id" class="form-control select2" required style="width: 100%;">
                            <option value="">{{ __('msg.select_category') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.title') }} *</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.amount') }} *</label>
                        <input type="number" name="amount" id="edit_amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.date') }} *</label>
                        <input type="date" name="expense_date" id="edit_expense_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.payment_method') }} *</label>
                        <select name="payment_method" id="edit_payment_method" class="form-control" required>
                            <option value="">{{ __('msg.select_payment_method') }}</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.paid_to') }} *</label>
                        <input type="text" name="paid_to" id="edit_paid_to" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.description') }}</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
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
<script>
// RTL Support
const isRTL = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
if (isRTL) {
    document.body.classList.add('rtl');
    document.body.setAttribute('dir', 'rtl');
}

const CSRF_TOKEN = "{{ csrf_token() }}";

// Use in your AJAX calls
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
    }
});

// Initialize Select2 if available
if (typeof $.fn.select2 !== 'undefined') {
    $('.select2').select2({
        placeholder: "{{ __('msg.select_category') }}",
        allowClear: true,
        dir: isRTL ? 'rtl' : 'ltr'
    });
}

// Event Listeners
document.getElementById('load_expenses').addEventListener('click', loadExpenses);
document.getElementById('addExpenseForm').addEventListener('submit', addExpense);
document.getElementById('editExpenseForm').addEventListener('submit', updateExpense);

function loadExpenses() {
    console.log('=== LOAD EXPENSES DEBUG ===');
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const categoryId = document.getElementById('category_filter').value;
    
    console.log('Filters - Start:', startDate, 'End:', endDate, 'Category:', categoryId);
    
    // Show loading state
    const tbody = document.querySelector('#expense_table tbody');
    tbody.innerHTML = '<tr><td colspan="8" class="text-center">{{ __("msg.loading") }}...</td></tr>';
    console.log('Loading state set');
    
    const params = new URLSearchParams({
        start_date: startDate,
        end_date: endDate
    });
    
    if (categoryId) {
        params.append('category_id', categoryId);
    }
    
    console.log('Fetching URL:', `/finance/expenses/data?${params}`);
    
    fetch(`/finance/expenses/data?${params}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(r => {
        console.log('Load expenses response status:', r.status);
        if (!r.ok) {
            throw new Error('{{ __("msg.network_error") }}: ' + r.status);
        }
        return r.json();
    })
    .then(res => {
        console.log('Load expenses response data:', res);
        if (res.success) {
            console.log('Data received, rendering table with', res.data.data?.length || 0, 'items');
            renderExpenseTable(res.data.data || []);
        } else {
            console.log('Error in response:', res.message);
            showNotification('error', res.message || '{{ __("msg.error_loading_expenses") }}');
        }
    })
    .catch(err => {
        console.error('Error loading expenses:', err);
        showNotification('error', '{{ __("msg.error_loading_expenses") }}: ' + err.message);
    });
}

function renderExpenseTable(expenses) {
    console.log('=== RENDER EXPENSE TABLE DEBUG ===');
    console.log('Expenses data received:', expenses);
    
    const tbody = document.querySelector('#expense_table tbody');
    
    if (!expenses || expenses.length === 0) {
        console.log('No data found, showing empty message');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">{{ __("msg.no_data_found") }}</td></tr>';
        return;
    }
    
    console.log('Rendering', expenses.length, 'expense records');
    
    let html = '';
    expenses.forEach((expense, index) => {
        // Escape single quotes in title for JavaScript
        const safeTitle = (expense.title || '').replace(/'/g, "\\'");
        
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${formatDate(expense.expense_date)}</td>
                <td>${expense.category ? expense.category.name : '{{ __("msg.n_a") }}'}</td>
                <td>${expense.title || '{{ __("msg.n_a") }}'}</td>
                <td>${formatCurrency(expense.amount || 0)}</td>
                <td><span class="badge badge-light">${expense.payment_method || '{{ __("msg.n_a") }}'}</span></td>
                <td>${expense.paid_to || '{{ __("msg.n_a") }}'}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" onclick="editExpense(${expense.id})" title="{{ __('msg.edit') }}">
                            <i class="icon-pencil7"></i>
                        </button>
                    </div>
                        <div class="btn-group mr-2">
                        <button class="btn btn-sm btn-danger" onclick="deleteExpense(${expense.id}, '${safeTitle}', ${expense.amount || 0})" title="{{ __('msg.delete') }}">
                            <i class="icon-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    console.log('Table rendered successfully');
}

function addExpense(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Basic validation
    if (!data.category_id || !data.title || !data.amount || !data.expense_date || !data.payment_method || !data.paid_to) {
        showNotification('error', '{{ __("msg.please_fill_all_required_fields") }}');
        return;
    }
    
    // Show loading state on button
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="icon-spinner4 spinner mr-2"></i> {{ __("msg.saving") }}...';
    submitBtn.disabled = true;
    
    fetch('/finance/expenses/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        if (!r.ok) {
            throw new Error('{{ __("msg.network_error") }}');
        }
        return r.json();
    })
    .then(res => {
        if (res.success) {
            $('#addExpenseModal').modal('hide');
            form.reset();
            
            // Reset Select2 if used
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').val(null).trigger('change');
            }
            
            showNotification('success', res.message || '{{ __("msg.expense_added_success") }}');
            loadExpenses();
        } else {
            showNotification('error', res.message || '{{ __("msg.error_adding_expense") }}');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', '{{ __("msg.error_adding_expense") }}: ' + err.message);
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function editExpense(id) {
    console.log('=== EDIT EXPENSE DEBUG ===');
    console.log('Editing expense ID:', id);
    
    if (!id) {
        console.error('No ID provided for edit');
        showNotification('error', '{{ __("msg.invalid_expense_id") }}');
        return;
    }
    
    showNotification('info', '{{ __("msg.loading_expense_data") }}');
    
    fetch(`/finance/expenses/edit/${id}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Edit response status:', response.status);
        if (!response.ok) {
            throw new Error(`{{ __("msg.network_error") }}: ${response.status}`);
        }
        return response.json();
    })
    .then(res => {
        console.log('Edit response data:', res);
        if (res.success) {
            populateEditExpenseForm(res.data);
            $('#editExpenseModal').modal('show');
            showNotification('success', '{{ __("msg.expense_data_loaded") }}');
        } else {
            showNotification('error', res.message || '{{ __("msg.error_loading_expense_data") }}');
        }
    })
    .catch(err => {
        console.error('Edit error:', err);
        showNotification('error', '{{ __("msg.error_loading_expense_data") }}: ' + err.message);
    });
}

function populateEditExpenseForm(expense) {
    console.log('Populating form with expense:', expense);
    
    document.getElementById('edit_expense_id').value = expense.id;
    document.getElementById('edit_category_id').value = expense.category_id;
    document.getElementById('edit_title').value = expense.title || '';
    document.getElementById('edit_amount').value = expense.amount || '';
    document.getElementById('edit_expense_date').value = expense.expense_date || '';
    document.getElementById('edit_payment_method').value = expense.payment_method || '';
    document.getElementById('edit_paid_to').value = expense.paid_to || '';
    document.getElementById('edit_description').value = expense.description || '';
    
    // Update Select2 if used
    if (typeof $.fn.select2 !== 'undefined') {
        $('#edit_category_id').trigger('change');
    }
}

function updateExpense(e) {
    e.preventDefault();
    
    const form = e.target;
    const expenseId = document.getElementById('edit_expense_id').value;
    
    if (!expenseId) {
        showNotification('error', '{{ __("msg.invalid_expense_id") }}');
        return;
    }
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Remove the method field as we're using PUT
    delete data._method;
    
    // Basic validation
    if (!data.category_id || !data.title || !data.amount || !data.expense_date || !data.payment_method || !data.paid_to) {
        showNotification('error', '{{ __("msg.please_fill_all_required_fields") }}');
        return;
    }
    
    // Show loading state on button
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="icon-spinner4 spinner mr-2"></i> {{ __("msg.updating") }}...';
    submitBtn.disabled = true;
    
    fetch(`/finance/expenses/update/${expenseId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        if (!r.ok) {
            throw new Error('{{ __("msg.network_error") }}');
        }
        return r.json();
    })
    .then(res => {
        if (res.success) {
            $('#editExpenseModal').modal('hide');
            form.reset();
            showNotification('success', res.message || '{{ __("msg.expense_updated_success") }}');
            loadExpenses();
        } else {
            showNotification('error', res.message || '{{ __("msg.error_updating_expense") }}');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', '{{ __("msg.error_updating_expense") }}: ' + err.message);
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// SweetAlert Delete Confirmation
function deleteExpense(id, title, amount) {
    console.log('=== DELETE EXPENSE DEBUG ===');
    console.log('Delete expense ID:', id, 'Title:', title, 'Amount:', amount);
    
    if (!id) {
        console.error('No ID provided for delete');
        showNotification('error', '{{ __("msg.invalid_expense_id") }}');
        return;
    }
    
    swal({
        title: "{{ __('msg.are_you_sure') }}?",
        text: `{{ __('msg.are_you_sure_delete_expense') }}\n"${title}" - ${formatCurrency(amount)}`,
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
            fetch(`/finance/expenses/delete/${id}`, {
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
                        text: res.message || '{{ __("msg.expense_deleted_success") }}',
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    });
                    
                    // Reload the expenses after a short delay
                    setTimeout(() => {
                        loadExpenses();
                    }, 500);
                    
                } else {
                    // Show error message
                    swal({
                        title: "{{ __('msg.error') }}!",
                        text: res.message || '{{ __("msg.error_deleting_expense") }}',
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
                    text: '{{ __("msg.error_deleting_expense") }}: ' + err.message,
                    icon: "error",
                    button: "{{ __('msg.ok') }}"
                });
            });
        }
    });
}

// Export functions for expenses
function exportExpensesToExcel() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const categoryId = document.getElementById('category_filter').value;
    
    const params = new URLSearchParams({
        start_date: startDate,
        end_date: endDate
    });
    
    if (categoryId) {
        params.append('category_id', categoryId);
    }
    
    showNotification('info', '{{ __("msg.preparing_excel_export") }}');
    window.location.href = `/finance/expenses/export/excel?${params}`;
}

function exportExpensesToPdf() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const categoryId = document.getElementById('category_filter').value;
    
    const params = new URLSearchParams({
        start_date: startDate,
        end_date: endDate
    });
    
    if (categoryId) {
        params.append('category_id', categoryId);
    }
    
    showNotification('info', '{{ __("msg.generating_pdf_report") }}');
    window.location.href = `/finance/expenses/export/pdf?${params}`;
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function formatDate(dateString) {
    if (!dateString) return '{{ __("msg.n_a") }}';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Beautiful Notification System
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
    
    // Add to container with RTL consideration
    if (isRTL) {
        container.insertBefore(notification, container.firstChild);
    } else {
        container.appendChild(notification);
    }
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        closeNotification(notificationId);
    }, 5000);
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

// Load expenses on page load
document.addEventListener('DOMContentLoaded', function() {
    loadExpenses();
    
    // Reset form when modal is closed
    $('#addExpenseModal').on('hidden.bs.modal', function () {
        document.getElementById('addExpenseForm').reset();
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').val(null).trigger('change');
        }
    });
    
    $('#editExpenseModal').on('hidden.bs.modal', function () {
        document.getElementById('editExpenseForm').reset();
    });
});
</script>

<style>
.rtl .modal-header .close {
    margin: -1rem -1rem -1rem auto;
}

.rtl .btn-group {
    direction: ltr;
}

.rtl .table th {
    text-align: right;
}

.rtl .form-group label {
    text-align: right;
    display: block;
}

.rtl .modal-footer {
    justify-content: flex-start;
}

.rtl .alert {
    text-align: right;
}

.rtl .btn i {
    margin-left: 0.25rem;
    margin-right: 0;
}

/* Notification animations */
.alert {
    transition: all 0.3s ease-in-out;
}

.alert.fade:not(.show) {
    opacity: 0;
    transform: translateX(100px);
}

/* Spinner animation */
.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* RTL specific styles */
body.rtl {
    direction: rtl;
    text-align: right;
}

body.rtl .mr-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

body.rtl .ml-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
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