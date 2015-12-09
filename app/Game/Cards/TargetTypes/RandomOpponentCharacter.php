<?php namespace App\Game\Cards\TargetTypes;

class RandomOpponentCharacter implements TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups) {
        $targets       = $board_target_groups->getOpponentMinionsWithHero();
        $keys          = array_keys($targets);
        $random_number = app('Random')->getFromRange(0, (count($keys) - 1));

        return [$targets[$keys[$random_number]]];
    }
}