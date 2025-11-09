<?php

namespace App\Http\Controllers\SupportTeam;

use App\Mail\NotificationEmail;
use App\Models\EducationalStage;
use App\Models\Communication;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\StudentRecord;
use App\Services\SmsService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CommunicationController extends Controller
{
    protected $allStudents;
    protected $allParents;

    public function __construct()
    {
        $this->middleware('teamSA');
        $this->loadAllRecipients();
    }

    protected function loadAllRecipients()
    {
        // Load all students with their relationships
        $this->allStudents = StudentRecord::with(['user', 'my_class.section', 'my_class.educational_stage'])
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'adm_no' => $student->adm_no,
                    'phone' => $student->user->phone,
                    'email' => $student->user->email,
                    'grade_id' => $student->my_class->class_type_id,
                    'class_id' => $student->my_class_id,
                    'section_id' => $student->section_id,
                    'grade_name' => $student->my_class->educational_stage->name,
                    'class_name' => $student->my_class->name,
                    'section_name' => $student->section->name,
                ];
            });

        // Load all parents with their relationships
        $this->allParents = StudentRecord::with(['my_parent', 'my_class.section', 'my_class.educational_stage'])
            ->whereNotNull('my_parent_id')
            ->whereHas('my_parent')
            ->get()
            ->filter(function ($student) {
                return $student->my_parent;
            })
            ->unique('my_parent_id')
            ->map(function ($student) {
                return [
                    'id' => $student->my_parent->id,
                    'name' => $student->my_parent->name,
                    'phone' => $student->my_parent->phone,
                    'email' => $student->my_parent->email,
                    'student_id' => $student->id,
                    'student_name' => $student->user->name,
                    'grade_id' => $student->my_class->class_type_id,
                    'class_id' => $student->my_class_id,
                    'section_id' => $student->section_id,
                    'grade_name' => $student->my_class->educational_stage->name,
                    'class_name' => $student->my_class->name,
                    'section_name' => $student->section->name,
                ];
            });
    }

    public function sms()
    {
        $data['grades'] = EducationalStage::all();
        $data['classes'] = collect();
        $data['sections'] = collect();
        $data['students'] = collect();
        $data['parents'] = collect();
        $data['communications'] = Communication::where('type', 'sms')
            ->with('sender')
            ->latest()
            ->take(10)
            ->get();

        $smsService = new SmsService();
        $data['smsConfigured'] = $smsService->isConfigured();
        $data['smsBalance'] = $smsService->getBalance();

        return view('pages.support_team.communication.sms', $data);
    }

    public function email()
    {
        $data['grades'] = EducationalStage::all();
        $data['classes'] = collect();
        $data['sections'] = collect();
        $data['students'] = collect();
        $data['parents'] = collect();
        $data['communications'] = Communication::where('type', 'email')
            ->with('sender')
            ->latest()
            ->take(10)
            ->get();

        return view('pages.support_team.communication.email', $data);
    }

    public function filterRecipients(Request $request)
    {
        $recipientType = $request->recipient_type;
        $selectedGrade = $request->selected_grade;
        $selectedClass = $request->selected_class;
        $selectedSection = $request->selected_section;

        $students = collect();
        $parents = collect();

        if ($recipientType === 'students' || $recipientType === 'both') {
            $students = $this->allStudents->filter(function ($student) use ($selectedGrade, $selectedClass, $selectedSection) {
                if ($selectedGrade && $student['grade_id'] != $selectedGrade) {
                    return false;
                }
                if ($selectedClass && $student['class_id'] != $selectedClass) {
                    return false;
                }
                if ($selectedSection && $student['section_id'] != $selectedSection) {
                    return false;
                }
                return true;
            });
        }

        if ($recipientType === 'parents' || $recipientType === 'both') {
            $parents = $this->allParents->filter(function ($parent) use ($selectedGrade, $selectedClass, $selectedSection) {
                if ($selectedGrade && $parent['grade_id'] != $selectedGrade) {
                    return false;
                }
                if ($selectedClass && $parent['class_id'] != $selectedClass) {
                    return false;
                }
                if ($selectedSection && $parent['section_id'] != $selectedSection) {
                    return false;
                }
                return true;
            })->map(function ($parent) {
                return [
                    'id' => $parent['student_id'], // Use student_id for parent selection
                    'name' => $parent['name'],
                    'phone' => $parent['phone'],
                    'email' => $parent['email'],
                    'student_name' => $parent['student_name'],
                    'grade_name' => $parent['grade_name'],
                    'class_name' => $parent['class_name'],
                    'section_name' => $parent['section_name'],
                ];
            });
        }

        return response()->json([
            'students' => $students->values(),
            'parents' => $parents->values()
        ]);
    }

    public function sendSms(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:160',
            'recipient_type' => 'required|in:students,parents,both',
            'selected_students' => 'required_if:recipient_type,students,both|array',
            'selected_parents' => 'required_if:recipient_type,parents,both|array',
        ]);

        $smsService = new SmsService();
        if (!$smsService->isConfigured()) {
            return back()->with('error', 'SMS service is not properly configured. Please check your SMS settings.');
        }

        $recipients = [];
        $phoneNumbers = [];
        $recipientType = $request->recipient_type;

        // Collect student recipients
        if ($recipientType === 'students' || $recipientType === 'both') {
            foreach ($request->selected_students as $studentId) {
                $student = StudentRecord::with('user')->find($studentId);
                if ($student && $student->user && $student->user->phone) {
                    $phoneNumbers[] = $student->user->phone;
                    $recipients[] = [
                        'id' => $student->id,
                        'name' => $student->user->name,
                        'phone' => $student->user->phone,
                        'type' => 'student',
                    ];
                }
            }
        }

        // Collect parent recipients
        if ($recipientType === 'parents' || $recipientType === 'both') {
            foreach ($request->selected_parents as $parentId) {
                $student = StudentRecord::with('my_parent.user')->find($parentId);
                if ($student && $student->my_parent && $student->my_parent->user && $student->my_parent->user->phone) {
                    $phoneNumbers[] = $student->my_parent->user->phone;
                    $recipients[] = [
                        'id' => $student->my_parent->id,
                        'name' => $student->my_parent->user->name,
                        'phone' => $student->my_parent->user->phone,
                        'type' => 'parent',
                        'student_name' => $student->user->name,
                    ];
                }
            }
        }

        if (empty($phoneNumbers)) {
            return back()->with('error', 'No valid phone numbers found for selected recipients.');
        }

        try {
            // Log the communication
            $communication = Communication::create([
                'type' => 'sms',
                'message' => $request->message,
                'recipients' => $recipients,
                'sender_id' => Auth::id(),
                'status' => 'pending',
            ]);

            // Send the SMS
            $result = $smsService->sendSms($phoneNumbers, $request->message);

            if ($result['success']) {
                $communication->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $recipientCount = count($phoneNumbers);
                $recipientTypeText = $recipientType === 'both' ? 'recipients' : $recipientType;
                return back()->with('success', 'SMS sent successfully to ' . $recipientCount . ' ' . $recipientTypeText . '.');
            } else {
                $communication->update([
                    'status' => 'failed',
                    'error_message' => implode(', ', $result['errors']),
                ]);

                return back()->with('error', 'Failed to send SMS: ' . $result['message']);
            }

        } catch (\Exception $e) {
            $communication->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send SMS: ' . $e->getMessage());
        }
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:students,parents,both',
            'selected_students' => 'required_if:recipient_type,students,both|array',
            'selected_parents' => 'required_if:recipient_type,parents,both|array',
        ]);

        $recipients = [];
        $emails = [];
        $recipientType = $request->recipient_type;

        // Collect student recipients
        if ($recipientType === 'students' || $recipientType === 'both') {
            foreach ($request->selected_students as $studentId) {
                $student = StudentRecord::with('user')->find($studentId);
                if ($student && $student->user && $student->user->email) {
                    $emails[] = $student->user->email;
                    $recipients[] = [
                        'id' => $student->id,
                        'name' => $student->user->name,
                        'email' => $student->user->email,
                        'type' => 'student',
                    ];
                }
            }
        }

        // Collect parent recipients
        if ($recipientType === 'parents' || $recipientType === 'both') {
            foreach ($request->selected_parents as $parentId) {
                $student = StudentRecord::with('my_parent.user')->find($parentId);
                if ($student && $student->my_parent && $student->my_parent->user && $student->my_parent->user->email) {
                    $emails[] = $student->my_parent->user->email;
                    $recipients[] = [
                        'id' => $student->my_parent->id,
                        'name' => $student->my_parent->user->name,
                        'email' => $student->my_parent->user->email,
                        'type' => 'parent',
                        'student_name' => $student->user->name,
                    ];
                }
            }
        }

        if (empty($emails)) {
            return back()->with('error', 'No valid email addresses found for selected recipients.');
        }

        try {
            // Log the communication
            $communication = Communication::create([
                'type' => 'email',
                'subject' => $request->subject,
                'message' => $request->message,
                'recipients' => $recipients,
                'sender_id' => Auth::id(),
                'status' => 'pending',
            ]);
            /*
            \Log::info('Sending email to: ' . print_r($recipients,true));
            \Log::info('Email subject: ' . print_r($request->subject,true));
            \Log::info('Email message: ' . print_r($request->message,true));*/
            // Send the email
            Mail::to($emails)->send(new NotificationEmail($request->subject, $request->message, $recipients));

            // Log the email sending
            \Log::info('Sending email to: ', $recipients);
            \Log::info('Email subject: ' . $request->subject);
            \Log::info('Email message: ' . $request->message);

            // Update status
            $communication->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $recipientCount = count($emails);
            $recipientTypeText = $recipientType === 'both' ? 'recipients' : $recipientType;
            return back()->with('success', 'Email sent successfully to ' . $recipientCount . ' ' . $recipientTypeText . '.');

        } catch (\Exception $e) {
            $communication->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function getClasses(Request $request)
    {
        $gradeId = $request->grade_id;
        $classes = $gradeId ? MyClass::where('class_type_id', $gradeId)->get() : collect();
        return response()->json($classes);
    }

    public function getSections(Request $request)
    {
        $classId = $request->class_id;
        $sections = $classId ? Section::where('my_class_id', $classId)->get() : collect();
        return response()->json($sections);
    }

    public function searchStudents(Request $request)
    {
        $query = $request->input('query');

        if (!is_string($query) || strlen($query) < 2) {
            return response()->json(['students' => [], 'parents' => []]);
        }

        // Search students
        $students = $this->allStudents->filter(function ($student) use ($query) {
            return stripos($student['name'], $query) !== false ||
                   stripos($student['adm_no'], $query) !== false ||
                   stripos($student['phone'] ?? '', $query) !== false ||
                   stripos($student['email'] ?? '', $query) !== false;
        })->take(10);

        // Search parents
        $parents = $this->allParents->filter(function ($parent) use ($query) {
            return stripos($parent['name'], $query) !== false ||
                   stripos($parent['student_name'], $query) !== false ||
                   stripos($parent['phone'] ?? '', $query) !== false ||
                   stripos($parent['email'] ?? '', $query) !== false;
        })->take(10);

        return response()->json([
            'students' => $students->values(),
            'parents' => $parents->values()
        ]);
    }
}