<?php

namespace Tests\Feature;

use App\Events\EventCreatedEvent;
use App\Events\PaggueCredentialsCreatedEvent;
use App\Listeners\CreatePagguePixWebhookListener;
use App\Listeners\DeletePaggueCredentialsWithinOneMonth;
use App\Models\Batch;
use App\Models\Event;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Tests\TestCase;

class PaggueCredentialsCreatedEventTest extends TestCase
{
    public function test_listeners_are_listening(): void
    {
        # ARRANGE
        LaravelEvent::fake();

        $credentials = PaggueCredentials::factory()->for(Promoter::factory())->create();

        # ACT
        PaggueCredentialsCreatedEvent::dispatch($credentials);

        # ASSERT
        LaravelEvent::assertListening(PaggueCredentialsCreatedEvent::class,
            CreatePagguePixWebhookListener::class,
            DeletePaggueCredentialsWithinOneMonth::class
        );
    }
}
