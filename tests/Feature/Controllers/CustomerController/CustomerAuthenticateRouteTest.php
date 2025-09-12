<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerAuthenticateRouteTest extends TestCase
{    
    public function test_login_is_returning_200_ok_and_the_bearer_token(): void
    {
        # ARRANGE
        $password = fake('pt_BR')->password(8);
        $customer = Customer::factory()->create(['password' => $password]);

        $body = [
            'registry' => $customer->registry,
            'password' => $password,
        ];

        # ACT
        $response = $this->postJson('/api/customer/login',$body);

        # ASSERT
        $response
            ->assertJsonStructure(['token'])
            ->assertStatus(200);
    }

    public function test_wrong_credentials_return_401_unauthorized_and_a_message()
    {
        # ARRANGE
        $password = fake('pt_BR')->password(8);
        $customer = Customer::factory()->create(['password' => $password]);

        $body = [
            'registry' => $customer->registry,
            'password' => '12345678',
        ];
        # ACT
        $response = $this->postJson('/api/customer/login',$body);

        # ASSERT
        $response
            ->assertExactJson(['message' => 'Invalid credentials'])
            ->assertStatus(401);
    }
}
