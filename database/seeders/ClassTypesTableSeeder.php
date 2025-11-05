<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('educational_stages')->delete();

        $data = [
            ['name' => 'Creche', 'code' => 'C'],
            ['name' => 'Pre Nursery', 'code' => 'PN'],
            ['name' => 'Nursery', 'code' => 'N'],
            ['name' => 'Primary', 'code' => 'P'],
            ['name' => 'Junior Secondary', 'code' => 'J'],
            ['name' => 'Senior Secondary', 'code' => 'S'],
        ];

        DB::table('educational_stages')->insert($data);

    }
}
