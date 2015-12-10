<?php namespace App\Game\Cards\TargetTypes;

use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Minion;

class ProvidedEnemyMinion implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        /** @var Minion $target */
        $target = current($board_target_groups->getProvidedTargets());
        if (!array_get($board_target_groups->getOpponentMinions(), $target->getId())) {
            throw new InvalidTargetException('Target must belong to opponent');
        }
        return [$target];
    }
}