<?php

namespace App\Listeners;

use App\Events\PaggueCredentialsCreatedEvent;
use App\Services\PagguePaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeletePaggueCredentialsWithinOneMonth implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function delay()
    {
        return now()->addMonth();
    }

    /**
     * Handle the event.
     */
    public function handle(PaggueCredentialsCreatedEvent $event): void
    {
        $credentials = $event->credentials;
        PagguePaymentService::credentials($credentials)->deletePixWebhook();
        $credentials->delete();
    }
}
