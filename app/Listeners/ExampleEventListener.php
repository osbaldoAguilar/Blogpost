<?php

namespace App\Listeners;

use App\Events\OurExampleEvent;
use Illuminate\Support\Facades\Log;

class ExampleEventListener
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
    public function handle(OurExampleEvent $event): void
    {
        //
        Log::debug("The user {$event->username} just performed the action of {$event->action}");
    }
}
