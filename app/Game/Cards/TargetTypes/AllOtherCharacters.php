<?php namespace App\Game\Cards\TargetTypes;

class AllOtherCharacters implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        $targets = $board_target_groups->getAllMinionsWithHeroes();
        unset($targets[$board_target_groups->getTriggerCard()->getId()]);
        return $targets;
    }
}