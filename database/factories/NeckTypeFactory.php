<?php

namespace Database\Factories;

use App\Models\NeckType;
use Illuminate\Database\Eloquent\Factories\Factory;

class NeckTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NeckType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => ucfirst(
                $this->faker->unique()->words($this->faker->numberBetween(1, 3), true)
            )
        ];
    }
}
