<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Order;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends BaseSeeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $CLIENTS_QUANTITY = $this->faker->numberBetween(40, 60);

        Client::factory()
            ->count($CLIENTS_QUANTITY)
            ->create();
    }
}
