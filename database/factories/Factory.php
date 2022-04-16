<?php

namespace Database\Factories;

use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Factories\Factory as LaravelFactory;
use Bluemmb\Faker\PicsumPhotosProvider;

abstract class Factory extends LaravelFactory
{
    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        $faker = Container::getInstance()->make(Generator::class);
        $faker->addProvider(new PicsumPhotosProvider($faker));

        return $faker;
    }
}
