<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ __('salary.salary_structures') }}</h5>
        <div class="header-elements">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSalaryStructureModal">
                <i class="icon-plus3 mr-1"></i> {{ __('salary.add_salary_structure') }}
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <select class="form-control" id="filterSalaryLevel" onchange="filterStructures()">
                    <option value="">All Salary Levels</option>
                    @foreach($salary_levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-control" id="filterComponentType" onchange="filterStructures()">
                    <option value="">All Component Types</option>
                    <option value="basic">{{ __('salary.basic') }}</option>
                    <option value="allowance">{{ __('salary.allowance') }}</option>
                    <option value="deduction">{{ __('salary.deduction') }}</option>
                    <option value="bonus">{{ __('salary.bonus') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-control" id="filterStatus" onchange="filterStructures()">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Salary Structures Table -->
        <div class="table-responsive" id="salaryStructuresTable">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('salary.component_name') }}</th>
                        <th>{{ __('salary.salary_levels') }}</th>
                        <th>{{ __('salary.component_type') }}</th>
                        <th>{{ __('salary.calculation_type') }}</th>
                        <th>{{ __('salary.amount') }}</th>
                        <th>Status</th>
                        <th>{{ __('msg.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salary_structures ?? [] as $structure)
                    <tr>
                        <td>{{ $structure->component_name }}</td>
                        <td>{{ $structure->salaryLevel->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-{{ $structure->component_type == 'basic' ? 'primary' : ($structure->component_type == 'allowance' ? 'success' : ($structure->component_type == 'bonus' ? 'info' : 'warning')) }}">
                                {{ __("salary.{$structure->component_type}") }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-light">
                                {{ __("salary.{$structure->calculation_type}") }}
                            </span>
                        </td>
                        <td>
                            @if($structure->calculation_type == 'percentage')
                                {{ $structure->amount }}%
                                @if($structure->percentage_of)
                                    <br><small class="text-muted">of {{ $structure->percentage_of }}</small>
                                @endif
                            @else
                                {{ number_format($structure->amount, 2) }}
                            @endif
                        </td>
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
                        <td colspan="7" class="text-center text-muted">
                            <i class="icon-info22 mr-2"></i> {{ __('msg.no_records_found') }}
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
    // Filter structures
    function filterStructures() {
        const levelId = $('#filterSalaryLevel').val();
        const componentType = $('#filterComponentType').val();
        const status = $('#filterStatus').val();

        $.ajax({
            url: '{{ route("finance.salaries.structures.filter") }}',
            method: 'GET',
            data: {
                salary_level_id: levelId,
                component_type: componentType,
                is_active: status
            },
            success: function(response) {
                if (response.success) {
                    $('#salaryStructuresTable').html(response.html);
                }
            }
        });
    }

    // Edit salary structure
    function editSalaryStructure(id) {
        $.ajax({
            url: `{{ url('finance/salaries/structures') }}/${id}/edit`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Populate edit modal with data
                    $('#editSalaryStructureModal').modal('show');
                    populateEditForm(response.structure);
                }
            }
        });
    }

    // Delete salary structure
    function deleteSalaryStructure(id) {
        if (confirm('{{ __("msg.confirm_delete") }}')) {
            $.ajax({
                url: `{{ url('finance/salaries/structures') }}/${id}/destroy`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Reload the structures
                        filterStructures();
                        showToast('success', response.message);
                    } else {
                        showToast('error', response.message);
                    }
                }
            });
        }
    }

    // Populate edit form
    function populateEditForm(structure) {
        $('#edit_structure_id').val(structure.id);
        $('#edit_salary_level_id').val(structure.salary_level_id);
        $('#edit_component_name').val(structure.component_name);
        $('#edit_component_type').val(structure.component_type);
        $('#edit_amount').val(structure.amount);
        $('#edit_calculation_type').val(structure.calculation_type);
        $('#edit_percentage_of').val(structure.percentage_of);
        $('#edit_is_active').val(structure.is_active ? '1' : '0');
        
        // Show/hide percentage field based on calculation type
        togglePercentageField(structure.calculation_type);
    }

    // Toggle percentage field visibility
    function togglePercentageField(calculationType) {
        const percentageGroup = $('#percentage_of_group');
        if (calculationType === 'percentage') {
            percentageGroup.show();
        } else {
            percentageGroup.hide();
        }
    }

    // Initialize calculation type toggle
    $(document).ready(function() {
        $('#calculation_type, #edit_calculation_type').on('change', function() {
            togglePercentageField($(this).val());
        });
    });

    // Show toast notification
    function showToast(type, message) {
        // Implement your toast notification here
        // This could be using your existing notification system
        alert(message); // Temporary simple alert
    }
</script>
@endpush