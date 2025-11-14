<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ __('salary.salary_levels') }}</h5>
        <div class="header-elements">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSalaryLevelModal">
                <i class="icon-plus3 mr-1"></i> {{ __('salary.add_salary_level') }}
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
                    <i class="icon-reset mr-1"></i> {{ __('salary.reset_filters') }}
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
                                <span class="badge badge-info ml-1" title="Has salary structures">
                                    {{ $level->salaryStructures->count() }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-primary">{{ $level->userType->name ?? 'N/A' }}</span>
                        </td>
                        <td>${{ number_format($level->base_salary, 2) }}</td> {{-- Fixed format_currency --}}
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
                                            {{ $level->name }} (${{ number_format($level->base_salary, 2) }}) {{-- Fixed format_currency --}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="icon-users mr-1"></i> {{ __('salary.assign_to_all') }}
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="icon-info22 mr-1"></i>
                        {{ __('salary.bulk_assign_help') }}
                    </small>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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

        // Bulk assignment form handling
        $('#user_type_id').on('change', function() {
            const userTypeId = $(this).val();
            $('.level-option').hide();
            $(`.level-option[data-user-type="${userTypeId}"]`).show();
            $('#salary_level_id').val('');
        });

        $('#bulkAssignForm').on('submit', function(e) {
            e.preventDefault();
            
            swal({
                title: "{{ __('salary.confirm_bulk_assign') }}",
                text: "This will assign the selected salary level to all employees of this user type.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, assign it!",
                cancelButtonText: "Cancel"
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
                                    title: "Success!",
                                    text: response.message,
                                    type: "success",
                                    confirmButtonText: "OK"
                                });
                                $('#bulkAssignForm')[0].reset();
                                $('.level-option').hide();
                            } else {
                                swal({
                                    title: "Error!",
                                    text: response.message,
                                    type: "error",
                                    confirmButtonText: "Try Again"
                                });
                            }
                        },
                        error: function(xhr) {
                            let message = 'An error occurred during bulk assignment';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            swal({
                                title: "Error!",
                                text: message,
                                type: "error",
                                confirmButtonText: "Try Again"
                            });
                        }
                    });
                }
            });
        });
    });

    function viewStructures(levelId) {
        window.location.href = "{{ route('finance.salaries.structures') }}?level_id=" + levelId;
    }

    function editSalaryLevel(id) {
        // Implement edit functionality
        console.log('Edit salary level:', id);
    }

    function deleteSalaryLevel(id) {
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
            if (result.value) {
                $.ajax({
                    url: "{{ route('finance.salaries.levels.destroy', '') }}/" + id,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            swal({
                                title: "Deleted!",
                                text: response.message,
                                type: "success",
                                confirmButtonText: "OK"
                            }).then(function() {
                                // Remove the row from table without page reload
                                $('#salary-level-' + id).remove();
                                
                                // Also remove from bulk assignment dropdown
                                $('.level-option[value="' + id + '"]').remove();
                            });
                        } else {
                            swal({
                                title: "Error!",
                                text: response.message,
                                type: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function(xhr) {
                        let message = 'An error occurred while deleting the salary level';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        swal({
                            title: "Error!",
                            text: message,
                            type: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            }
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
</script>
@endpush