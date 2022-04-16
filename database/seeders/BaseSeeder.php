<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Bluemmb\Faker\PicsumPhotosProvider;

class BaseSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $faker = Faker::create('pt_BR');
        $faker->addProvider(new PicsumPhotosProvider($faker));
        $this->faker = $faker;
    }
}
