<?php

namespace Database\Seeders;

use App\Models\ProductType;

class ProductTypeSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductType::factory()
            ->count($this->faker->numberBetween(3, 8))
            ->create();
    }
}
