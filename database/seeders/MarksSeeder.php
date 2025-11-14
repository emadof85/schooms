<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\MyClass;
use App\Models\StudentRecord;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('marks')->delete();

        $this->createMarksForClass();
    }

    protected function createMarksForClass()
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

        // Get subjects for this class
        $subjects = Subject::where('my_class_id', $class->id)->get();

        // Get grades
        $grades = Grade::all()->keyBy(function ($grade) {
            return $grade->id;
        });

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                // Generate random scores
                $t1 = rand(10, 20);
                $t2 = rand(10, 20);
                $t3 = rand(10, 20);
                $t4 = rand(10, 20);
                $tca = $t1 + $t2 + $t3 + $t4;
                $exm = rand(40, 80);
                $total = $tca + $exm;

                // Determine grade based on total
                $grade = $this->getGrade($total, $grades);

                $data = [
                    'student_id' => $student->user_id,
                    'subject_id' => $subject->id,
                    'my_class_id' => $class->id,
                    'section_id' => $student->section_id,
                    'exam_id' => $exam->id,
                    't1' => $t1,
                    't2' => $t2,
                    't3' => $t3,
                    't4' => $t4,
                    'tca' => $tca,
                    'exm' => $exm,
                    'tex1' => null,
                    'tex2' => null,
                    'tex3' => null,
                    'sub_pos' => null,
                    'cum' => $total,
                    'cum_ave' => number_format($total / 100, 2),
                    'grade_id' => $grade ? $grade->id : null,
                    'year' => '2023',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                DB::table('marks')->insert($data);
            }
        }
    }

    protected function getGrade($score, $grades)
    {
        foreach ($grades as $grade) {
            if ($score >= $grade->mark_from && $score <= $grade->mark_to) {
                return $grade;
            }
        }
        return null;
    }
}