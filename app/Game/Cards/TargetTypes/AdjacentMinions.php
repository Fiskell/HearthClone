<?php namespace App\Game\Cards\TargetTypes;

use App\Game\Cards\Minion;

class AdjacentMinions implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        $player_minions = $board_target_groups->getPlayerMinions();
        $trigger_card   = $board_target_groups->getTriggerCard();

        /** @var Minion $trigger_card */
        $adjacent_positions = [
            ($trigger_card->getPosition() - 1),
            ($trigger_card->getPosition() + 1)
        ];

        $targets = [];
        /** @var Minion $minion */
        foreach ($player_minions as $minion) {
            if (in_array($minion->getPosition(), $adjacent_positions)) {
                $targets[] = $minion;
            }
        }

        return $targets;
    }
}