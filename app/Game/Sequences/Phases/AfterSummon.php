<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 5:50 PM
 */

namespace App\Game\Sequences\Phases;

use App\Game\Cards\Minion;
use App\Models\TriggerQueue;

class AfterSummon extends CardPhase
{
    public $phase_name = 'after_summon_phase';

    function queue(Minion $minion, array $targets = []) {
        $player           = $minion->getOwner();
        $player_minions   = $player->getMinionsInPlay();
        $opponent         = $player->getOtherPlayer();
        $opponent_minions = $opponent->getMinionsInPlay();

        /** @var Minion[] $all_minions */
        $all_minions = $player_minions + $opponent_minions;

        foreach ($all_minions as $tmp_minion) {
            // todo, don't believe that it can immediately go over, probably wrong.
            if ($minion->getId() == $tmp_minion->getId()) {
                continue;
            }

            $trigger = array_get($tmp_minion->getTrigger(), $this->phase_name);
            if (is_null($trigger)) {
                continue;
            }

            $tmp_trigger          = new AfterSummon();
            $tmp_trigger->card    = $tmp_minion;
            $tmp_trigger->targets = $targets;

            /** @var TriggerQueue $trigger_queue */
            $trigger_queue = app('TriggerQueue');
            $trigger_queue->queue($tmp_trigger);
        }
    }
}