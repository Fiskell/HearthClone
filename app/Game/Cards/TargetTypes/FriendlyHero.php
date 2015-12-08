<?php namespace App\Game\Cards\TargetTypes;

class FriendlyHero implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        return [$board_target_groups->getPlayer()->getHero()];
    }
}