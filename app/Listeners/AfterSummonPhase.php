<?php namespace App\Listeners;

use App\Events\SummonEvent;
use App\Models\TriggerQueue;

class AfterSummonPhase extends AbstractTrigger
{
    public $event_name = "after_summon_phase";

    /**
     * Handle the event.
     *
     * @param  SummonEvent $event
     * @return void
     */
    public function handle(SummonEvent $event) {
        $this->event      = $event;
        $summoned_minion  = $this->event->getSummonedMinion();
        $player           = $summoned_minion->getOwner();
        $player_minions   = $player->getMinionsInPlay();
        $opponent         = $player->getOtherPlayer();
        $opponent_minions = $opponent->getMinionsInPlay();


        $trigger_array = $this->getSetTriggers();

        $all_minions = $player_minions + $opponent_minions;

        foreach ($all_minions as $minion) {
            // todo, don't believe that it can immediately go over, probably wrong.
            if ($minion->getId() == $summoned_minion->getId()) {
                continue;
            }

            $trigger = array_get($trigger_array, $minion->getName() . '.' . $this->event_name);
            if(is_null($trigger)) {
                continue;
            }

            $tmp_trigger = new AfterSummonPhase();
            $tmp_trigger->trigger_card = $minion;
            $tmp_trigger->trigger_card_targets = $event->getTargets();

            /** @var TriggerQueue $trigger_queue */
            $trigger_queue = app('TriggerQueue');
            $trigger_queue->queue($tmp_trigger);
        }
    }
}
