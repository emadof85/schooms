<?php

namespace App\Repositories;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceRepo
{
    public function forClassAndDate($class_id, $date)
    {
        // Return attendance keyed by student_record_id
        $d = Carbon::parse($date)->toDateString();
        return Attendance::where('class_id', $class_id)->where('date', $d)->get()->keyBy('student_record_id');
    }

    public function upsertBulk($class_id, $date, array $items, $marked_by)
    {
        $d = Carbon::parse($date)->toDateString();
        $saved = 0;
        foreach ($items as $it) {
            $data = [
                'student_record_id' => $it['student_record_id'],
                'class_id' => $class_id,
                'date' => $d,
                'status' => $it['status'] ?? 'present',
                'note' => $it['note'] ?? null,
                'marked_by' => $marked_by,
                'marked_at' => now(),
            ];

            $attendance = Attendance::updateOrCreate(
                ['student_record_id' => $it['student_record_id'], 'date' => $d],
                $data
            );

            if ($attendance) $saved++;
        }
        return $saved;
    }

    public function summaryForClassAndDate($class_id, $date)
    {
        $d = Carbon::parse($date)->toDateString();
        return Attendance::where('class_id', $class_id)
            ->where('date', $d)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function historyForStudent($student_id, $start_date, $end_date)
    {
        $start = Carbon::parse($start_date)->toDateString();
        $end = Carbon::parse($end_date)->toDateString();
        return Attendance::where('student_record_id', $student_id)
            ->whereBetween('date', [$start, $end])
            ->with(['student_record.user', 'marker'])
            ->orderBy('date')
            ->get();
    }
}
