<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AttendanceRepo;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    protected $repo;

    public function __construct(AttendanceRepo $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $class_id = $request->get('class_id');
        $date = $request->get('date', now()->toDateString());

        if (!$class_id) {
            return response()->json(['error' => 'class_id required'], 422);
        }

        $this->authorize('mark', $class_id);

        $att = $this->repo->forClassAndDate($class_id, $date);

        // get students in class
        $students = StudentRecord::where('class_id', $class_id)->with('student')->get();

        $data = $students->map(function ($sr) use ($att) {
            $a = $att->get($sr->id);
            return [
                'student_record_id' => $sr->id,
                'student' => $sr->student,
                'status' => $a ? $a->status : null,
                'note' => $a ? $a->note : null,
                'marked_by' => $a ? $a->marked_by : null,
                'marked_at' => $a ? $a->marked_at : null,
            ];
        });

        return response()->json(['date' => $date, 'class_id' => $class_id, 'rows' => $data]);
    }

    public function showPage()
    {
        $classes = \App\Models\MyClass::all();
        return view('pages.support_team.attendance.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'class_id' => 'required|integer',
            'date' => 'required|date',
            'items' => 'required|array',
            'items.*.student_record_id' => 'required|integer',
            'items.*.status' => 'required|string',
            'items.*.note' => 'nullable|string',
        ]);

        $this->authorize('mark', $payload['class_id']);

        $saved = $this->repo->upsertBulk($payload['class_id'], $payload['date'], $payload['items'], Auth::id());

        return response()->json(['saved' => $saved]);
    }
}
