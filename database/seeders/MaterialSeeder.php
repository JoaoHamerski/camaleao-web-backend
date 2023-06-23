<?php

namespace Database\Seeders;

use App\Models\Material;

class MaterialSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $MATERIALS_QUANTITY = $this->faker->numberBetween(5, 10);

        Material::factory()
            ->count($MATERIALS_QUANTITY)
            ->create();
    }
}
