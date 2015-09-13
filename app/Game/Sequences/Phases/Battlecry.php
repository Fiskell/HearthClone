<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 6:25 PM
 */

namespace App\Game\Sequences\Phases;

use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Mechanics;
use App\Game\Cards\Minion;
use App\Models\TriggerQueue;

class Battlecry extends CardPhase
{
    public $phase_name = 'battlecry';

    function queue(Minion $minion, array $targets = []) {

        if ($minion->hasMechanic(Mechanics::$BATTLECRY)) {

            /** @var Minion $target */
            foreach ($targets as $target) {
                if ($target->hasMechanic(Mechanics::$STEALTH)) {
                    throw new InvalidTargetException('Cannot silence stealth minion');
                }
            }

            $this->card = $minion;
            $this->targets = $targets;

            /** @var TriggerQueue $trigger_queue */
            $trigger_queue = app('TriggerQueue');
            $trigger_queue->queue($this);

        }
    }
}