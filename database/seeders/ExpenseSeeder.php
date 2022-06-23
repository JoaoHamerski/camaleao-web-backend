<?php

namespace Database\Seeders;

use App\Models\Via;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\ProductType;

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
            ->make()
            ->each(function ($expense) {
                $this->makeExpense($expense);
            });
    }

    public function makeExpense(Expense $expense)
    {
        $expense->fill([
            'user_id' => User::inRandomOrder()->first()->id,
            'expense_via_id' => Via::inRandomOrder()->first()->id,
            'expense_type_id' => ExpenseType::inRandomOrder()->first()->id,
            'product_type_id' => ProductType::inRandomOrder()->first()->id,
            'is_confirmed' => $this->faker->optional(.8)->boolean(80)
        ]);

        if ($expense->is_confirmed) {
            $expense->confirmed_at = now();
        }

        $expense->save();
    }
}
