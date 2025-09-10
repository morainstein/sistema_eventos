<?php

namespace Tests\Feature;

use App\Events\PaggueCredentialsCreatedEvent;
use App\Http\Middleware\AuthPromoter;
use App\Listeners\CreatePagguePixWebhookListener;
use App\Models\Event;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CreatePagguePixWebhookListenerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
        Http::fake(Http::response(['id' => 123]));
        LaravelEvent::fake();
    }

    public function test_register_a_new_webhook_if_there_is_no_webhook_id(): void
    {
        # ARRANGE
        $credentials = PaggueCredentials::factory()
            ->for(Promoter::factory(),'promoter')
            ->create(['webhook_id' => null]);

        # ACT
        (new CreatePagguePixWebhookListener())
            ->handle(new PaggueCredentialsCreatedEvent($credentials));

        # ASSERT
        $this->assertDatabaseHas('paggue_credentials',[
            'id' => $credentials->id,
            'webhook_id' => '123'
        ]);
    }

    public function test_if_there_is_a_webhook_id_registered_doesnt_create_a_new(): void
    {
        # ARRANGE
        $credentials = PaggueCredentials::factory()
            ->for(Promoter::factory(),'promoter')
            ->create(['webhook_id' => 321]);

        # ACT
        (new CreatePagguePixWebhookListener())
            ->handle(new PaggueCredentialsCreatedEvent($credentials));

        # ASSERT
        $this->assertDatabaseHas('paggue_credentials',[
            'id' => $credentials->id,
            'webhook_id' => 321
        ]);
    }
}
