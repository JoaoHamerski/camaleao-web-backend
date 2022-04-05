<?php

namespace Database\Seeders;

use App\Models\City;

class CitySeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $CITIES_QUANTITY = 20;

        City::factory()->count($CITIES_QUANTITY)->create();
    }
}
