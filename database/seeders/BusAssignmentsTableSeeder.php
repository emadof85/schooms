<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusAssignment;
use App\Models\BusDriver;

class BusAssignmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assignments = [
            [
                'bus_id' => 1,
                'bus_route_id' => 1,
                'assignment_date' => now()->format('Y-m-d'),
                'active' => true,
            ],
            [
                'bus_id' => 2,
                'bus_route_id' => 2,
                'assignment_date' => now()->format('Y-m-d'),
                'active' => true,
            ],
            [
                'bus_id' => 3,
                'bus_route_id' => 3,
                'assignment_date' => now()->format('Y-m-d'),
                'active' => true,
            ],
            [
                'bus_id' => 5,
                'bus_route_id' => 4,
                'assignment_date' => now()->format('Y-m-d'),
                'active' => true,
            ],
        ];

        foreach ($assignments as $assignment) {
            BusAssignment::create($assignment);
        }
    }
}