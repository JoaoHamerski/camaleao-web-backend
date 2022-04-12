<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Client;
use App\Models\ShippingCompany;

class ClientSeeder extends BaseSeeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $CLIENTS_QUANTITY = $this->faker->numberBetween(10, 15);

        Client::factory()
            ->count($CLIENTS_QUANTITY)
            ->create()
            ->each(function ($client) {
                $client->update([
                    'city_id' => City::inRandomOrder()->first()->id
                ]);

                $client->update([
                    'shipping_company_id' => ShippingCompany::inRandomOrder()->first()->id
                ]);
            });
    }
}
