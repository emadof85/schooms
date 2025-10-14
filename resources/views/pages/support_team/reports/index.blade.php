@extends('layouts.master')

@section('page_title', __('msg.reports'))

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">{{ __('msg.reports') }}</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">{{ __('msg.daily_attendance_summary') }}</h6>
                    </div>
                    <div class="card-body">
                        <form id="daily_form">
                            <div class="form-row mb-3">
                                <div class="col-md-6">
                                    <label for="daily_class_id">{{ __('msg.class') }}</label>
                                    <select id="daily_class_id" class="form-control">
                                        <option value="">{{ __('msg.select_class') }}</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="daily_date">{{ __('msg.date') }}</label>
                                    <input id="daily_date" type="date" class="form-control" value="{{ date('Y-m-d') }}" />
                                </div>
                            </div>
                            <button type="button" id="load_daily" class="btn btn-primary">{{ __('msg.load') }}</button>
                        </form>
                        <div id="daily_summary_info" style="display:none; margin-top:20px;">
                            <p><strong>{{ __('msg.class') }}:</strong> <span id="daily_class_name"></span></p>
                            <p><strong>{{ __('msg.date') }}:</strong> <span id="daily_date_display"></span></p>
                            <p><strong>{{ __('msg.summary') }}:</strong> <span id="daily_summary"></span></p>
                        </div>
                        <table id="daily_table" class="table datatable-button-html5-columns" style="width:100%; display:none;">
                            <thead>
                                <tr>
                                    <th>{{ __('msg.student') }}</th>
                                    <th>{{ __('msg.status') }}</th>
                                    <th>{{ __('msg.note') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">{{ __('msg.student_attendance_sheet') }}</h6>
                    </div>
                    <div class="card-body">
                        <form id="student_form">
                            <div class="form-row mb-3">
                                <div class="col-md-12">
                                    <label for="student_id">{{ __('msg.student') }}</label>
                                    <select id="student_id" class="form-control">
                                        <option value="">{{ __('msg.select_student') }}</option>
                                        @foreach($students as $s)
                                            <option value="{{ $s->id }}">{{ $s->user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-3">
                                <div class="col-md-6">
                                    <label for="start_date">{{ __('msg.start_date') }}</label>
                                    <input id="start_date" type="date" class="form-control" />
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date">{{ __('msg.end_date') }}</label>
                                    <input id="end_date" type="date" class="form-control" />
                                </div>
                            </div>
                            <button type="button" id="load_student" class="btn btn-primary">{{ __('msg.load') }}</button>
                        </form>
                        <div id="student_sheet_info" style="display:none; margin-top:20px;">
                            <p><strong>{{ __('msg.student') }}:</strong> <span id="student_name"></span></p>
                            <p><strong>{{ __('msg.period') }}:</strong> <span id="student_period"></span></p>
                        </div>
                        <table id="student_table" class="table datatable-button-html5-columns" style="width:100%; display:none;">
                            <thead>
                                <tr>
                                    <th>{{ __('msg.date') }}</th>
                                    <th>{{ __('msg.status') }}</th>
                                    <th>{{ __('msg.note') }}</th>
                                    <th>{{ __('msg.marked_by') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$('#load_daily').click(function(){
    var class_id = $('#daily_class_id').val();
    var date = $('#daily_date').val();
    if (!class_id || !date) {
        alert('Please select class and date');
        return;
    }
    $.post('{{ route("reports.daily_summary") }}', {
        class_id: class_id,
        date: date,
        _token: '{{ csrf_token() }}'
    }, function(res){
        $('#daily_class_name').text(res.class);
        $('#daily_date_display').text(res.date);
        var summary = '';
        for (var status in res.summary) {
            summary += status + ': ' + res.summary[status] + ', ';
        }
        $('#daily_summary').text(summary);
        $('#daily_summary_info').show();
        var tbody = $('#daily_table tbody');
        tbody.empty();
        res.rows.forEach(function(r){
            tbody.append('<tr><td>' + r.name + '</td><td>' + r.status + '</td><td>' + r.note + '</td></tr>');
        });
        $('#daily_table').show();
        if (!$.fn.DataTable.isDataTable('#daily_table')) {
            $('#daily_table').DataTable({
                dom: 'Bfrtip',
                buttons: ['pdf', 'excel']
            });
        }
    }).fail(function(){
        alert('Error loading data');
    });
});

$('#load_student').click(function(){
    var student_id = $('#student_id').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    if (!student_id || !start_date || !end_date) {
        alert('Please select student and dates');
        return;
    }
    $.post('{{ route("reports.student_sheet") }}', {
        student_id: student_id,
        start_date: start_date,
        end_date: end_date,
        _token: '{{ csrf_token() }}'
    }, function(res){
        $('#student_name').text(res.student);
        $('#student_period').text(res.start_date + ' to ' + res.end_date);
        $('#student_sheet_info').show();
        var tbody = $('#student_table tbody');
        tbody.empty();
        res.rows.forEach(function(r){
            tbody.append('<tr><td>' + r.date + '</td><td>' + r.status + '</td><td>' + r.note + '</td><td>' + r.marked_by + '</td></tr>');
        });
        $('#student_table').show();
        if (!$.fn.DataTable.isDataTable('#student_table')) {
            $('#student_table').DataTable({
                dom: 'Bfrtip',
                buttons: ['pdf', 'excel']
            });
        }
    }).fail(function(){
        alert('Error loading data');
    });
});
</script>

@endsection
