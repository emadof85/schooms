<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusRoute;

class BusRoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = [
            [
                'route_name' => 'Downtown Route',
                'start_location' => 'Downtown',
                'end_location' => 'School',
                'departure_time' => '07:00',
                'arrival_time' => '08:00',
                'distance_km' => 15.5,
                'active' => true,
            ],
            [
                'route_name' => 'North District Route',
                'start_location' => 'North District',
                'end_location' => 'School',
                'departure_time' => '07:15',
                'arrival_time' => '08:15',
                'distance_km' => 12.3,
                'active' => true,
            ],
            [
                'route_name' => 'East Side Route',
                'start_location' => 'East Side',
                'end_location' => 'School',
                'departure_time' => '07:30',
                'arrival_time' => '08:30',
                'distance_km' => 18.7,
                'active' => true,
            ],
            [
                'route_name' => 'West End Route',
                'start_location' => 'West End',
                'end_location' => 'School',
                'departure_time' => '07:45',
                'arrival_time' => '08:45',
                'distance_km' => 22.1,
                'active' => true,
            ],
            [
                'route_name' => 'South Hills Route',
                'start_location' => 'South Hills',
                'end_location' => 'School',
                'departure_time' => '08:00',
                'arrival_time' => '09:00',
                'distance_km' => 25.8,
                'active' => true,
            ],
        ];

        foreach ($routes as $route) {
            BusRoute::create($route);
        }
    }
}