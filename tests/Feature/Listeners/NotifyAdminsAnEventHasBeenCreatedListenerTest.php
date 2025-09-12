<?php

namespace Tests\Feature;

use App\Events\EventCreatedEvent;
use App\Listeners\NotifyAdminsAnEventHasBeenCreatedListener;
use App\Mail\EventCreatedMail;
use App\Models\Admin;
use App\Models\Batch;
use App\Models\Event;
use App\Models\Promoter;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotifyAdminsAnEventHasBeenCreatedListenerTest extends TestCase
{
    public function test_listener_is_being_pushed_to_queue()
    {
        # ARRANGE
        Queue::fake();
        $event = Event::factory()->for(Promoter::factory())->has(Batch::factory())->create();

        # ACT
        EventCreatedEvent::dispatch($event);

        # ASSERT
        Queue::assertPushed(CallQueuedListener::class, function ($job){
            return $job->class === NotifyAdminsAnEventHasBeenCreatedListener::class;
        });
    }
    
    public function test_pushs_admins_emails_to_queue()
    {
        # ARRANGE
        Mail::fake();
        Admin::factory(2)->create();
        $event = Event::factory()->for(Promoter::factory())->has(Batch::factory())->create();

        # ACT
        (new NotifyAdminsAnEventHasBeenCreatedListener())->handle(new EventCreatedEvent($event));

        # ASSERT
        Mail::assertQueued(EventCreatedMail::class);
    }
}
