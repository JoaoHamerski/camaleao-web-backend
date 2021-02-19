<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $delivery_dates = [
            '2020-08-20',
            '2020-08-21',
            '2020-08-22',
        ];

        $production_dates = [
            '2020-09-10',
            '2020-09-11',
            '2020-09-12',
        ];

        return [
            'code' => $this->faker->unique()->numberBetween(1, 1000),
            'status_id' => $this->faker->numberBetween(1, 6),
            'quantity' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 0, 3000),
            'delivery_date' => $this->faker->randomElement($delivery_dates),
            'production_date' => $this->faker->randomElement($production_dates)
        ];
    }

    public function configure()
    {
        $times = [0, 0, 0, 1, 2, 3, 4];

        return $this->afterCreating(function(Order $order) use ($times) {
            Note::factory()
                ->times($this->faker->randomElement($times))
                ->create(['order_id' => $order->id]);
        });
    }
}
