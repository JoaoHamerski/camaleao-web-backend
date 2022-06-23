<?php

namespace Database\Seeders;

use App\Models\AppConfig;
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

        AppConfig::set(
            'app',
            'product_types_expense',
            ProductType::inRandomOrder()->first()->id
        );

        AppConfig::set(
            'app',
            'employee_expense',
            ProductType::inRandomOrder()->first()->id
        );
    }
}
