<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Status;
use App\Models\AppConfig;
use App\Models\ClothingType;
use App\Models\GarmentMatch;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Data limite de inicio para o intervalo de datas
     * gerado em "created_at"
     *
     * @var string
     */
    protected $CREATED_AT_START_DATE = '-3 months';

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $data = [
            'code' => $this->faker->unique()->randomNumber(
                $this->faker->numberBetween(3, 8)
            ),
            'name' => $this->faker->words(
                $this->faker->numberBetween(1, 5),
                $asText = true
            ),
            'created_at' => $this->faker->dateTimeBetween(
                $this->CREATED_AT_START_DATE,
                'now'
            ),
        ];

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'code' => $data['code'],
            'name' => $data['name'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['created_at'],
            'status_id' => Status::inRandomOrder()->first()->id,
            'delivery_date' => $this->faker->dateTimeBetween('-3 months', '+3 months')
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            if ($this->faker->boolean(10)) {
                $this->populatePreRegistered($order);
                return;
            }

            $this->populateProducts($order);
            $this->populateValuesAndQuantity($order);
        });
    }

    protected function populateValuesAndQuantity($order)
    {
        $order->refresh();
        $totalValue = $order->total_products_value;

        $shipping_value = $this->faker->optional(.35)->randomFloat(2, 0, $totalValue);
        $discount = $this->faker->optional(.35)->randomFloat(2, 0, $totalValue / 2);

        $price = bcsub($totalValue, $discount ?? 0, 2);
        $price = bcadd($price, $shipping_value ?? 0, 2);

        $quantity = $order->products->sum('quantity');

        $order->update(
            compact(
                'price',
                'quantity',
                'shipping_value',
                'discount'
            )
        );
    }

    protected function populateProducts($order)
    {
        $QUANTITY = $this->faker->numberBetween(1, 5);


        for ($i = 0; $i < $QUANTITY; $i++) {
            $order->products()->create([
                'description' => $this->faker->sentence(10),
                'value' => $this->faker->randomFloat(2, 1, 100),
                'quantity' => $this->faker->numberBetween(1, 5),
                'unity' => $this->faker->randomElement(['un', 'cx', 'cpx'])
            ]);
        }
    }

    protected function populatePreRegistered(Order $order)
    {
        $price = round($this->faker->randomFloat(2, 20, 50), 1)
            * $this->faker->numberBetween(1, 15)
            * $this->faker->numberBetween(1, 5);

        $price = round($price, $precision = 1);

        $order->update([
            'price' => $price
        ]);
    }
}
