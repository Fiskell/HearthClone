<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 2:13 PM
 */

namespace App\Game\Sequences;

use App\Events\SpellTextPhaseEvent;
use App\Game\Cards\Minion;
use App\Models\TriggerQueue;

class PlaySpellSequence extends CardSequence
{
    public function resolve(Minion $card, array $targets = []) {
        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');

        /* On Play Phase */

        /* Dragonkin Sorcerer Phase */

        /* Spellbender Phase */

        /* Spell Text Phase */
        event(new SpellTextPhaseEvent($card, $targets));
        $trigger_queue->resolveQueue();

        /* After Spell Phase */


        /* Check Game Over*/
    }
}