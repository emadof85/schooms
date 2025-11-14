<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\MyClass;
use App\Models\StudentRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('exam_records')->delete();

        $this->createExamRecordsForClass();
    }

    protected function createExamRecordsForClass()
    {
        // Get SSS 1 class
        $class = MyClass::where('name', 'SSS 1')->first();
        if (!$class) {
            return;
        }

        // Get exam
        $exam = Exam::where('term', 1)->where('year', '2023')->first();
        if (!$exam) {
            return;
        }

        // Get students in this class
        $students = StudentRecord::where('my_class_id', $class->id)->get();

        $studentTotals = [];

        foreach ($students as $student) {
            // Calculate total from marks
            $marks = DB::table('marks')
                ->where('student_id', $student->user_id)
                ->where('exam_id', $exam->id)
                ->get();

            $total = $marks->sum('cum');
            $ave = $marks->count() > 0 ? $total / $marks->count() : 0;

            $studentTotals[] = [
                'student' => $student,
                'total' => $total,
                'ave' => $ave,
            ];
        }

        // Sort by total descending for position
        usort($studentTotals, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        $classAve = collect($studentTotals)->avg('ave');

        foreach ($studentTotals as $index => $data) {
            $student = $data['student'];
            $total = $data['total'];
            $ave = $data['ave'];
            $pos = $index + 1;

            $examRecord = [
                'exam_id' => $exam->id,
                'student_id' => $student->user_id,
                'my_class_id' => $class->id,
                'section_id' => $student->section_id,
                'total' => $total,
                'ave' => number_format($ave, 2),
                'class_ave' => number_format($classAve, 2),
                'pos' => $pos,
                'af' => null,
                'ps' => null,
                'p_comment' => 'Good performance',
                't_comment' => 'Keep it up',
                'year' => '2023',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('exam_records')->insert($examRecord);
        }
    }
}