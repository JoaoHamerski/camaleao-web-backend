<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FormOptionsMatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ModelSeeder::class,
            MaterialSeeder::class,
            SleeveTypeSeeder::class,
            NeckTypeSeeder::class,
            GarmentSizeSeeder::class
        ]);
    }
}
