<?php

namespace App\Listeners;

use App\Events\PaggueCredentialsCreatedEvent;
use App\Models\PaggueCredentials;
use App\Services\PagguePaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePagguePixWebhookListener
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
    public function handle(PaggueCredentialsCreatedEvent $event): void
    {
        $credentials = $event->credentials;
        if(!$credentials->webhook_id){
            $webhook_id = PagguePaymentService::credentials($credentials)
                ->createPixWebhook();
            $credentials->webhook_id = $webhook_id;
            $credentials->save();
        }
    }
}
