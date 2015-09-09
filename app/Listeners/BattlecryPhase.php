<?php namespace App\Listeners;

use App\Events\SummonEvent;
use App\Exceptions\InvalidTargetException;
use App\Models\Mechanics;
use App\Models\Minion;
use App\Models\TriggerQueue;

class BattlecryPhase extends AbstractTrigger
{
    public $event_name = 'battlecry';
    /**
     * Handle the event.
     *
     * @param SummonEvent $event
     * @throws InvalidTargetException
     */
    public function handle(SummonEvent $event) {
        $this->event = $event;

        if ($event->getSummonedMinion()->hasMechanic(Mechanics::$BATTLECRY)) {

            /** @var Minion $target */
            foreach ($event->getTargets() as $target) {
                if ($target->hasMechanic(Mechanics::$STEALTH)) {
                    throw new InvalidTargetException('Cannot silence stealth minion');
                }
            }

            $this->trigger_card = $event->getSummonedMinion();
            $this->trigger_card_targets = $event->getTargets();

            /** @var TriggerQueue $trigger_queue */
            $trigger_queue = app('TriggerQueue');
            $trigger_queue->queue($this);
        }
    }
}
