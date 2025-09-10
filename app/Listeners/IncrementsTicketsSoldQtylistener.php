<?php

namespace App\Listeners;

use App\Events\TicketPayedEvent;
use App\Models\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncrementsTicketsSoldQtylistener
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
        $batch = Batch::find($event->ticket->batch_id);
        $batch->increment('tickets_sold');
        $batch->save();
    }
}
