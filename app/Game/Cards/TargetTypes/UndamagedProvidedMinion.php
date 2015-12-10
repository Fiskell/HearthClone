<?php namespace App\Game\Cards\TargetTypes;

use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Minion;

class UndamagedProvidedMinion implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        /** @var Minion[] $targets */
        $targets = $board_target_groups->getProvidedTargets();

        foreach ($targets as $target) {
            if ($target->getHealth() < $target->getMaxHealth()) {
                throw new InvalidTargetException('Target must be undamaged');
            }
        }

        return $targets;
    }
}