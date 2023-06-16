<?php

namespace Database\Seeders;

use App\Models\AppConfig;
use App\Models\Client;
use App\Models\Order;

class OrdersSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::all();

        $clients->each(function ($client) {
            $ORDERS_FOR_EACH_CLIENT = $this->faker->numberBetween(40, 60);

            Order::factory()
                ->count($ORDERS_FOR_EACH_CLIENT)
                ->for($client)
                ->create();
        });
    }
}
