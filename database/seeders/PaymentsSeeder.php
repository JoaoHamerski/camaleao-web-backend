<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Via;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Collection;

class PaymentsSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = Order::all();

        $orders->each(function (Order $order) {
            $CHANCE_OF_ORDER_BE_PAID = 30;

            $this->factoryPayments(
                $order,
                $this->faker->boolean($CHANCE_OF_ORDER_BE_PAID)
            );
        });
    }

    protected function factoryPayments(Order $order, bool $isOrderPaid)
    {
        $payments = collect();
        $price = $order->price;

        do {
            $payment = $this->faker->randomFloat(1, 50, 200);
            $payments->push($payment);

            if ($payments->sum() > $price) {
                $payments->pop();
                $payments->push(
                    $price - $payments->sum()
                );
            }
        } while ($payments->sum() < $price);

        $this->seedPayments(
            $order,
            $payments,
            $isOrderPaid
        );
    }

    protected function seedPayments(Order $order, Collection $payments, $isConfirmed): void
    {
        Payment::factory()
            ->for($order)
            ->count($payments->count())
            ->state(new Sequence(fn ($sequence) => [
                'value' => $payments->get($sequence->index),
                'payment_via_id' => Via::inRandomOrder()->first()->id,
                'date' => $this->faker->dateTimeBetween(
                    $order->created_at,
                    $order->closed_at ?? 'now'
                ),
                'created_at' => $this->faker->dateTimeBetween(
                    $order->created_at,
                    $order->closed_at ?? 'now'
                )
            ]))
            ->confirmed($isConfirmed)
            ->create();
    }
}
