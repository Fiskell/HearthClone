<?php namespace App\Game\Cards\TargetTypes;

class OtherFriendlyMinions implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        $player_minions = $board_target_groups->getPlayerMinions();
        unset($player_minions[$board_target_groups->getTriggerCard()->getId()]);
        return $player_minions;
    }
}