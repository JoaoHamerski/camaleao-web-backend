<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

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
            ->count(1)
            ->has(
                Order::factory()->count(1)
            )->create();
    }
}
