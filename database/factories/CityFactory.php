<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->city
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (City $city) {
            $this->belongsToState($city);
        });
    }

    protected function belongsToState(City $city)
    {
        if ($this->faker->boolean($chanceOfTrue = 75)) {
            $city->update([
                'state_id' => State::inRandomOrder()->first()->id
            ]);
        }
    }
}
