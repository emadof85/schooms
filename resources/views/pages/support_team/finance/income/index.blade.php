@extends('layouts.master')

@section('page_title', __('msg.income_management'))

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">{{ __('msg.income_management') }}</h6>
        <div class="header-elements">
            <div class="btn-group mr-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addIncomeModal">
                    {{ __('msg.add_income') }}
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    {{ __('msg.export') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="#" class="dropdown-item" onclick="exportToExcel()">
                        <i class="icon-file-excel mr-2"></i> {{ __('msg.export_excel') }}
                    </a>
                    <a href="#" class="dropdown-item" onclick="exportToPdf()">
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
                <button id="load_incomes" class="btn btn-primary">{{ __('msg.load') }}</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="income_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('msg.date') }}</th>
                        <th>{{ __('msg.category') }}</th>
                        <th>{{ __('msg.title') }}</th>
                        <th>{{ __('msg.amount') }}</th>
                        <th>{{ __('msg.payment_method') }}</th>
                        <th>{{ __('msg.received_from') }}</th>
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

<!-- Add Income Modal -->
<div class="modal fade" id="addIncomeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('msg.add_income') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addIncomeForm">
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
                        <input type="text" name="title" class="form-control" required placeholder="Enter income title">
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.amount') }} *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.date') }} *</label>
                        <input type="date" name="income_date" class="form-control" required value="{{ date('Y-m-d') }}">
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
                        <label>{{ __('msg.received_from') }} *</label>
                        <input type="text" name="received_from" class="form-control" required placeholder="Name of payer">
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.description') }}</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Optional description"></textarea>
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

<!-- Edit Income Modal -->
<div class="modal fade" id="editIncomeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('msg.edit_income') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editIncomeForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_income_id">
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
                        <input type="date" name="income_date" id="edit_income_date" class="form-control" required>
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
                        <label>{{ __('msg.received_from') }} *</label>
                        <input type="text" name="received_from" id="edit_received_from" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('msg.description') }}</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
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
document.getElementById('load_incomes').addEventListener('click', loadIncomes);
document.getElementById('addIncomeForm').addEventListener('submit', addIncome);
document.getElementById('editIncomeForm').addEventListener('submit', updateIncome);

