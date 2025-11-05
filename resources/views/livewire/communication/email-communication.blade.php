<div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('msg.send_email_communication') }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="sendEmail">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('msg.educational_stage') }}</label>
                                    <select wire:model.live="selectedGrade" class="form-control" onchange="getEducationalStageClasses(this.value)">
                                        <option value="">{{ __('msg.all_educational_stages') }}</option>
                                        @foreach($grades as $grade)
                                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('msg.class') }}</label>
                                    <select wire:model.live="selectedClass" class="form-control" onchange="getClassSections(this.value)" id="selectedClass">
                                        <option value="">{{ __('msg.all_classes') }}</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('msg.section') }}</label>
                                    <select class="form-control" id="selectedSection" onchange="filterStudentsBySection()">
                                        <option value="">{{ __('msg.all_sections') }}</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('msg.subject') }}</label>
                            <input type="text" wire:model="subject" class="form-control" required>
                            @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ __('msg.message') }}</label>
                            <textarea wire:model="message" class="form-control" rows="5" required></textarea>
                            @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ __('msg.select_students') }}</label>
                            <div class="border p-2 student-checkboxes" style="max-height: 200px; overflow-y: auto;">
                                @if(count($students) > 0)
                                    @foreach($students as $student)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model="selectedStudents" value="{{ $student->id }}" id="student{{ $student->id }}">
                                            <label class="form-check-label" for="student{{ $student->id }}">
                                                {{ $student->user->name }} - {{ $student->adm_no }} ({{ $student->my_class->name }} - {{ $student->section->name }})
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ __('msg.no_students_found') }}</p>
                                @endif
                            </div>
                            @error('selectedStudents') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('msg.send_email') }}</span>
                            <span wire:loading>{{ __('msg.sending_email') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
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
</div>
