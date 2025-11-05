<?php

namespace App\Http\Livewire\Communication;

use App\Mail\NotificationEmail;
use App\Models\EducationalStage;
use App\Models\Communication;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class EmailCommunication extends Component
{
    public $subject = '';
    public $message = '';
    public $selectedGrade = '';
    public $selectedClass = '';
    public $selectedSection = '';
    public $selectedStudents = [];
    public $grades = [];
    public $classes = [];
    public $sections = [];
    public $students = [];
    public $communications = [];

    public function mount()
    {
        $this->grades = EducationalStage::all();
        $this->classes = collect();
        $this->sections = collect();
        $this->students = collect();
        $this->loadCommunications();
        $this->filterStudents();
    }

    public function updatedSelectedGrade($value)
    {
        Log::info('');
        Log::info('Selected Grade updated to: ' . $value);
        $this->classes = $value ? MyClass::where('class_type_id', $value)->get() : collect();
        $this->selectedClass = '';
        $this->selectedSection = '';
        $this->sections = collect();
        $this->filterStudents();
    }

    public function updatedSelectedClass($value)
    {
        $this->sections = $value ? Section::where('my_class_id', $value)->get() : collect();
        $this->selectedSection = '';
        $this->filterStudents();
    }

    public function updatedSelectedSection($value)
    {
        $this->selectedSection = $value;
        $this->filterStudents();
    }

    public function filterStudents()
    {
        $query = StudentRecord::with(['user', 'my_class', 'section']);

        if ($this->selectedGrade) {
            $query->whereHas('my_class', function ($q) {
                $q->where('class_type_id', $this->selectedGrade);
            });
        }

        if ($this->selectedClass) {
            $query->where('my_class_id', $this->selectedClass);
        }

        if ($this->selectedSection) {
            $query->where('section_id', $this->selectedSection);
        }

        $this->students = $query->get();
    }

    public function sendEmail()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'selectedStudents' => 'required|array|min:1',
        ]);

        $recipients = [];
        $studentEmails = [];

        foreach ($this->selectedStudents as $studentId) {
            $student = StudentRecord::with('user')->find($studentId);
            if ($student && $student->user && $student->user->email) {
                $studentEmails[] = $student->user->email;
                $recipients[] = [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                ];
            }
        }

        if (empty($studentEmails)) {
            session()->flash('error', 'No valid email addresses found for selected students.');
            return;
        }

        try {
            // Log the communication
            $communication = Communication::create([
                'type' => 'email',
                'subject' => $this->subject,
                'message' => $this->message,
                'recipients' => $recipients,
                'sender_id' => Auth::id(),
                'status' => 'pending',
            ]);

            // Send the email
            Mail::to($studentEmails)->send(new NotificationEmail($this->subject, $this->message, $recipients));

            // Update status
            $communication->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            session()->flash('success', 'Email sent successfully to ' . count($studentEmails) . ' students.');
            $this->resetForm();
            $this->loadCommunications();

        } catch (\Exception $e) {
            $communication->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            session()->flash('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->subject = '';
        $this->message = '';
        $this->selectedStudents = [];
    }

    public function loadCommunications()
    {
        $this->communications = Communication::where('type', 'email')
            ->with('sender')
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.communication.email-communication');
    }
}
