<?php namespace App\Listeners;

use App\Events\SummonEvent;
use App\Models\TriggerQueue;

class SpellTextPhase extends AbstractTrigger
{
    public $event_name = 'spell_text_phase';

    /**
     * Handle the event.
     *
     * @param  SummonEvent $event
     * @return void
     */
    public function handle(SummonEvent $event)
    {
        $tmp_trigger = new SpellTextPhase();
        $tmp_trigger->trigger_card = $event->getSummonedMinion();
        $tmp_trigger->trigger_card_targets = $event->getTargets();

        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');
        $trigger_queue->queue($tmp_trigger);
    }
}
