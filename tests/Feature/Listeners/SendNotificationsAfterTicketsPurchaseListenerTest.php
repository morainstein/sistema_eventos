<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Events\TicketPayedEvent;
use App\Http\Middleware\AuthPromoter;
use App\Listeners\SendNotificationsAfterTicketsPurchaseListener;
use App\Mail\NotifyCustomerTicketPurchaseSuccessfullyMail;
use App\Mail\NotifyPromoterTicketPurchaseMail;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Promoter;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendNotificationsAfterTicketsPurchaseListenerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
    }

    public function test_sends_notifications_after_ticket_purchase(): void
    {
        # ARRANGE
        Mail::fake();
        $event = Event::factory()->for(Promoter::class::factory())->create();
        $batch = Batch::factory()->for($event)->create();
        $ticket = Ticket::factory()->for(Customer::class::factory())
            ->for($event)->for($batch)->create([
                'payment_status' => PaymentStatus::PAYED->value,
                'final_price' => $batch->price
            ]);

        // # ACT
        (new SendNotificationsAfterTicketsPurchaseListener())
            ->handle(new TicketPayedEvent($ticket));

        # ASSERT
        Mail::assertQueued(NotifyCustomerTicketPurchaseSuccessfullyMail::class, function ($mail) use ($ticket) {
            return $mail->ticket->id === $ticket->id;
        });

        Mail::assertQueued(NotifyPromoterTicketPurchaseMail::class, function ($mail) use ($ticket) {
            return $mail->ticket->id === $ticket->id;
        });
    }

}
