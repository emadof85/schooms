<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\User;
use App\Models\Nationality;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\Dorm;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Ensure at least one nationality exists
        $nationality = Nationality::first();
        if (! $nationality) {
            $nationality = Nationality::create([
                'name' => [
                    'en' => 'Unknown',
                    'ar' => 'غير معروف',
                    'fr' => 'Inconnu',
                    'ru' => 'Неизвестно',
                ]
            ]);
        }

        $classIds = MyClass::pluck('id')->toArray();
        $sectionIds = Section::pluck('id')->toArray();
        $dormIds = Dorm::pluck('id')->toArray();

        // Create 20 dummy student users and student records
        for ($i = 0; $i < 20; $i++) {
            $name = $faker->name;
            $email = $faker->unique()->safeEmail;

            $user = User::create([
                'name' => $name,
                'username' => $faker->unique()->userName,
                'email' => $email,
                'phone' => $faker->phoneNumber,
                'phone2' => null,
                'dob' => $faker->date('Y-m-d', '-16 years'),
                'gender' => $faker->randomElement(['male', 'female']),
                'photo' => 'user.png',
                'address' => $faker->address,
                'bg_id' => null,
                'password' => Hash::make('password'),
                'nal_id' => $nationality->id,
                'state_id' => null,
                'lga_id' => null,
                'code' => Str::upper(Str::random(8)),
                'user_type' => 'student',
                'email_verified_at' => now(),
            ]);

            $isWithdrawn = $faker->boolean(5); // 5% chance withdrawn
            \App\Models\StudentRecord::create([
                'session' => '2024/2025',
                'user_id' => $user->id,
                'my_class_id' => $classIds ? $faker->randomElement($classIds) : null,
                'section_id' => $sectionIds ? $faker->randomElement($sectionIds) : null,
                'my_parent_id' => null,
                'dorm_id' => $dormIds ? $faker->randomElement($dormIds) : null,
                'dorm_room_no' => $faker->randomNumber(3),
                // Use user id to ensure uniqueness of admission numbers
                'adm_no' => 'ADM'.str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'year_admitted' => 2020,
                'wd' => $isWithdrawn ? 1 : 0,
                'wd_date' => $isWithdrawn ? $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d') : null,
                'grad' => 0,
                'grad_date' => null,
                'house' => $faker->randomElement(['Red', 'Blue', 'Green', 'Yellow']),
                'age' => $faker->numberBetween(10, 18),
            ]);
        }
    }
}
