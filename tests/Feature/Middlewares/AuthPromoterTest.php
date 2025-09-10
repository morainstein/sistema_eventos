<?php

namespace Tests\Feature;

use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\TestHelper;

class AuthPromoterTest extends TestCase
{
    public function test_promoter_has_to_be_authenticated_to_create_an_event(): void
    {
        # ARRANGE
        $promoter = Promoter::factory()->create();
        $data = TestHelper::generateDataToCreateAnEventModel($promoter->id);

        # ACT
        $response = $this->postJson('/api/events',$data);

        # ASSERT
        $response
            ->assertExactJson(['message' => 'Unauthorized'])
            ->assertUnauthorized();
    }

    public function test_deny_when_promoter_isnt_authenticated(): void
    {
        $this->putJson('/api/promoter')
            ->assertExactJson(['message' => 'Unauthorized'])
            ->assertUnauthorized();
    }
}
