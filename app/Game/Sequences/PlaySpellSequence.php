<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 2:13 PM
 */

namespace App\Game\Sequences;

use App\Game\Cards\Card;
use App\Models\TriggerQueue;

class PlaySpellSequence extends CardSequence
{
    public function resolve(Card $card, array $targets = []) {
        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');

        /* On Play Phase */

        /* Dragonkin Sorcerer Phase */

        /* Spellbender Phase */

        /* Spell Text Phase */
        App('SpellText')->queue($card, $targets);
        $trigger_queue->resolveQueue();

        /* After Spell Phase */


        /* Check Game Over*/
    }
}