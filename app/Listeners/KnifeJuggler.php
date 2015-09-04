<?php

namespace App\Listeners;

use App\Events\SummonMinionEvent;
use App\Models\TriggerableInterface;
use App\Models\TriggerQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class KnifeJuggler implements TriggerableInterface
{
    /** @var  SummonMinionEvent */
    private $event;

    /**
     * Handle the event.
     *
     * @param  SummonMinionEvent $event
     * @return void
     */
    public function handle(SummonMinionEvent $event) {
        $this->event = $event;

        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');
        $trigger_queue->queue($this);
    }

    public function resolve() {
        $summoned_minion  = $this->event->getSummonedMinion();
        $player           = $summoned_minion->getOwner();
        $player_minions   = $player->getMinionsInPlay();
        $opponent         = $player->getOtherPlayer();
        $opponent_minions = $opponent->getMinionsInPlay();

        foreach ($player_minions as $minion) {
            if ($minion->getName() != 'Knife Juggler') {
                continue;
            }

            if($minion->getId() == $summoned_minion->getId()) {
                continue;
            }

            $hero_id                    = $opponent->getHero()->getId();
            $opponent_minions[$hero_id] = $opponent->getHero();
            $random_key                 = array_rand($opponent_minions);
            $random_minion              = $opponent_minions[$random_key];
            $random_minion->takeDamage(1);
        }
    }
}
