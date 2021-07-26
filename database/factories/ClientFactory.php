<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender = ['male', 'female'];

        $firstName = $this->faker->firstName(
            $this->faker->randomElement($gender)
        );
        $lastName = $this->faker->lastName;

        return [
            'name' => $firstName . ' ' . $lastName,
            'phone' => $this->faker->phoneNumberCleared,
            'city_id' => $this->faker->randomElement([true, false])
                ? City::inRandomOrder()->first()->id
                : null
        ];
    }
}
