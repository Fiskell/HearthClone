<?php

namespace App\Listeners;

use App\Events\OnPlayPhaseEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnPlayPhase
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OnPlayPhaseEvent  $event
     * @return void
     */
    public function handle(OnPlayPhaseEvent $event)
    {
        //
    }
}
