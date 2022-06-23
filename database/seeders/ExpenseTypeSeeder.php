<?php

namespace Database\Seeders;

use App\Models\AppConfig;
use App\Models\ExpenseType;

class ExpenseTypeSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $EXPENSE_TYPES_QUANTITY = $this->faker->numberBetween(2, 6);

        ExpenseType::factory()
            ->count($EXPENSE_TYPES_QUANTITY)
            ->create();

        AppConfig::set(
            'app',
            'expense_types_ids_to_show',
            ExpenseType::inRandomOrder()->take(2)->get()->pluck('id')
        );
    }
}
