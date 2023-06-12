<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FormOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SectorsSeeder::class,
            StatusSeeder::class,
            ModelSeeder::class,
            MaterialSeeder::class,
            SleeveTypeSeeder::class,
            NeckTypeSeeder::class,
            GarmentSizeSeeder::class,
            GarmentMatchSeeder::class
        ]);
    }
}
