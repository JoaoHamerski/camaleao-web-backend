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

            $this->populateClothes($order);
            $this->populateValuesAndQuantity($order);
        });
    }

    protected function populateValuesAndQuantity($order)
    {
        $order->refresh();
        $isGarment = !!$order->garments()->count();

        $clothesValue = $isGarment
            ? $order->total_garments_value
            : $order->total_clothings_value;

        $shipping_value = $this->faker->optional(.35)->randomFloat(2, 0, $clothesValue);
        $discount = $this->faker->optional(.35)->randomFloat(2, 0, $clothesValue / 2);

        $price = bcsub($clothesValue, $discount ?? 0, 2);
        $price = bcadd($price, $shipping_value ?? 0, 2);

        $quantity = $isGarment
            ? $order->garments->sum('quantity')
            : $order->clothingTypes()->sum('quantity');

        $order->update(
            compact(
                'price',
                'quantity',
                'shipping_value',
                'discount'
            )
        );
    }

    protected function populateClothes(Order $order)
    {
        // Ativo quando quiser testar retrocompatibilidade com clothingTypes
        // if ($this->faker->boolean(20)) {
        //     $this->populateClothingTypes($order);
        //     return;
        // }

        $this->populateGarments($order);
    }

    protected function populateGarments($order)
    {
        $ORDER_GARMENTS_COUNT = $this->faker->numberBetween(1, 5);

        for ($i = 0; $i < $ORDER_GARMENTS_COUNT; $i++) {
            $match = GarmentMatch::inRandomOrder()->first();
            $individualNames = $this->populateIndividualNames($match);

            $garment = $order->garments()->create([
                'garment_match_id' => $match->id,
                'individual_names' => json_encode($individualNames)
            ]);

            $this->populateGarmentSizes($garment, $match, $individualNames);
        }
    }

    protected function populateGarmentSizes($garment, $match, $individualNames = null)
    {
        if ($individualNames) {
            $sizes = collect($individualNames);
            $grouped = $sizes->groupBy('size_id');

            $grouped->each(function ($group, $id) use ($garment) {
                $garment->sizes()->attach([
                    $id => ['quantity' => $group->count()]
                ]);
            });

            return;
        }

        $match->sizes->each(function ($size) use ($garment) {
            $garment->sizes()->attach([
                $size->id => [
                    'quantity' => $this->faker->numberBetween(1, 10)
                ]
            ]);
        });
    }

    protected function populateIndividualNames($match)
    {
        if ($this->faker->boolean(60)) {
            return null;
        }

        $NAMES_QUANTITY = $this->faker->numberBetween(1, 20);
        $names = [];

        for ($i = 0; $i < $NAMES_QUANTITY; $i++) {
            $names[] = [
                'name' => $this->faker->name(),
                'number' => $this->faker->numberBetween(0, 999),
                'size_id' => $match->sizes->random()->id
            ];
        }

        return $names;
    }

    protected function populateClothingTypes($order)
    {
        $clothingTypes = ClothingType::inRandomOrder()
            ->take($this->faker->numberBetween(1, 5))
            ->get();

        $clothingTypes->each(function (ClothingType $clothingType) use ($order) {
            $order->clothingTypes()->attach([
                $clothingType->id => [
                    'value' => round($this->faker->randomFloat(2, 20, 50), 1),
                    'quantity' => $this->faker->numberBetween(1, 15)
                ]
            ]);
        });
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
