<?php

namespace App\Listeners;

use App\Events\SpellTextPhaseEvent;
use App\Events\SummonEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SpellTextPhase
{
    /**
     * Handle the event.
     *
     * @param  SummonEvent $event
     * @return void
     */
    public function handle(SummonEvent $event)
    {
        //
    }
}
