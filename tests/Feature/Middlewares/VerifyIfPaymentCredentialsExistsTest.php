<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthPromoter;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\TestHelper;

class VerifyIfPaymentCredentialsExistsTest extends TestCase
{
    public function test_payment_credentials_must_exists_to_promoter_create_an_event(): void
    {
        # ARRANGE
        $this->withoutMiddleware(AuthPromoter::class);
        $promoter = Promoter::factory()->create();
        Auth::setUser($promoter);

        $data = TestHelper::generateDataToCreateAnEventModel($promoter->id);

        # ACT
        $response = $this->postJson('/api/events',$data);

        # ASSERT
        $response
            ->assertExactJson(['message' => "There are no registered payment credentials. First, sign up the credentials so you can create an event."])
            ->assertForbidden();
    }
}
