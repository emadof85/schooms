<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ __('salary.salary_records') }}</h5>
        <div class="header-elements">
            <div class="list-icons">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSalaryRecordModal">
                    <i class="icon-plus3 mr-1"></i> {{ __('salary.add_salary_record') }}
                </button>
                <button type="button" class="btn btn-success btn-sm ml-1" data-toggle="modal" data-target="#calculateSalaryModal">
                    <i class="icon-calculator mr-1"></i> {{ __('salary.calculate_salary') }}
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-control" id="filterPeriod">
                    <option value="">{{ __('salary.all_periods') }}</option>
                    @foreach($pay_periods as $period)
                        <option value="{{ $period }}">{{ $period }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filterEmployee">
                    <option value="">{{ __('salary.all_employees') }}</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filterStatus">
                    <option value="">{{ __('salary.all_statuses') }}</option>
                    <option value="pending">{{ __('salary.pending') }}</option>
                    <option value="paid">{{ __('salary.paid') }}</option>
                    <option value="failed">{{ __('salary.failed') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-light" onclick="loadSalaryRecords()">
                    <i class="icon-filter3 mr-1"></i> Filter
                </button>
            </div>
        </div>

        <!-- Salary Records Table -->
        <div class="table-responsive" id="salaryRecordsTable">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('salary.employee') }}</th>
                        <th>{{ __('salary.pay_period') }}</th>
                        <th>{{ __('salary.basic_salary') }}</th>
                        <th>{{ __('salary.net_salary') }}</th>
                        <th>{{ __('salary.payment_status') }}</th>
                        <th>{{ __('salary.payment_date') }}</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salary_records as $record)
                    <tr>
                        <td>{{ $record->employee->user->name ?? 'N/A' }}</td>
                        <td>{{ $record->pay_period }}</td>
                        <td>{{ number_format($record->basic_salary, 2) }}</td>
                        <td>{{ number_format($record->net_salary, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $record->payment_status == 'paid' ? 'success' : ($record->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                {{ __("salary.{$record->payment_status}") }}
                            </span>
                        </td>
                        <td>{{ $record->payment_date->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm" onclick="generatePayslip({{ $record->id }})">
                                    <i class="icon-file-text2"></i>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="editSalaryRecord({{ $record->id }})">
                                    <i class="icon-pencil7"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSalaryRecord({{ $record->id }})">
                                    <i class="icon-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>