<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $isConfirmed = $this->faker->randomElement([true, true, false, null]);
        $confirmedAt = null;

        if ($isConfirmed) {
            $confirmedAt = Carbon::now();
        }

        $createdAt = Carbon::now()->today()->subDays(rand(1, 5));

        return [
            'order_id' => 1,
            'payment_via_id' => rand(1, 4),
            'value' => '',
            'date' => $createdAt->toDateString(),
            'is_confirmed' => $isConfirmed,
            'confirmed_at' => $confirmedAt,
            'created_at' => $createdAt
        ];
    }
}
