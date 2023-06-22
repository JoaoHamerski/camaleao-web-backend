<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BasicSeeder extends Seeder
{
    /**
     * Seed básico para o funcionamento de todas as features.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SectorSeeder::class,
            StatusSeeder::class,
            UserSeeder::class
        ]);
    }
}
