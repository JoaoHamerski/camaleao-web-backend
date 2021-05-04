<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email' => 'admin@email.com',
            'email_verified_at' => now(),
            'password' => '$2a$10$tJJM84SkQBkUb8rHYjlF..qT2r7mnjQXv04a1zGsKdIXrKu5SG.UO',
            'remember_token' => Str::random(10),
        ];
    }
}
