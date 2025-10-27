<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusStop;

class BusStopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stops = [
            // Downtown Route stops
            [
                'bus_route_id' => 1,
                'stop_name' => 'Central Station',
                'address' => 'Central Station, Downtown',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'order' => 1,
                'active' => true,
            ],
            [
                'bus_route_id' => 1,
                'stop_name' => 'Main Street',
                'address' => 'Main Street, Downtown',
                'latitude' => 40.7138,
                'longitude' => -74.0070,
                'order' => 2,
                'active' => true,
            ],
            [
                'bus_route_id' => 1,
                'stop_name' => 'School Entrance',
                'address' => 'School Main Entrance',
                'latitude' => 40.7148,
                'longitude' => -74.0080,
                'order' => 3,
                'active' => true,
            ],

            // North District Route stops
            [
                'bus_route_id' => 2,
                'stop_name' => 'North Plaza',
                'address' => 'North Plaza Shopping Center',
                'latitude' => 40.7228,
                'longitude' => -74.0160,
                'order' => 1,
                'active' => true,
            ],
            [
                'bus_route_id' => 2,
                'stop_name' => 'Oak Avenue',
                'address' => 'Oak Avenue Residential Area',
                'latitude' => 40.7238,
                'longitude' => -74.0170,
                'order' => 2,
                'active' => true,
            ],
            [
                'bus_route_id' => 2,
                'stop_name' => 'School Entrance',
                'address' => 'School Main Entrance',
                'latitude' => 40.7148,
                'longitude' => -74.0080,
                'order' => 3,
                'active' => true,
            ],

            // East Side Route stops
            [
                'bus_route_id' => 3,
                'stop_name' => 'East Mall',
                'address' => 'East Side Shopping Mall',
                'latitude' => 40.7128,
                'longitude' => -73.9960,
                'order' => 1,
                'active' => true,
            ],
            [
                'bus_route_id' => 3,
                'stop_name' => 'River Street',
                'address' => 'River Street Bridge',
                'latitude' => 40.7138,
                'longitude' => -73.9970,
                'order' => 2,
                'active' => true,
            ],
            [
                'bus_route_id' => 3,
                'stop_name' => 'School Entrance',
                'address' => 'School Main Entrance',
                'latitude' => 40.7148,
                'longitude' => -74.0080,
                'order' => 3,
                'active' => true,
            ],

            // West End Route stops
            [
                'bus_route_id' => 4,
                'stop_name' => 'West Park',
                'address' => 'West End Park',
                'latitude' => 40.7128,
                'longitude' => -74.0160,
                'order' => 1,
                'active' => true,
            ],
            [
                'bus_route_id' => 4,
                'stop_name' => 'Hill Road',
                'address' => 'Hill Road Junction',
                'latitude' => 40.7138,
                'longitude' => -74.0170,
                'order' => 2,
                'active' => true,
            ],
            [
                'bus_route_id' => 4,
                'stop_name' => 'School Entrance',
                'address' => 'School Main Entrance',
                'latitude' => 40.7148,
                'longitude' => -74.0080,
                'order' => 3,
                'active' => true,
            ],

            // South Hills Route stops
            [
                'bus_route_id' => 5,
                'stop_name' => 'South Valley',
                'address' => 'South Valley Community Center',
                'latitude' => 40.7028,
                'longitude' => -74.0060,
                'order' => 1,
                'active' => true,
            ],
            [
                'bus_route_id' => 5,
                'stop_name' => 'Mountain View',
                'address' => 'Mountain View Apartments',
                'latitude' => 40.7038,
                'longitude' => -74.0070,
                'order' => 2,
                'active' => true,
            ],
            [
                'bus_route_id' => 5,
                'stop_name' => 'School Entrance',
                'address' => 'School Main Entrance',
                'latitude' => 40.7148,
                'longitude' => -74.0080,
                'order' => 3,
                'active' => true,
            ],
        ];

        foreach ($stops as $stop) {
            BusStop::create($stop);
        }
    }
}