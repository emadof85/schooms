<div class="modal fade" id="editSalaryStructureModal" tabindex="-1" role="dialog" aria-labelledby="editSalaryStructureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSalaryStructureModalLabel">{{ __('salary.edit_salary_structure') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editSalaryStructureForm" action="{{ route('finance.salaries.structures.update', ['id' => ':id']) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_structure_id" name="id">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_salary_level_id">{{ __('salary.salary_levels') }} *</label>
                        <select class="form-control" id="edit_salary_level_id" name="salary_level_id" required>
                            <option value="">Select Salary Level</option>
                            @foreach($salary_levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_component_name">{{ __('salary.component_name') }} *</label>
                        <input type="text" class="form-control" id="edit_component_name" name="component_name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_component_type">{{ __('salary.component_type') }} *</label>
                        <select class="form-control" id="edit_component_type" name="component_type" required>
                            <option value="basic">{{ __('salary.basic') }}</option>
                            <option value="allowance">{{ __('salary.allowance') }}</option>
                            <option value="deduction">{{ __('salary.deduction') }}</option>
                            <option value="bonus">{{ __('salary.bonus') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_calculation_type">{{ __('salary.calculation_type') }} *</label>
                        <select class="form-control" id="edit_calculation_type" name="calculation_type" required>
                            <option value="fixed">{{ __('salary.fixed') }}</option>
                            <option value="percentage">{{ __('salary.percentage') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_amount">{{ __('salary.amount') }} *</label>
                        <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" min="0" required>
                    </div>

                    <div class="form-group" id="edit_percentage_of_group" style="display: none;">
                        <label for="edit_percentage_of">Percentage Of *</label>
                        <input type="text" class="form-control" id="edit_percentage_of" name="percentage_of" placeholder="e.g., Basic Salary">
                    </div>

                    <div class="form-group">
                        <label for="edit_is_active">Status *</label>
                        <select class="form-control" id="edit_is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('msg.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('msg.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>