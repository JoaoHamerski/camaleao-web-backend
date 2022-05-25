<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Expense;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $createdAt = new Carbon($this->faker->dateTimeBetween('-1 month', 'now'));

        return [
            'description' => $this->faker->sentence(5),
            'value' => $this->faker->randomFloat(2, 800, 2000),
            'date' => $createdAt,
            'created_at' => $createdAt,
        ];
    }
}
