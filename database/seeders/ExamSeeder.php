<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('exams')->delete();

        $data = [
            [
                'name' => 'First Term Examination',
                'term' => 1,
                'year' => '2023',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('exams')->insert($data);
    }
}