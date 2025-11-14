<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Qs;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('users')->delete();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->createNewUsers();
        $this->createManyUsers( 3);
    }

    protected function createNewUsers()
    {
        $password = Hash::make('schooms123'); // Default user password

        $users = [

            ['name' => 'my Techno',
                'email' => 'info@maitechno.com',
                'username' => 'maitechno',
                'password' => $password,
                'user_type' => 'super_admin',
                'photo' => 'global_assets/images/user.png',
                'code' => strtoupper(Str::random(10)),
                'remember_token' => Str::random(10),
            ],

            ['name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => $password,
            'user_type' => 'admin',
            'username' => 'admin',
            'photo' => 'global_assets/images/user.png',
            'code' => strtoupper(Str::random(10)),
            'remember_token' => Str::random(10),
            ],

            ['name' => 'Teacher Chike',
                'email' => 'teacher@teacher.com',
                'user_type' => 'teacher',
                'username' => 'teacher',
                'password' => $password,
                'photo' => 'global_assets/images/user.png',
                'code' => strtoupper(Str::random(10)),
                'remember_token' => Str::random(10),
            ],

            ['name' => 'Parent Kaba',
                'email' => 'parent@parent.com',
                'user_type' => 'parent',
                'username' => 'parent',
                'password' => $password,
                'photo' => 'global_assets/images/user.png',
                'code' => strtoupper(Str::random(10)),
                'remember_token' => Str::random(10),
            ],

            ['name' => 'Accountant Jeff',
                'email' => 'accountant@accountant.com',
                'user_type' => 'accountant',
                'username' => 'accountant',
                'password' => $password,
                'photo' => 'global_assets/images/user.png',
                'code' => strtoupper(Str::random(10)),
                'remember_token' => Str::random(10),
            ],
        ];

        foreach ($users as $userData) {
            \App\User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }

    protected function createManyUsers(int $count)
    {
        $user_type = Qs::getAllUserTypes(['super_admin', 'librarian', 'student']);

        for($i = 1; $i <= $count; $i++){

            foreach ($user_type as $k => $ut){

                $userData = ['name' => ucfirst($user_type[$k]).' '.$i,
                    'email' => $user_type[$k].$i.'@'.$user_type[$k].'.com',
                    'user_type' => $user_type[$k],
                    'username' => $user_type[$k].$i,
                    'password' => Hash::make($user_type[$k]),
                    'photo' => 'global_assets/images/user.png',
                    'code' => strtoupper(Str::random(10)),
                    'remember_token' => Str::random(10),
                ];

                \App\User::updateOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );

            }

        }
    }
}