function loadIncomes() {
    console.log('=== LOAD INCOMES DEBUG ===');
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const categoryId = document.getElementById('category_filter').value;
    
    console.log('Filters - Start:', startDate, 'End:', endDate, 'Category:', categoryId);
    
    // Show loading state
    const tbody = document.querySelector('#income_table tbody');
    tbody.innerHTML = '<tr><td colspan="8" class="text-center">{{ __("msg.loading") }}...</td></tr>';
    console.log('Loading state set');
    
    const params = new URLSearchParams({
        start_date: startDate,
        end_date: endDate
    });
    
    if (categoryId) {
        params.append('category_id', categoryId);
    }
    
    console.log('Fetching URL:', `/finance/incomes/data?${params}`);
    
    fetch(`/finance/incomes/data?${params}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(r => {
        console.log('Load incomes response status:', r.status);
        if (!r.ok) {
            throw new Error('Network response was not ok: ' + r.status);
        }
        return r.json();
    })
    .then(res => {
        console.log('Load incomes response data:', res);
        if (res.success) {
            console.log('Data received, rendering table with', res.data.data?.length || 0, 'items');
            renderIncomeTable(res.data.data || []);
        } else {
            console.log('Error in response:', res.message);
            showNotification('error', res.message || 'Error loading incomes');
        }
    })
    .catch(err => {
        console.error('Error loading incomes:', err);
        showNotification('error', 'Error loading incomes: ' + err.message);
    });
}

function renderIncomeTable(incomes) {
    console.log('=== RENDER INCOME TABLE DEBUG ===');
    console.log('Incomes data received:', incomes);
    
    const tbody = document.querySelector('#income_table tbody');
    
    if (!incomes || incomes.length === 0) {
        console.log('No data found, showing empty message');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">{{ __("msg.no_data_found") }}</td></tr>';
        return;
    }
    
    console.log('Rendering', incomes.length, 'income records');
    
    let html = '';
    incomes.forEach((income, index) => {
        // Escape single quotes in title for JavaScript
        const safeTitle = (income.title || '').replace(/'/g, "\\'");
        
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${formatDate(income.income_date)}</td>
                <td>${income.category ? income.category.name : 'N/A'}</td>
                <td>${income.title || 'N/A'}</td>
                <td>${formatCurrency(income.amount || 0)}</td>
                <td><span class="badge badge-light">${income.payment_method || 'N/A'}</span></td>
                <td>${income.received_from || 'N/A'}</td>
                <td>
                    <div class="btn-group">
                        <div class="btn-group mr-2">
                        <button class="btn btn-sm btn-primary" onclick="editIncome(${income.id})" title="{{ __('msg.edit') }}">
                            <i class="icon-pencil7"></i>
                        </button>
                        </div>
                        <div class="btn-group mr-2">
                        <button class="btn btn-sm btn-danger" onclick="deleteIncome(${income.id}, '${safeTitle}', ${income.amount || 0})" title="{{ __('msg.delete') }}">
                            <i class="icon-trash"></i>
                        </button>
                    </div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    console.log('Table rendered successfully');
}

function addIncome(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Basic validation
    if (!data.category_id || !data.title || !data.amount || !data.income_date || !data.payment_method || !data.received_from) {
        showNotification('error', 'Please fill all required fields');
        return;
    }
    
    // Show loading state on button
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="icon-spinner4 spinner mr-2"></i> {{ __("msg.saving") }}...';
    submitBtn.disabled = true;
    
    fetch('/finance/incomes/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        if (!r.ok) {
            throw new Error('Network response was not ok');
        }
        return r.json();
    })
    .then(res => {
        if (res.success) {
            $('#addIncomeModal').modal('hide');
            form.reset();
            
            // Reset Select2 if used
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').val(null).trigger('change');
            }
            
            showNotification('success', res.message || 'Income added successfully');
            loadIncomes();
        } else {
            showNotification('error', res.message || 'Error adding income');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', 'Error adding income: ' + err.message);
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function editIncome(id) {
    console.log('=== EDIT INCOME DEBUG ===');
    console.log('Editing income ID:', id);
    
    if (!id) {
        console.error('No ID provided for edit');
        showNotification('error', 'Invalid income ID');
        return;
    }
    
    showNotification('info', 'Loading income data...');
    
    fetch(`/finance/incomes/edit/${id}`, {
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
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(res => {
        console.log('Edit response data:', res);
        if (res.success) {
            populateEditForm(res.data);
            $('#editIncomeModal').modal('show');
            showNotification('success', 'Income data loaded successfully');
        } else {
            showNotification('error', res.message || 'Error loading income data');
        }
    })
    .catch(err => {
        console.error('Edit error:', err);
        showNotification('error', 'Error loading income data: ' + err.message);
    });
}

function populateEditForm(income) {
    console.log('Populating form with income:', income);
    
    document.getElementById('edit_income_id').value = income.id;
    document.getElementById('edit_category_id').value = income.category_id;
    document.getElementById('edit_title').value = income.title || '';
    document.getElementById('edit_amount').value = income.amount || '';
    document.getElementById('edit_income_date').value = income.income_date || '';
    document.getElementById('edit_payment_method').value = income.payment_method || '';
    document.getElementById('edit_received_from').value = income.received_from || '';
    document.getElementById('edit_description').value = income.description || '';
    
    // Update Select2 if used
    if (typeof $.fn.select2 !== 'undefined') {
        $('#edit_category_id').trigger('change');
    }
}

function updateIncome(e) {
    e.preventDefault();
    
    const form = e.target;
    const incomeId = document.getElementById('edit_income_id').value;
    
    if (!incomeId) {
        showNotification('error', 'Invalid income ID');
        return;
    }
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Remove the method field as we're using PUT
    delete data._method;
    
    // Basic validation
    if (!data.category_id || !data.title || !data.amount || !data.income_date || !data.payment_method || !data.received_from) {
        showNotification('error', 'Please fill all required fields');
        return;
    }
    
    // Show loading state on button
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="icon-spinner4 spinner mr-2"></i> {{ __("msg.updating") }}...';
    submitBtn.disabled = true;
    // update 
    fetch(`/finance/incomes/update/${incomeId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        if (!r.ok) {
            throw new Error('Network response was not ok');
        }
        return r.json();
    })
    .then(res => {
        if (res.success) {
            $('#editIncomeModal').modal('hide');
            form.reset();
            showNotification('success', res.message || 'Income updated successfully');
            loadIncomes();
        } else {
            showNotification('error', res.message || 'Error updating income');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('error', 'Error updating income: ' + err.message);
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// SweetAlert Delete Confirmation
function deleteIncome(id, title, amount) {
    console.log('=== DELETE INCOME DEBUG ===');
    console.log('Delete income ID:', id, 'Title:', title, 'Amount:', amount);
    
    if (!id) {
        console.error('No ID provided for delete');
        showNotification('error', 'Invalid income ID');
        return;
    }
    
    swal({
        title: "{{ __('msg.are_you_sure') }}?",
        text: `{{ __('msg.are_you_sure_delete_income') }}\n"${title}" - ${formatCurrency(amount)}`,
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
            fetch(`/finance/incomes/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            })
            .then(r => {
                console.log('Delete response status:', r.status);
                if (!r.ok) {
                    throw new Error('Network response was not ok: ' + r.status);
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
                        text: res.message || 'Income deleted successfully',
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    });
                    
                    // Reload the incomes after a short delay
                    setTimeout(() => {
                        loadIncomes();
                    }, 500);
                    
                } else {
                    // Show error message
                    swal({
                        title: "{{ __('msg.error') }}!",
                        text: res.message || 'Error deleting income',
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
                    text: 'Error deleting income: ' + err.message,
                    icon: "error",
                    button: "{{ __('msg.ok') }}"
                });
            });
        }
    });
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
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
// export - UPDATED to match new route method names
function exportToExcel() {
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
    
    // Show loading
    showNotification('info', 'Preparing Excel export...');
    
    // UPDATED: Changed to exportIncomeExcel
    window.location.href = `/finance/incomes/export/excel?${params}`;
}

function exportToPdf() {
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
    
    // Show loading
    showNotification('info', 'Generating PDF report...');
    
    // UPDATED: Changed to exportIncomePdf
    window.location.href = `/finance/incomes/export/pdf?${params}`;
}
// Load incomes on page load
document.addEventListener('DOMContentLoaded', function() {
    loadIncomes();
    
    // Reset form when modal is closed
    $('#addIncomeModal').on('hidden.bs.modal', function () {
        document.getElementById('addIncomeForm').reset();
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').val(null).trigger('change');
        }
    });
    
    $('#editIncomeModal').on('hidden.bs.modal', function () {
        document.getElementById('editIncomeForm').reset();
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