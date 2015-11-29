<?php namespace App\Game\Sequences\Phases;

use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Card;
use App\Game\Cards\Mechanics;
use App\Game\Cards\Minion;

class Battlecry extends CardPhase
{
    public $phase_name = 'battlecry';

    public function queue(Card $minion, array $targets = []) {

        if (!$minion->hasMechanic(Mechanics::$BATTLECRY)) {
            return;
        }

        /** @var Minion $target */
        foreach ($targets as $target) {
            if ($target->hasMechanic(Mechanics::$STEALTH)) {
                throw new InvalidTargetException('Cannot silence stealth minion');
            }
        }

        $this->card    = $minion;
        $this->targets = $targets;

        app('TriggerQueue')->queue($this);
    }
}