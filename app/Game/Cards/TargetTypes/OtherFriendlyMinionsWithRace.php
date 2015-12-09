<?php namespace App\Game\Cards\TargetTypes;

use App\Game\Cards\Minion;

class OtherFriendlyMinionsWithRace implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        $player_minions = $board_target_groups->getPlayerMinions();
        unset($player_minions[$board_target_groups->getTriggerCard()->getId()]);
        $targets = [];
        /** @var Minion $player_minion */
        foreach ($player_minions as $player_minion) {
            if ($player_minion->getRace() == $board_target_groups->getTargetRace()) {
                $targets[] = $player_minion;
            }
        }
        return $targets;
    }
}