@extends('layouts.master')

@section('page_title')
    {{ __('msg.email_communication') }}
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('msg.send_email_communication') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('support_team.communication.send_email') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('msg.recipient_type') }}</label>
                                <select name="recipient_type" class="form-control" id="recipientType" onchange="filterRecipients()">
                                    <option value="students">{{ __('msg.students') }}</option>
                                    <option value="parents">{{ __('msg.parents') }}</option>
                                    <option value="both">{{ __('msg.both') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('msg.educational_stage') }}</label>
                                <select name="selected_grade" class="form-control" onchange="getClasses(this.value)">
                                    <option value="">{{ __('msg.all_educational_stages') }}</option>
                                    @foreach($grades as $grade)
                                        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('msg.class') }}</label>
                                <select name="selected_class" class="form-control" onchange="getSections(this.value)" id="selectedClass">
                                    <option value="">{{ __('msg.all_classes') }}</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('msg.section') }}</label>
                                <select name="selected_section" class="form-control" id="selectedSection">
                                    <option value="">{{ __('msg.all_sections') }}</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('msg.subject') }}</label>
                                <input type="text" name="subject" class="form-control" required>
                                @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('msg.message') }}</label>
                                <textarea name="message" class="form-control" rows="3" required></textarea>
                                @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ __('msg.select_recipients') }}</label>
                        <div class="border p-2 recipient-checkboxes" style="max-height: 300px; overflow-y: auto;" id="recipientsContainer">
                            <p class="text-muted">{{ __('msg.select_filters_above') }}</p>
                        </div>
                        @error('selected_students') <span class="text-danger">{{ $message }}</span> @enderror
                        @error('selected_parents') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('msg.send_email') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('msg.recent_communications') }}</h5>
            </div>
            <div class="card-body">
                @if(count($communications) > 0)
                    @foreach($communications as $comm)
                        <div class="border-bottom pb-2 mb-2">
                            <strong>{{ $comm->subject }}</strong><br>
                            <small class="text-muted">
                                {{ __('msg.sent_by') }} {{ $comm->sender->name }} {{ __('msg.on') }} {{ $comm->created_at->format('M d, Y H:i') }}
                            </small><br>
                            <span class="badge badge-{{ $comm->status == 'sent' ? 'success' : ($comm->status == 'failed' ? 'danger' : 'warning') }}">
                                {{ __('msg.' . $comm->status) }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">{{ __('msg.no_communications_sent') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if (session()->has('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
@endif

@if (session()->has('error'))
    <div class="alert alert-danger mt-3">
        {{ session('error') }}
    </div>
@endif

<script>
function getClasses(gradeId) {
    fetch('{{ route("support_team.communication.get_classes") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ grade_id: gradeId })
    })
    .then(response => response.json())
    .then(data => {
        const classSelect = document.getElementById('selectedClass');
        classSelect.innerHTML = '<option value="">{{ __("msg.all_classes") }}</option>';
        data.forEach(cls => {
            classSelect.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
        });
        filterRecipients();
    });
}

function getSections(classId) {
    fetch('{{ route("support_team.communication.get_sections") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ class_id: classId })
    })
    .then(response => response.json())
    .then(data => {
        const sectionSelect = document.getElementById('selectedSection');
        sectionSelect.innerHTML = '<option value="">{{ __("msg.all_sections") }}</option>';
        data.forEach(section => {
            sectionSelect.innerHTML += `<option value="${section.id}">${section.name}</option>`;
        });
        filterRecipients();
    });
}

function filterRecipients() {
    const recipientType = document.getElementById('recipientType').value;
    const selectedGrade = document.querySelector('select[name="selected_grade"]').value;
    const selectedClass = document.getElementById('selectedClass').value;
    const selectedSection = document.getElementById('selectedSection').value;

    fetch('{{ route("support_team.communication.filter_recipients") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            recipient_type: recipientType,
            selected_grade: selectedGrade,
            selected_class: selectedClass,
            selected_section: selectedSection
        })
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('recipientsContainer');
        container.innerHTML = '';

        if ((recipientType === 'students' || recipientType === 'both') && data.students.length > 0) {
            container.innerHTML += `
                <div class="mb-3">
                    <h6 class="text-primary">{{ __('msg.students') }}</h6>
                    ${data.students.map(student => `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="selected_students[]" value="${student.id}" id="student${student.id}">
                            <label class="form-check-label" for="student${student.id}">
                                <strong>${student.user.name}</strong> - ${student.adm_no} (${student.my_class.name} - ${student.section.name})
                                <br>
                                ${student.user.email ? `<small class="text-muted">${student.user.email}</small>` : `<small class="text-danger">{{ __('msg.no_email_address') }}</small>`}
                            </label>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        if ((recipientType === 'parents' || recipientType === 'both') && data.parents.length > 0) {
            container.innerHTML += `
                <div class="mb-3">
                    <h6 class="text-success">{{ __('msg.parents') }}</h6>
                    ${data.parents.map(parent => `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="selected_parents[]" value="${parent.id}" id="parent${parent.id}">
                            <label class="form-check-label" for="parent${parent.id}">
                                <strong>${parent.my_parent.user.name}</strong> (Parent of ${parent.user.name})
                                <br>
                                ${parent.my_parent.user.email ? `<small class="text-muted">${parent.my_parent.user.email}</small>` : `<small class="text-danger">{{ __('msg.no_email_address') }}</small>`}
                            </label>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        if (((recipientType === 'students' || recipientType === 'both') && data.students.length === 0) &&
            ((recipientType === 'parents' || recipientType === 'both') && data.parents.length === 0)) {
            container.innerHTML = '<p class="text-muted">{{ __("msg.no_recipients_found") }}</p>';
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    filterRecipients();
});
</script>
@endsection