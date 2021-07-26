<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ClothingType;
use App\Models\ClothingTypeOrder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

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
        return [
            'code' => $this->faker->unique()->randomNumber(5),
            'name' => $this->faker->words(3, true),
            'client_id' => 1
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $clothingTypes = ClothingType::all();
            $totalValue = 0;
            $totalQuantity = 0;
            
            foreach ($clothingTypes as $type) {
                if ($this->faker->randomElement([true, false])) {
                    $value = $this->faker->randomFloat(2, 10, 350);
                    $quantity = $this->faker->numberBetween(1, 5);

                    ClothingTypeOrder::create([
                        'order_id' => $order->id,
                        'clothing_type_id' => $type->id,
                        'value' => $value,
                        'quantity' => $quantity
                    ]);

                    $totalValue += $value;
                    $totalQuantity += $quantity;
                }
            }

            $order->update([
                'quantity' => $totalQuantity,
                'price' => $order->totalClothingsValue() - $order->discount
            ]);

            Payment::factory()->count(rand(0, 10))->state(new Sequence(
                fn ($sequence) => [
                    'order_id' => $order->id,
                    'value' => $this->faker->randomFloat(2, 10, 100)
                ]
            ))->create();
        });
    }
}
