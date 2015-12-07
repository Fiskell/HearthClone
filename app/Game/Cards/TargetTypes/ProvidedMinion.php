<?php namespace App\Game\Cards\TargetTypes;

use App\Exceptions\InvalidTargetException;

class ProvidedMinion implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        $targets = $board_target_groups->getProvidedTargets();
        $targets_alive = $board_target_groups->removeDeadMinions($targets);
        if (count($targets) != count($targets_alive)) {
            throw new InvalidTargetException();
        }
        return $targets;
    }
}