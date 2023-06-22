<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Order;

class NoteSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = Order::all();

        $orders->each(function ($order) {
            $CHANCE_OF_ORDER_HAVING_NOTES = 35;
            $NOTES_FOR_EACH_ORDER = $this->faker->numberBetween(0, 5);

            if ($this->faker->boolean($CHANCE_OF_ORDER_HAVING_NOTES)) {
                Note::factory()
                    ->count($NOTES_FOR_EACH_ORDER)
                    ->for($order)
                    ->create();
            }
        });
    }
}
