<?php

namespace Tests\Feature;

use App\Events\EventCreatedEvent;
use App\Listeners\NotifyAdminsAnEventHasBeenCreatedListener;
use App\Models\Batch;
use App\Models\Event;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Tests\TestCase;

class EventCreatedEventTest extends TestCase
{
    public function test_listeners_are_listening(): void
    {
        # ARRANGE
        LaravelEvent::fake();
        $event = Event::factory()->for(Promoter::factory())->has(Batch::factory())->create();

        # ACT
        EventCreatedEvent::dispatch($event);

        # ASSERT
        LaravelEvent::assertListening(EventCreatedEvent::class,
            NotifyAdminsAnEventHasBeenCreatedListener::class
        );
    }
}
