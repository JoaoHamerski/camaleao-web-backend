<?php

namespace Database\Seeders;

use App\Models\Expense;

class ExpenseSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $EXPENSES_QUANTITY = $this->faker->numberBetween(1000, 2000);

        Expense::factory()
            ->count($EXPENSES_QUANTITY)
            ->create();
    }
}
