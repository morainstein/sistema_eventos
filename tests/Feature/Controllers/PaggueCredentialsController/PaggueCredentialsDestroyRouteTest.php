<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthPromoter;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaggueCredentialsDestroyRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
        Http::fake();
    }
    
    public function test_really_destroy_credentials__return_200_ok_and_a_message(): void
    {
        # ARRANGE
        $promoter = Promoter::factory()->has(PaggueCredentials::factory(),'credentials')->create();
        Auth::login($promoter);

        # ACT
        $response = $this->deleteJson('/api/promoter/credentials/paggue');

        # ASSERT
        $response
            ->assertExactJson(['message' => 'Your credentials has been destroyed'])
            ->assertOk();

        $this->assertNull(PaggueCredentials::where('promoter_id',$promoter->id)->first());
    }
}
