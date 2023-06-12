<?php

namespace Database\Seeders;

use App\Models\NeckType;

class NeckTypeSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $NECK_TYPE_QUANTITY = $this->faker->numberBetween(5, 10);

        NeckType::factory()
            ->count($NECK_TYPE_QUANTITY)
            ->create();
    }
}
