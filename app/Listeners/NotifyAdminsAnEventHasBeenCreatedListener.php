<?php

namespace App\Listeners;

use App\Events\EventCreatedEvent;
use App\Mail\EventCreatedMail;
use App\Models\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAdminsAnEventHasBeenCreatedListener implements ShouldQueue
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
    public function handle(EventCreatedEvent $event): void
    {
        $event = $event->event;
        $admins = Admin::all();
        
        foreach($admins as $admin){
            $email = new EventCreatedMail($admin,$event);
            Mail::queue($email);
        }
    }
}
