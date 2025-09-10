<?php

namespace Tests\Feature;

use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PromoterAuthenticateRouteTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_login_is_returning_200_ok_and_the_bearer_token(): void
    {
        # ARRANGE
        $password = fake('pt_BR')->password(8);
        $promoter = Promoter::factory()->create(['password' => $password]);

        $body = [
            'registry' => $promoter->registry,
            'password' => $password,
        ];

        # ACT
        $response = $this->postJson('/api/promoter/login',$body);

        # ASSERT
        $response
            ->assertJsonStructure(['token'])
            ->assertStatus(200);
    }

    public function test_wrong_credentials_return_401_unauthorized_and_a_message()
    {
        # ARRANGE
        $password = fake('pt_BR')->password(8);
        $promoter = Promoter::factory()->create(['password' => $password]);

        $body = [
            'registry' => $promoter->registry,
            'password' => '12345678',
        ];
        # ACT
        $response = $this->postJson('/api/promoter/login',$body);

        # ASSERT
        $response
            ->assertExactJson(['message' => 'Invalid credentials'])
            ->assertStatus(401);
    }
}
