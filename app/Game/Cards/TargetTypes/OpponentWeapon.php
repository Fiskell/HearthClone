<?php namespace App\Game\Cards\TargetTypes;

class OpponentWeapon implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        return [$board_target_groups->getOpponent()->getHero()->getWeapon()];
    }
}