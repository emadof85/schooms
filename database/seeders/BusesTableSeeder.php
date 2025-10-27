<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bus;

class BusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buses = [
            [
                'bus_number' => 'BUS001',
                'plate_number' => 'ABC-123',
                'model' => 'Mercedes-Benz Sprinter',
                'capacity' => 20,
                'status' => 'active',
                'description' => 'School bus for primary students',
            ],
            [
                'bus_number' => 'BUS002',
                'plate_number' => 'DEF-456',
                'model' => 'Volkswagen Crafter',
                'capacity' => 25,
                'status' => 'active',
                'description' => 'School bus for secondary students',
            ],
            [
                'bus_number' => 'BUS003',
                'plate_number' => 'GHI-789',
                'model' => 'Ford Transit',
                'capacity' => 18,
                'status' => 'active',
                'description' => 'School bus for kindergarten students',
            ],
            [
                'bus_number' => 'BUS004',
                'plate_number' => 'JKL-012',
                'model' => 'Iveco Daily',
                'capacity' => 22,
                'status' => 'maintenance',
                'description' => 'School bus under maintenance',
            ],
            [
                'bus_number' => 'BUS005',
                'plate_number' => 'MNO-345',
                'model' => 'Mercedes-Benz Vito',
                'capacity' => 15,
                'status' => 'active',
                'description' => 'Small school bus for special needs students',
            ],
        ];

        foreach ($buses as $bus) {
            Bus::create($bus);
        }
    }
}