<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GraphqlUserQueryTest extends TestCase
{
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $response = $this->graphQL('
            mutation {
                login(email: "gerencia@email.com" password: "123456") {
                    token
                }
            }
        ');

        $this->token = 'Bearer ' . $response->json('data.login.token');
    }

    public function testUserQuery()
    {
        $query = '
            {
                users {
                    id
                    name
                    email
                    created_at
                    role {
                        id
                        name
                    }
                }
            }
        ';

        $response = $this->withHeaders([
            'authorization' => $this->token
        ])->graphQL($query);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'users' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'created_at',
                            'role' => [
                                'id',
                                'name'
                            ]
                        ]
                    ]
                ]
            ]);
    }
}
