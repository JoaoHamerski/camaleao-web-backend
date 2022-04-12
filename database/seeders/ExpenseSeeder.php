<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Via;

class ExpenseSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $EXPENSES_QUANTITY = $this->faker->numberBetween(100, 500);

        Expense::factory()
            ->count($EXPENSES_QUANTITY)
            ->create()
            ->each(function ($expense) {
                $expense->update([
                    'user_id' => User::inRandomOrder()->first()->id,
                    'expense_via_id' => Via::inRandomOrder()->first()->id,
                    'expense_type_id' => ExpenseType::inRandomOrder()->first()->id
                ]);
            });
    }
}
