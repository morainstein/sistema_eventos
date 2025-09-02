<?php

namespace App\Listeners;

use App\Events\TicketPayedEvent;
use App\Mail\NotifyCustomerTicketPurchaseSuccessfullyMail;
use App\Mail\NotifyPromoterTicketPurchaseMail;
use App\Models\Promoter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendNotificationsAfterTicketsPurchaseListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketPayedEvent $event): void
    {
        $ticket = $event->ticket;

        $promoter = Promoter::find($ticket->event->promoter_id);

        $promoterMail = new NotifyCustomerTicketPurchaseSuccessfullyMail($ticket->event,$ticket,$ticket->customer);
        $customerMail = new NotifyPromoterTicketPurchaseMail($promoter,$ticket->event,$ticket,$ticket->customer);
        Mail::queue($promoterMail);
        Mail::queue($customerMail);
    }
}
