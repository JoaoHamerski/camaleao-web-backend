<?php

namespace Database\Factories;

use App\Models\ShippingCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingCompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ShippingCompany::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => ucfirst($this->faker->words(
                $this->faker->numberBetween(1, 4),
                $asText = true
            ))
        ];
    }
}
