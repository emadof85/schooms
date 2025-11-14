<div class="modal fade" id="addSalaryStructureModal" tabindex="-1" role="dialog" aria-labelledby="addSalaryStructureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalaryStructureModalLabel">{{ __('salary.add_salary_structure') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addSalaryStructureForm" action="{{ route('finance.salaries.structures.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="salary_level_id">{{ __('salary.salary_levels') }} *</label>
                        <select class="form-control" id="salary_level_id" name="salary_level_id" required>
                            <option value="">Select Salary Level</option>
                            @foreach($salary_levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="component_name">{{ __('salary.component_name') }} *</label>
                        <input type="text" class="form-control" id="component_name" name="component_name" required>
                    </div>

                    <div class="form-group">
                        <label for="component_type">{{ __('salary.component_type') }} *</label>
                        <select class="form-control" id="component_type" name="component_type" required>
                            <option value="basic">{{ __('salary.basic') }}</option>
                            <option value="allowance">{{ __('salary.allowance') }}</option>
                            <option value="deduction">{{ __('salary.deduction') }}</option>
                            <option value="bonus">{{ __('salary.bonus') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="calculation_type">{{ __('salary.calculation_type') }} *</label>
                        <select class="form-control" id="calculation_type" name="calculation_type" required>
                            <option value="fixed">{{ __('salary.fixed') }}</option>
                            <option value="percentage">{{ __('salary.percentage') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="amount">{{ __('salary.amount') }} *</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
                    </div>

                    <div class="form-group" id="percentage_of_group" style="display: none;">
                        <label for="percentage_of">Percentage Of *</label>
                        <input type="text" class="form-control" id="percentage_of" name="percentage_of" placeholder="e.g., Basic Salary">
                    </div>

                    <div class="form-group">
                        <label for="is_active">Status *</label>
                        <select class="form-control" id="is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('msg.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('msg.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>