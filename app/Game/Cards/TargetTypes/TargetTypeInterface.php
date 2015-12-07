<?php namespace App\Game\Cards\TargetTypes;

interface TargetTypeInterface
{
    public function getTargets(BoardTargetGroups $board_target_groups);
}