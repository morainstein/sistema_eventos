<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Events\TicketPayedEvent;
use App\Http\Middleware\AuthPromoter;
use App\Listeners\IncrementsTicketsSoldQtylistener;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Promoter;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncrementsTicketsSoldQtyListenerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
    }

    public function test_increments_tickets_sold_qty(): void
    {
        # ARRANGE
        $event = Event::factory()->for(Promoter::class::factory())->create();
        $batch = Batch::factory()->for($event)->create();
        $ticket = Ticket::factory()->for(Customer::class::factory())
            ->for($event)->for($batch)->create([
                'payment_status' => PaymentStatus::PAYED->value,
                'final_price' => $batch->price
            ]);

        // # ACT
        (new IncrementsTicketsSoldQtylistener())
            ->handle(new TicketPayedEvent($ticket));

        # ASSERT
        $this->assertDatabaseHas('batches', [
            'id' => $batch->id,
            'tickets_sold' => 1
        ]);
    }
}
