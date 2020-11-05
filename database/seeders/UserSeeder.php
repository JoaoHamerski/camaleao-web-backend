<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
        	'email' => 'gerencia@email.com',
        	'role_id' => 3
        ]);

        User::factory()->create([
        	'email' => 'atendimento@email.com',
        	'role_id' => 2
        ]);	

        User::factory()->create([
            'email' => 'atendimento2@email.com',
            'role_id' => 2
        ]);

        User::factory()->create([
        	'email' => 'design@email.com',
        	'role_id' => 1
        ]);
    }
}
