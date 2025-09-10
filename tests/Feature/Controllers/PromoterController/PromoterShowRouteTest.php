<?php

namespace Tests\Feature;

use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PromoterShowRouteTest extends TestCase
{
    public function test_show_route_is_returning_200_ok_and_the_customer(): void
    {
        # ARRANGE
        $promoter = Promoter::factory()->create();

        $expectedJson = [
            "id" => $promoter->id,
            "phone" => $promoter->phone,
            "name" => $promoter->name,
            "email" => $promoter->email,
            "created_at" => $promoter->created_at,
        ];

        # ACT
        $response = $this->getJson("/api/promoter/{$promoter->id}");

        # ASSERT
        $response
            ->assertExactJson($expectedJson)
            ->assertStatus(200);
    }
}
