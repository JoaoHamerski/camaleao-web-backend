<?php

namespace Database\Factories;

use App\Models\ExpenseType;

class ExpenseTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExpenseType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(
                $this->faker->numberBetween(1, 4),
                true
            )
        ];
    }
}
