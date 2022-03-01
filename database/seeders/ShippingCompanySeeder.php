<?php

namespace Database\Seeders;

use App\Models\ShippingCompany;
use Illuminate\Database\Seeder;

class ShippingCompanySeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $SHIPPING_COMPANIES_QUANTITY = $this->faker->numberBetween(4, 10);

        ShippingCompany::factory()
            ->count($SHIPPING_COMPANIES_QUANTITY)
            ->create();
    }
}
