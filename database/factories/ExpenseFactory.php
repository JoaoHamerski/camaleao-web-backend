<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Via;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'expense_type_id' => ExpenseType::inRandomOrder()->first()->id,
            'expense_via_id' => Via::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'description' => $this->faker->sentence(10),
            'value' => $this->faker->randomFloat(2, 800, 2000),
            'date' => $createdAt,
            'created_at' => $createdAt,
        ];
    }
}
