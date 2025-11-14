<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ __('salary.deductions_bonuses') }}</h5>
        <div class="header-elements">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDeductionsBonusesModal">
                <i class="icon-plus3 mr-1"></i> {{ __('salary.add_deduction_bonus') }}
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-control" id="filterType" onchange="filterDeductionsBonuses()">
                    <option value="">All Types</option>
                    <option value="deduction">{{ __('salary.deduction') }}</option>
                    <option value="bonus">{{ __('salary.bonus') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filterEmployee" onchange="filterDeductionsBonuses()">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->user->name ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filterStatus" onchange="filterDeductionsBonuses()">
                    <option value="">All Status</option>
                    <option value="active">{{ __('salary.active') }}</option>
                    <option value="inactive">{{ __('salary.inactive') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filterRecurring" onchange="filterDeductionsBonuses()">
                    <option value="">All Frequencies</option>
                    <option value="one_time">{{ __('salary.one_time') }}</option>
                    <option value="monthly">{{ __('salary.monthly') }}</option>
                    <option value="yearly">{{ __('salary.yearly') }}</option>
                </select>
            </div>
        </div>

        <!-- Deductions & Bonuses Table -->
        <div class="table-responsive" id="deductionsBonusesTable">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Effective Date</th>
                        <th>Recurring</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deductions_bonuses ?? [] as $item)
                    <tr>
                        <td>{{ $item->employee->user->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-{{ $item->type == 'bonus' ? 'success' : 'warning' }}">
                                {{ __("salary.{$item->type}") }}
                            </span>
                        </td>
                        <td>{{ $item->description }}</td>
                        <td>{{ number_format($item->amount, 2) }}</td>
                        <td>{{ $item->effective_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-light">
                                {{ __("salary.{$item->recurring}") }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $item->status == 'active' ? 'success' : 'danger' }}">
                                {{ __("salary.{$item->status}") }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning btn-sm" onclick="editDeductionsBonuses({{ $item->id }})" title="Edit">
                                    <i class="icon-pencil7"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteDeductionsBonuses({{ $item->id }})" title="Delete">
                                    <i class="icon-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="icon-info22 mr-2"></i> No deductions or bonuses found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Filter deductions and bonuses
    function filterDeductionsBonuses() {
        const type = $('#filterType').val();
        const employeeId = $('#filterEmployee').val();
        const status = $('#filterStatus').val();
        const recurring = $('#filterRecurring').val();

        $.ajax({
            url: '{{ route("finance.salaries.deductions_bonuses.filter") }}',
            method: 'GET',
            data: {
                type: type,
                employee_id: employeeId,
                status: status,
                recurring: recurring
            },
            success: function(response) {
                if (response.success) {
                    $('#deductionsBonusesTable').html(response.html);
                }
            }
        });
    }

    // Edit deduction/bonus
    function editDeductionsBonuses(id) {
        $.ajax({
            url: `{{ url('finance/salaries/deductions-bonuses') }}/${id}/edit`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Populate and show edit modal
                    $('#editDeductionsBonusesModal').modal('show');
                    populateEditForm(response.deductionBonus);
                }
            }
        });
    }

    // Delete deduction/bonus
    function deleteDeductionsBonuses(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: `{{ url('finance/salaries/deductions-bonuses') }}/${id}/destroy`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        filterDeductionsBonuses(); // Reload the table
                        showToast('success', response.message);
                    } else {
                        showToast('error', response.message);
                    }
                }
            });
        }
    }

    // Initialize when tab is shown
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if ($(e.target).attr('href') === '#deductions-bonuses') {
            filterDeductionsBonuses();
        }
    });
</script>
@endpush