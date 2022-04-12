<?php

namespace Tests\Feature;

use App\Models\AppConfig;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrdersQueriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        AppConfig::set('orders', 'print_commission', '1.50');

        $ROLES = config('app.roles');

        $user = User::factory()->create();

        $user->role()->associate(
            Role::find($ROLES['GERENCIA'])
        );

        $this->be($user);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testOrdersClothingTypes()
    {
        $client = Client::factory()->create();
        $GRAPHQL_QUERY = <<<STR
            mutation {
                orderCreate (client_id: "$client->id" input: {
                    code: "1000"
                    clothing_types: [
                        {key: "white_shirt" value: "R$ 10,4" quantity: "2"}
                    ]
                }) {
                    id
                }
            }
        STR;

        $response = $this->graphQL($GRAPHQL_QUERY);

        var_dump($response->json());
    }
}
