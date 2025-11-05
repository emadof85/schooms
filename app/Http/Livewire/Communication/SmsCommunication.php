<?php

namespace App\Http\Livewire\Communication;

use App\Models\EducationalStage;
use App\Models\Communication;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\StudentRecord;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SmsCommunication extends Component
{
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
    public $smsConfigured = false;
    public $smsBalance = null;

    public function mount()
    {
        $this->grades = EducationalStage::all();
        $this->classes = collect();
        $this->sections = collect();
        $this->students = collect();
        $this->loadCommunications();
        $this->checkSmsConfiguration();
        $this->filterStudents();
    }

    public function checkSmsConfiguration()
    {
        $smsService = new SmsService();
        $this->smsConfigured = $smsService->isConfigured();
        $this->smsBalance = $smsService->getBalance();
    }

    public function updatedSelectedGrade()
    {
        $this->classes = $this->selectedGrade ? MyClass::where('class_type_id', $this->selectedGrade)->get() : collect();
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

    public function sendSms()
    {
        if (!$this->smsConfigured) {
            session()->flash('error', 'SMS service is not properly configured. Please check your SMS settings.');
            return;
        }

        $this->validate([
            'message' => 'required|string|max:160',
            'selectedStudents' => 'required|array|min:1',
        ]);

        $recipients = [];
        $phoneNumbers = [];

        foreach ($this->selectedStudents as $studentId) {
            $student = StudentRecord::with('user')->find($studentId);
            if ($student && $student->user && $student->user->phone) {
                $phoneNumbers[] = $student->user->phone;
                $recipients[] = [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'phone' => $student->user->phone,
                ];
            }
        }

        if (empty($phoneNumbers)) {
            session()->flash('error', 'No valid phone numbers found for selected students.');
            return;
        }

        try {
            // Log the communication
            $communication = Communication::create([
                'type' => 'sms',
                'message' => $this->message,
                'recipients' => $recipients,
                'sender_id' => Auth::id(),
                'status' => 'pending',
            ]);

            // Send the SMS
            $smsService = new SmsService();
            $result = $smsService->sendSms($phoneNumbers, $this->message);

            if ($result['success']) {
                $communication->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                session()->flash('success', $result['message']);
                $this->resetForm();
                $this->loadCommunications();
                $this->checkSmsConfiguration(); // Refresh balance
            } else {
                $communication->update([
                    'status' => 'failed',
                    'error_message' => implode(', ', $result['errors']),
                ]);

                session()->flash('error', 'Failed to send SMS: ' . $result['message']);
            }

        } catch (\Exception $e) {
            $communication->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            session()->flash('error', 'Failed to send SMS: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->message = '';
        $this->selectedStudents = [];
    }

    public function loadCommunications()
    {
        $this->communications = Communication::where('type', 'sms')
            ->with('sender')
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.communication.sms-communication');
    }
}
