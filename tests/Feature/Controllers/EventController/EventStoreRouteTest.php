<?php

namespace Tests\Feature;

use App\Events\EventCreatedEvent;
use App\Http\Middleware\AuthPromoter;
use App\Http\Middleware\VerifyIfPaymentCredentialsExists;
use App\Listeners\NotifyAdminsAnEventHasBeenCreatedListener;
use App\Mail\EventCreatedMail;
use App\Models\Admin;
use App\Models\Batch;
use App\Models\Event;
use App\Models\Promoter;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\TestHelper;

class EventStoreRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
        $this->withoutMiddleware(VerifyIfPaymentCredentialsExists::class);
    }

    public function test_an_event_created_has_to_dispatch_an_laravel_event_and_return_201_created(): void
    {
        # ARRANGE
        $user = Promoter::factory()->create();
        Auth::setUser($user);

        LaravelEvent::fake();

        $data = TestHelper::generateDataToCreateAnEventModel($user->id);

        # ACT
        $response = $this->postJson('/api/events',$data);
        
        # ASSERT
        $response
            ->assertExactJson(['message' => 'Event registered successfully'])
            ->assertCreated();

        LaravelEvent::assertDispatched(EventCreatedEvent::class);
    }
}
