@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Attendance</h3>

    <div class="form-row mb-3">
        <div class="col-md-4">
            <select id="class_select" class="form-control">
                <option value="">Select class</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input id="att_date" type="date" class="form-control" value="{{ date('Y-m-d') }}" />
        </div>
        <div class="col-md-2">
            <button id="load" class="btn btn-primary">Load</button>
        </div>
    </div>

    <div id="table_wrap">
        <table class="table table-striped" id="att_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Status</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button id="save" class="btn btn-success">Save</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('load').addEventListener('click', function(){
    var class_id = document.getElementById('class_select').value;
    var date = document.getElementById('att_date').value;
    if (!class_id) return alert('select class');

    fetch('/attendance?class_id='+class_id+'&date='+date, {headers:{'Accept':'application/json'}})
        .then(r=>r.json())
        .then(res=>{
            var tbody = document.querySelector('#att_table tbody');
            tbody.innerHTML = '';
            res.rows.forEach(function(r,i){
                var tr = document.createElement('tr');
                tr.innerHTML = '<td>'+(i+1)+'</td>'+
                    '<td>'+ (r.student.user ? r.student.user.name : 'N/A') +'</td>'+
                    '<td>'+
                        '<select class="form-control status">'+
                            '<option value="present" '+(r.status=='present'? 'selected':'')+'>Present</option>'+
                            '<option value="absent" '+(r.status=='absent'? 'selected':'')+'>Absent</option>'+
                            '<option value="late" '+(r.status=='late'? 'selected':'')+'>Late</option>'+
                            '<option value="excused" '+(r.status=='excused'? 'selected':'')+'>Excused</option>'+
                        '</select>'+
                    '</td>'+
                    '<td><input class="form-control note" value="'+(r.note||'')+'" /></td>'+
                    '<input type="hidden" class="srid" value="'+r.student_record_id+'" />';
                tbody.appendChild(tr);
            });
        });
});

document.getElementById('save').addEventListener('click', function(){
    var class_id = document.getElementById('class_select').value;
    var date = document.getElementById('att_date').value;
    var rows = [];
    document.querySelectorAll('#att_table tbody tr').forEach(function(tr){
        var srid = tr.querySelector('.srid').value;
        var status = tr.querySelector('.status').value;
        var note = tr.querySelector('.note').value;
        rows.push({student_record_id: srid, status: status, note: note});
    });

    fetch('/attendance', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        body: JSON.stringify({class_id: class_id, date: date, items: rows})
    }).then(r=>r.json()).then(res=>{
        alert('Saved: '+ (res.saved || 0));
    });
});
</script>

@endsection
