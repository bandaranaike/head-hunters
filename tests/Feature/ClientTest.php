<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientTest extends TestCase
{

    public function test_client_creation()
    {
        $clientData = [
            'name' => 'Test Client',
            'email' => 'test@example.com',
        ];

        Client::factory()->create($clientData);

        $this->assertDatabaseHas('clients', $clientData);
    }
}
