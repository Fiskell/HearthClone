<?php

namespace App\Listeners;

use App\Events\AfterSummonPhaseEvent;
use App\Models\Minion;
use App\Models\TriggerableInterface;
use App\Models\TriggerQueue;

class AfterSummonPhase implements TriggerableInterface
{
    /** @var  AfterSummonPhaseEvent $event */
    private $event;

    /**
     * Handle the event.
     *
     * @param  AfterSummonPhaseEvent $event
     * @return void
     */
    public function handle(AfterSummonPhaseEvent $event) {
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

            if ($minion->getId() == $summoned_minion->getId()) {
                continue;
            }

            $hero_id                    = $opponent->getHero()->getId();
            $opponent_minions[$hero_id] = $opponent->getHero();
            // todo fix random numbers using ioc
//            $random_key                 = array_rand($opponent_minions);
            $opponent_minion_keys = array_keys(array_sort($opponent_minions, function(Minion $value) {
                return $value->getId();
            }));
            $random_key    = $minion->getRandomNumber();
            $random_minion = $opponent_minions[$opponent_minion_keys[$random_key]];
            $random_minion->takeDamage(1);
        }
    }
}
