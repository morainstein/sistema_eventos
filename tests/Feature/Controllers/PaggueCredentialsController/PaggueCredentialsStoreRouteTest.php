<?php

namespace Tests\Feature;

use App\Events\PaggueCredentialsCreatedEvent;
use App\Http\Middleware\AuthPromoter;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
use Database\Factories\PaggueCredentialsFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaggueCredentialsStoreRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
        Http::fake();
        LaravelEvent::fake();
    }

    public function test_dispatch_PaggueCredentialsCreatedEvent__return_201_created_and_a_message_without_credentials_in_database(): void
    {
        # ARRANGE
        $promoter = Promoter::factory()->create();
        Auth::login($promoter);

        $company_id = '12345';
        $webhook_token = PaggueCredentialsFactory::webhookTokenExample();
        $bearer_token = PaggueCredentialsFactory::bearerTokenExample();

        $body = [
            'company_id' => $company_id,
            'webhook_token' => $webhook_token,
            'bearer_token' => $bearer_token,
        ];

        # ACT
        $response = $this->postJson('/api/promoter/credentials/paggue',$body);

        # ASSERT
        $message = 'Credentials has been stored. For security reasons, credentials will be destroyed within a month. In case you had old credentials, it has been replaced by these new ones you sent now';
        $response
            ->assertExactJson(['message' => $message])
            ->assertCreated();
        
        $this->assertDatabaseHas('paggue_credentials',[
            'company_id' => $company_id,
            'webhook_token' => $webhook_token,
            'bearer_token' => $bearer_token,
        ]);
        
        LaravelEvent::assertDispatched(PaggueCredentialsCreatedEvent::class);
    }

    public function test_dispatch_PaggueCredentialsCreatedEvent__return_201_created_and_a_message_already_has_credentials_in_database(): void
    {
        
        # ARRANGE
        $promoter = Promoter::factory()->has(PaggueCredentials::factory(),'credentials')->create();
        Auth::login($promoter);

        $body = [
            'company_id' => '12345',
            'webhook_token' => 'webhook_token-example',
            'bearer_token' => 'bearer_token-example',
        ];

        # ACT
        $response = $this->postJson('/api/promoter/credentials/paggue',$body);
        
        # ASSERT
        $message = 'Credentials has been stored. For security reasons, credentials will be destroyed within a month. In case you had old credentials, it has been replaced by these new ones you sent now';
        $response
            ->assertExactJson(['message' => $message])
            ->assertCreated();

        $this->assertEquals(1,PaggueCredentials::where('promoter_id', $promoter->id)
            ->count(), 'There is more than one credentials for the given promoter');

        LaravelEvent::assertDispatched(PaggueCredentialsCreatedEvent::class);
    }
}
