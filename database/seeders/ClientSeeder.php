<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Client;
use App\Models\Payment;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        Client::factory()
            ->count(50)
            ->create();
    }
}
