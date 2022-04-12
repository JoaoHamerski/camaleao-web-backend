<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Status;
use App\Models\AppConfig;
use App\Models\ClothingType;
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
     * Data limite de inicio para o intervalo de datas
     * gerado em "created_at"
     *
     * @var string
     */
    protected $CREATED_AT_START_DATE = '-1 month';

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
            'updated_at' => $data['created_at']
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $this->populatePriceAndQuantity($order);
            $this->populateDeliveryAndProductionDate($order);
            $this->populateCommissions($order);
            $this->belongsToStatus($order);
        });
    }

    protected function belongsToStatus(Order $order)
    {
        $order->update([
            'status_id' => Status::inRandomOrder()->first()->id
        ]);
    }

    protected function populateCommissions(Order $order)
    {
        if ($order->isPreRegistered()) {
            return;
        }

        $confirmedAt = $this
            ->faker
            ->optional(.6)
            ->dateTimeBetween($order->created_at, 'now');

        $commission = $order->commissions()->create([
            'print_commission' => AppConfig::get('orders', 'print_commission'),
            'seam_commission' => $order->fresh()->getCommissions()->toJson(),
            'created_at' => $order->created_at,
            'updated_at' => $order->created_at
        ]);

        $users = User::production()->get();

        $users->each(function ($user) use ($commission, $confirmedAt) {
            $user->commissions()->attach([
                $commission->id => [
                    'role_id' => $user->role->id,
                    'commission_value' => $commission->getUserCommission($user),
                    'confirmed_at' => $confirmedAt,
                ]
            ]);
        });
    }

    protected function populatePriceAndQuantity(Order $order)
    {
        if ($isPreRegistered = $this->faker->boolean(10)) {
            $this->populatePreRegistered($order);
            return;
        }

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

        $order->update([
            'price' => $order->total_clothings_value,
            'quantity' => $order->clothingTypes()->sum('quantity')
        ]);
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

    protected function populateDeliveryAndProductionDate(Order $order)
    {
        $date = $this
            ->faker
            ->dateTimeBetween('-1 month', 'now');

        $productionDate = null;
        $deliveryDate = null;

        if ($hasProductionDate = $this->faker->boolean(70)) {
            $productionDate = (new Carbon($date));

            if ($hasDeliveryDate = $this->faker->boolean(85)) {
                $deliveryDate = (new Carbon($date))
                    ->addDays($this->faker->numberBetween(1, 20));
            }
        }

        $order->update([
            'production_date' => $productionDate,
            'delivery_date' => $deliveryDate
        ]);
    }
}
