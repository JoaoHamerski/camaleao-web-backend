<?php

namespace Database\Seeders;

use App\Models\Model;

class ModelSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $MODELS_QUANTITY = $this->faker->numberBetween(5, 10);

        Model::factory()
            ->count($MODELS_QUANTITY)
            ->create();
    }
}
