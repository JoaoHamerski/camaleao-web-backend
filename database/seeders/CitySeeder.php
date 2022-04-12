<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;

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

        City::factory()
            ->count($CITIES_QUANTITY)
            ->create()
            ->each(function ($city) {
                $city->update([
                    'state_id' => State::inRandomOrder()->first()->id
                ]);
            });
    }
}
