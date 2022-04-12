<?php

namespace Database\Factories;

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
        $CREATED_AT = $this->faker->dateTimeBetween('-2 months', 'now');

        return [
            'name' => $this->getClientName(),
            'phone' => $this->faker->optional(.8)->phoneNumberCleared,
            'created_at' => $CREATED_AT,
            'updated_at' => $CREATED_AT,
        ];
    }

    protected function getClientName()
    {
        $genders = ['male', 'female'];

        $firstName = $this->faker->firstName(
            $this->faker->randomElement($genders)
        );

        $lastName = $this->faker->lastName;

        return "$firstName $lastName";
    }
}
