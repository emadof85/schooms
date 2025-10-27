<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;
use App\Models\Employee;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get some users that could be employees (excluding students and parents)
        $users = User::whereNotIn('user_type', ['student', 'parent'])->get();

        foreach ($users as $user) {
            // Create employee record for each non-student/parent user
            Employee::create([
                'user_id' => $user->id,
                'license_number' => $user->user_type === 'teacher' ? 'DRV' . str_pad($user->id, 6, '0', STR_PAD_LEFT) : null,
                'license_expiry' => $user->user_type === 'teacher' ? now()->addYears(2)->format('Y-m-d') : null,
                'type' => $user->user_type === 'teacher' ? 'driver' : 'staff',
                'active' => true,
            ]);
        }
    }
}