<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AttendanceRepo;
use App\Models\MyClass;
use App\Models\StudentRecord;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReportsController extends Controller
{
    protected $attendanceRepo;

    public function __construct(AttendanceRepo $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }

    public function index()
    {
        $classes = MyClass::all();
        $students = StudentRecord::with('user')->get();
        return view('pages.support_team.reports.index', compact('classes', 'students'));
    }

    public function dailySummary(Request $request)
    {
        $request->validate([
            'class_id' => 'required|integer',
            'date' => 'required|date',
        ]);

        $this->authorize('mark', \App\Models\Attendance::class); // Reuse attendance policy

        $class = MyClass::findOrFail($request->class_id);
        $summary = $this->attendanceRepo->summaryForClassAndDate($request->class_id, $request->date);
        $students = StudentRecord::where('my_class_id', $request->class_id)->with('user')->get();

        $attendances = Attendance::where('class_id', $request->class_id)
            ->where('date', Carbon::parse($request->date)->toDateString())
            ->get()
            ->keyBy('student_record_id');

        $studentList = $students->map(function ($student) use ($attendances) {
            $att = $attendances->get($student->id);
            return [
                'name' => $student->user->name ?? 'N/A',
                'status' => $att ? __('msg.' . $att->status) : __('msg.not_marked'),
                'note' => $att ? $att->note : '',
            ];
        });

        return response()->json([
            'date' => $request->date,
            'class' => $class->name,
            'summary' => $summary,
            'rows' => $studentList
        ]);
    }

    public function studentSheet(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $this->authorize('mark', \App\Models\Attendance::class);

        $student = StudentRecord::with('user')->findOrFail($request->student_id);
        $history = $this->attendanceRepo->historyForStudent($request->student_id, $request->start_date, $request->end_date);

        $historyData = $history->map(function ($att) {
            return [
                'date' => $att->date->format('Y-m-d'),
                'status' => __('msg.' . $att->status),
                'note' => $att->note ?? '',
                'marked_by' => $att->marker ? $att->marker->name : '',
            ];
        });

        return response()->json([
            'student' => $student->user->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'rows' => $historyData
        ]);
    }
}
