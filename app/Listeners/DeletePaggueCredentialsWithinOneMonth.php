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

    public function withDelay(PaggueCredentialsCreatedEvent $event): int
    {
        return 60 * 60 * 24 * 30; // 30 days
    }

    public function handle(PaggueCredentialsCreatedEvent $event): void
    {
        $credentials = $event->credentials;
        PagguePaymentService::credentials($credentials)->deletePixWebhook();
        $credentials->delete();
    }
}
