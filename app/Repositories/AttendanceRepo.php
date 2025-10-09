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
}
