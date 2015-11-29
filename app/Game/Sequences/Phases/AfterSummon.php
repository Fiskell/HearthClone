<?php namespace App\Game\Sequences\Phases;

use App\Exceptions\DumbassDeveloperException;
use App\Game\Cards\Card;
use App\Game\Cards\Minion;

class AfterSummon extends CardPhase
{
    public $phase_name = 'after_summon_phase';

    public function queue(Card $minion, array $targets = []) {
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

            $trigger = array_get($tmp_minion->getTrigger(), $this->phase_name . '.0');
            if (is_null($trigger)) {
                continue;
            }

            $trigger_cost      = array_get($trigger, 'cost');
            $trigger_operation = array_get($trigger, 'operation');
            if ($trigger_cost && $trigger_operation) {
                // todo not all statements implemented
                switch ($trigger_operation) {
                    case "<=":
                        $valid_cost = $minion->getCost() <= $trigger_cost;
                        break;
                    default:
                        throw new DumbassDeveloperException('Trigger cost operation ' . $trigger_operation . ' not implemented for after summon phase.');
                }

                if (!$valid_cost) {
                    return;
                }
            }

            $tmp_trigger          = new AfterSummon();
            $tmp_trigger->card    = $tmp_minion;
            $tmp_trigger->targets = [$minion];

            app('TriggerQueue')->queue($tmp_trigger);
        }
    }
}