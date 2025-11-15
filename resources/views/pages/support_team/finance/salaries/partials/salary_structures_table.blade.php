@if(isset($salary_structures) && (is_array($salary_structures) || is_object($salary_structures)))
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
        @forelse($salary_structures as $structure)
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
@else
<div class="alert alert-warning">
    <i class="icon-warning mr-2"></i> {{ __('salary.invalid_data_structure') }}
</div>
@endif