<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentBusAssignment;
use App\Models\StudentRecord;
use App\Models\BusAssignment;
use App\Models\BusStop;

class StudentBusAssignmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get some students
        $students = StudentRecord::take(10)->get();

        // Get bus assignments
        $busAssignments = BusAssignment::all();

        // Get bus stops
        $busStops = BusStop::all();

        if ($students->isNotEmpty() && $busAssignments->isNotEmpty() && $busStops->isNotEmpty()) {
            foreach ($students as $index => $student) {
                $busAssignment = $busAssignments[$index % $busAssignments->count()];
                $busStop = $busStops->where('bus_route_id', $busAssignment->bus_route_id)->first();

                if ($busStop) {
                    StudentBusAssignment::create([
                        'student_record_id' => $student->id,
                        'bus_assignment_id' => $busAssignment->id,
                        'bus_stop_id' => $busStop->id,
                        'fee' => rand(50, 200), // Random fee between 50 and 200
                        'status' => 'active',
                    ]);
                }
            }
        }
    }
}