<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Client;
use App\Models\Order;
use App\Models\ShippingCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    use FactoryByProbabilitiesTrait;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    protected static $methodsByProbability = [
        ['belongsToCity', 'chance' => 80],
        ['hasManyOrders', 'chance' => 75],
        ['belongsToShippingCompany', 'chance' => 75]
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->getClientName(),
            'phone' => $this->faker->optional(.8)->phoneNumberCleared,
            'created_at' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Client $client) {
            $this->executeMethodsByProbability($client);
        });
    }

    protected function getClientName()
    {
        $genders = ['male', 'female'];

        $firstName = $this->faker->firstName(
            $this->faker->randomElement($genders)
        );

        $lastName = $this->faker->lastName;

        return "$firstName $lastName";
    }

    protected function hasManyOrders(Client $client)
    {
        $quantity = $this
            ->faker
            ->numberBetween(1, 20);

        Order::factory()
            ->count($quantity)
            ->for($client)
            ->create();
    }

    protected function belongsToCity(Client $client)
    {
        $client->update([
            'city_id' => City::inRandomOrder()->first()->id
        ]);
    }

    protected function belongsToShippingCompany(Client $client)
    {
        $client->update([
            'shipping_company_id' => ShippingCompany::inRandomOrder()->first()->id
        ]);
    }
}
