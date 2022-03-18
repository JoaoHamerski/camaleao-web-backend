<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Note;
use App\Models\User;
use App\Models\Order;
use App\Models\Status;
use App\Models\Payment;
use App\Models\AppConfig;
use App\Models\ClothingType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class OrderFactory extends Factory
{
    use FactoryByProbabilitiesTrait;

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

    protected static $methodsByProbability = [
        ['hasManyNotes', 'chance' => 20],
        ['belongsToStatus', 'chance' => 100],
        ['hasManyPayments', 'chance' => 90]
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $createdAt = $this->faker->dateTimeBetween(
            $this->CREATED_AT_START_DATE,
            'now'
        );

        return [
            'client_id' => 1,
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => ucfirst(
                $this->faker->words(
                    $this->faker->numberBetween(1, 4),
                    $asText = true
                )
            ),
            'created_at' => $createdAt,
            'updated_at' => $createdAt
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $this->populatePriceAndQuantity($order);
            $this->populateDeliveryAndProductionDate($order);
            $this->populateCommissions($order);
            $this->executeMethodsByProbability($order);
        });
    }

    protected function hasManyNotes(Order $order)
    {
        $NOTES_QUANTITY = $this->faker->numberBetween(1, 5);

        Note::factory()
            ->count($NOTES_QUANTITY)
            ->for($order)
            ->create();
    }

    protected function belongsToStatus(Order $order)
    {
        $order->update([
            'status_id' => Status::inRandomOrder()->first()->id
        ]);
    }

    protected function hasManyPayments(Order $order)
    {
        if ($this->faker->boolean(30)) {
            $this->factoryPaidOrder($order);
            return;
        }

        $this->factoryNotPaidOrder($order);
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

    protected function factoryPaidOrder(Order $order)
    {
        $MAX_PAYMENTS_QUANTITY = $this->faker->numberBetween(1, 7);

        $price = $order->price;
        $payments = collect();
        $valueAvg = bcdiv($price, $MAX_PAYMENTS_QUANTITY, 2);
        $total = 0;

        for ($i = 0; $i < $MAX_PAYMENTS_QUANTITY; $i++) {
            $value = round($this->faker->randomFloat(
                2,
                bcsub($valueAvg, $valueAvg * .2, 2),
                bcadd($valueAvg, $valueAvg * .2, 2)
            ), 1);

            $total = bcadd($total, $value, 2);
            $payments->push(['value' => $value]);

            if ($value >= $price) {
                $payments = collect([['value' => $price]]);
                break;
            }

            if (
                $this->isTotalExceedingPrice($total, $price)
                || $this->isTotalUnderPriceOnLastIteration(
                    $i === $MAX_PAYMENTS_QUANTITY - 1,
                    $total,
                    $price
                )
            ) {
                $payments->pop();
                $payments->push(
                    ['value' => bcsub($price, $payments->sum('value'), 2)]
                );

                break;
            }
        }

        $this->closePaidOrder($order, 30);
        $this->insertPayments($order, $payments, 100);
    }

    protected function closePaidOrder(Order $order, $chance)
    {
        if ($this->faker->boolean($chance)) {
            $order->update([
                'closed_at' => $this->faker->dateTimeBetween($order->created_at, 'now')
            ]);
        }
    }

    protected function factoryNotPaidOrder(Order $order)
    {
        $MAX_PAYMENTS_QUANTITY = $this->faker->numberBetween(1, 7);

        $price = $order->price;
        $payments = collect();
        $valueAvg = bcdiv($price, $MAX_PAYMENTS_QUANTITY, 2);

        for ($i = 0; $i < $MAX_PAYMENTS_QUANTITY; $i++) {
            $value = round($this->faker->randomFloat(
                2,
                bcsub($valueAvg, $valueAvg * .1, 2),
                bcadd($valueAvg, $valueAvg * .1, 2)
            ));

            $payments->push(['value' => $value]);
        }

        $this->insertPayments($order, $payments, 50);
    }

    protected function isTotalExceedingPrice($paymentsValue, $price)
    {
        return $paymentsValue >= $price;
    }

    protected function isTotalUnderPriceOnLastIteration($isLastIteration, $paymentsValue, $price)
    {
        return ($isLastIteration && $paymentsValue < $price);
    }

    protected function insertPayments(Order $order, $payments, $chanceOfConfirmation)
    {
        Payment::factory()
            ->for($order)
            ->count($payments->count())
            ->confirmed($chanceOfConfirmation)
            ->state(new Sequence(fn ($sequence) => [
                'value' => data_get($payments, "$sequence->index.value"),
                'date' => $this->faker->dateTimeBetween(
                    $order->created_at,
                    $order->closed_at ?? 'now'
                ),
                'created_at' => $this->faker->dateTimeBetween(
                    $order->created_at,
                    $order->closed_at ?? 'now'
                )
            ]))
            ->create();
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

        if ($hasReminder = $this->faker->boolean(70)) {
            $this->preRegisteredOrderHasReminder($order);
        }
    }

    protected function preRegisteredOrderHasReminder(Order $order)
    {
        Note::factory()
            ->state(['is_reminder' => true])
            ->for($order)
            ->create();
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
