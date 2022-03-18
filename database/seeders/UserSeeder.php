<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ROLES = Config::get('app.roles');

        User::factory()->createMany([
            [
                'email' => Config::get('database.seeder.user_email'),
                'password' => Hash::make(Config::get('database.seeder.user_password')),
                'role_id' => $ROLES['GERENCIA'],
            ],
            [
                'email' => 'gerencia@email.com',
                'role_id' => $ROLES['GERENCIA']
            ],
            [
                'email' => 'atendimento@email.com',
                'role_id' => $ROLES['ATENDIMENTO']
            ],
            [
                'email' => 'design@email.com',
                'role_id' => $ROLES['DESIGN']
            ],
            [
                'email' => 'costura@email.com',
                'role_id' => $ROLES['COSTURA']
            ],
            [
                'email' => 'estampa@email.com',
                'role_id' => $ROLES['ESTAMPA']
            ]
        ]);
    }
}
