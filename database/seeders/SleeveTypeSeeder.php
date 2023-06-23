<?php

namespace Database\Seeders;

use App\Models\SleeveType;

class SleeveTypeSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $SLEEVE_TYPE_QUANTITY = $this->faker->numberBetween(5, 10);

        SleeveType::factory()
            ->count($SLEEVE_TYPE_QUANTITY)
            ->create();
    }
}
