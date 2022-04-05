<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Via;
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
            'order_id' => 1,
            'payment_via_id' => Via::inRandomOrder()->first()->id,
            'value' => $this->faker->randomFloat($precision = 2, 0, 1000),
            'date' =>  $createdAt->clone()->addDays($this->faker->numberBetween(-7, 7)),
            'created_at' => $createdAt,
        ];
    }

    public function confirmed($probability = 50)
    {
        if ($this->faker->boolean($probability)) {
            return $this->state(function ($attributes) {
                return $this->confirmedState($attributes);
            });
        }

        return $this->state(function ($attributes) {
            return $this->randomState($attributes);
        });
    }

    protected function randomState($attributes)
    {
        $createdAt = $attributes['created_at'];

        $data['is_confirmed'] = $this->faker->optional(.5)->boolean(50);
        $data['confirmed_at'] = null;

        if ($data['is_confirmed']) {
            $data['confirmed_at'] = $createdAt
                ->clone()
                ->addDays(
                    $this->faker->numberBetween(0, 5)
                );
        }

        return $data;
    }

    protected function confirmedState($attributes)
    {
        $createdAt = $attributes['created_at'];

        return [
            'is_confirmed' => true,
            'confirmed_at' => $createdAt->clone()->addDays(
                $this->faker->numberBetween(0, 5)
            )->toString()
        ];
    }
}
