<?php namespace App\Game\Cards\TargetTypes;

use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Minion;

class AllMinions implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        return $board_target_groups->getPlayerMinions() + $board_target_groups->getOpponentMinions();
    }
}