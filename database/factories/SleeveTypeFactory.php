<?php

namespace Database\Factories;

use App\Models\SleeveType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SleeveTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SleeveType::class;

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
