<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GraphqlTest extends TestCase
{
    public function testGraphqlConnection()
    {
        $response = $this->post(config('lighthouse.route.uri'));

        $response->assertStatus(200);
    }
}
