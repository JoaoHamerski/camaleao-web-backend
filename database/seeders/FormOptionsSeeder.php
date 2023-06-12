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
            ModelSeeder::class,
            MaterialSeeder::class,
            SleeveTypeSeeder::class,
            NeckTypeSeeder::class,
            ClothSizeSeeder::class,
            ClothMatchSeeder::class
        ]);
    }
}
