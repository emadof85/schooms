<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusDriver;
use App\Models\Employee;

class BusDriversTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get employees that are drivers
        $drivers = Employee::where('type', 'driver')->get();

        if ($drivers->isEmpty()) {
            // If no drivers exist, create some sample ones
            $drivers = Employee::where('type', 'staff')->take(3)->get();
        }

        $buses = [1, 2, 3, 4, 5]; // Bus IDs from BusesTableSeeder

        foreach ($drivers as $index => $driver) {
            if (isset($buses[$index])) {
                BusDriver::create([
                    'employee_id' => $driver->id,
                    'bus_id' => $buses[$index],
                    'assignment_date' => now()->format('Y-m-d'),
                    'active' => true,
                ]);
            }
        }
    }
}