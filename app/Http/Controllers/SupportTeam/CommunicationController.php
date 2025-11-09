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
    public function __construct()
    {
        $this->middleware('teamSA');
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
            $query = StudentRecord::with(['user', 'my_class', 'section']);

            if ($selectedGrade) {
                $query->whereHas('my_class', function ($q) use ($selectedGrade) {
                    $q->where('class_type_id', $selectedGrade);
                });
            }

            if ($selectedClass) {
                $query->where('my_class_id', $selectedClass);
            }

            if ($selectedSection) {
                $query->where('section_id', $selectedSection);
            }

            $students = $query->get();
        }

        if ($recipientType === 'parents' || $recipientType === 'both') {
            $query = StudentRecord::with(['my_parent.user', 'my_class', 'section']);

            if ($selectedGrade) {
                $query->whereHas('my_class', function ($q) use ($selectedGrade) {
                    $q->where('class_type_id', $selectedGrade);
                });
            }

            if ($selectedClass) {
                $query->where('my_class_id', $selectedClass);
            }

            if ($selectedSection) {
                $query->where('section_id', $selectedSection);
            }

            $parents = $query->whereNotNull('my_parent_id')
                ->whereHas('my_parent.user')
                ->get()
                ->filter(function ($student) {
                    return $student->my_parent && $student->my_parent->user;
                })
                ->unique('my_parent_id');
        }

        return response()->json([
            'students' => $students,
            'parents' => $parents
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

            // Send the email
            Mail::to($emails)->send(new NotificationEmail($request->subject, $request->message, $recipients));

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
}