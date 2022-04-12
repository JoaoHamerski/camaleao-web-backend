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
        $createdAt = new Carbon(
            $this->faker->dateTimeBetween('-2 weeks', '+2 weeks')
        );

        return [
            'value' => $this->faker->randomFloat($precision = 2, 0, 1000),
            'date' =>  $createdAt->clone()->addDays($this->faker->numberBetween(-7, 7)),
            'created_at' => $createdAt,
        ];
    }

    public function confirmed($isConfirmed = false)
    {
        if ($isConfirmed) {
            return $this->state(
                fn ($attributes) => $this->confirmedState($attributes)
            );
        }

        return $this->state(
            fn ($attributes) => $this->randomState($attributes)
        );
    }

    protected function confirmedState($attributes)
    {
        $createdAt = new Carbon($attributes['created_at']);

        return [
            'is_confirmed' => true,
            'confirmed_at' => $createdAt->addHours(
                $this->faker->numberBetween(0, 48)
            )
        ];
    }

    protected function randomState($attributes)
    {
        $createdAt = new Carbon($attributes['created_at']);
        $isConfirmed = $this->faker->optional(.65)->boolean(65);
        $hasConfirmedDatetime = $isConfirmed !== null;

        return [
            'is_confirmed' => $isConfirmed,
            'confirmed_at' => $hasConfirmedDatetime
                ? $createdAt->addHours(
                    $this->faker->numberBetween(0, 48)
                )
                : null
        ];
    }
}
