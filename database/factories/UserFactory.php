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
            'name' => $this->faker->name,
            'email' => 'admin@email.com',
            'email_verified_at' => now(),
            'password' => '$2a$10$9eHiEcdKroxX/DCEas0.f.O7QnrVE9Aa2.bTUSHeguap7LOkYqhO6', // password
            'remember_token' => Str::random(10),
        ];
    }
}
