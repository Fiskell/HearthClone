<?php namespace App\Game\Cards\TargetTypes;

use App\Game\Cards\Minion;

class AllOtherMinionsWithRace implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        $player_minions   = $board_target_groups->getPlayerMinions();
        $opponent_minions = $board_target_groups->getOpponentMinions();
        $target_race      = $board_target_groups->getTargetRace();

        unset($player_minions[$board_target_groups->getTriggerCard()->getId()]);

        $targets = [];
        /** @var Minion $player_minion */
        foreach ($player_minions as $player_minion) {
            if ($player_minion->getRace() == $target_race) {
                $targets[] = $player_minion;
            }
        }

        /** @var Minion $opponent_minion */
        foreach ($opponent_minions as $opponent_minion) {
            if ($opponent_minion->getRace() == $target_race) {
                $targets[] = $opponent_minion;
            }
        }

        return $targets;
    }
}