<?php

namespace Tests\Feature;

use App\Events\TicketPayedEvent;
use App\Listeners\IncrementsTicketsSoldQtylistener;
use App\Listeners\SendNotificationsAfterTicketsPurchaseListener;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Promoter;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Tests\TestCase;

class TicketPayedEventTest extends TestCase
{
    public function test_listeners_are_listening(): void
    {
        # ARRANGE
        LaravelEvent::fake();

        $ticket = Event::factory()->for(Promoter::factory())
            ->has(Batch::factory()
                ->has(Ticket::factory()->for(Customer::factory())
                    ->state(fn($att,Batch $b)=>[
                        'event_id' => $b->event_id,
                        'final_price' => $b->price,
                    ])
                )
            )->create()->tickets[0];

        # ACT
        TicketPayedEvent::dispatch($ticket);

        # ASSERT
        LaravelEvent::assertListening(TicketPayedEvent::class,
            IncrementsTicketsSoldQtylistener::class,
            SendNotificationsAfterTicketsPurchaseListener::class
        );
    }
}
