<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/31/15
 * Time: 12:03 AM
 */

namespace App\Listeners;

use App\Events\DeathEvent;
use App\Models\Mechanics;
use App\Models\TriggerableInterface;
use App\Models\TriggerQueue;

class Deathrattle implements TriggerableInterface
{
    /** @var DeathEvent $event */
    protected $event;

    public function handle(DeathEvent $event)
    {
        $this->event = $event;
        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');
        $trigger_queue->queue($this);
    }

    public function resolve() {
        $killed_card = $this->event->getKilledCard();
        if(!$killed_card->hasMechanic(Mechanics::$DEATHRATTLE)) {
            return;
        }

        switch ($killed_card->getName()) {
            case 'Loot Hoarder':
                $killed_card->getOwner()->drawCard();
        }
    }
}