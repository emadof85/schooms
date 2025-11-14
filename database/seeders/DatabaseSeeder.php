<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BloodGroupsTableSeeder::class);
        $this->call(GradesTableSeeder::class);
        $this->call(DormsTableSeeder::class);
        $this->call(ClassTypesTableSeeder::class);
        $this->call(UserTypesTableSeeder::class);
        $this->call(MyClassesTableSeeder::class);
        $this->call(NationalitiesTableSeeder::class);
        // $this->call(StatesTableSeeder::class);
        // $this->call(LgasTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(EmployeesTableSeeder::class);
        $this->call(SubjectsTableSeeder::class);
        $this->call(SectionsTableSeeder::class);
        $this->call(StudentRecordsTableSeeder::class);
        $this->call(ExamSeeder::class);
        $this->call(MarksSeeder::class);
        $this->call(ExamRecordsSeeder::class);
        $this->call(SkillsTableSeeder::class);
        $this->call(DummyDataSeeder::class);

        // Bus Management Seeders
        $this->call(BusesTableSeeder::class);
        $this->call(BusRoutesTableSeeder::class);
        $this->call(BusStopsTableSeeder::class);
        $this->call(BusDriversTableSeeder::class);
        $this->call(BusAssignmentsTableSeeder::class);
        $this->call(StudentBusAssignmentsTableSeeder::class);
    }
}
