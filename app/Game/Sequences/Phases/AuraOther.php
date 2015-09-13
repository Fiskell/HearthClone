<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 6:25 PM
 */

namespace App\Game\Sequences\Phases;

use App\Game\Cards\Minion;

class AuraOther extends CardPhase
{
    public $phase_name = 'aura_other';

    public function queue(Minion $minion, array $targets = []) {
        // todo loop over all cards, clear out auras.

        // todo loop over all cards with aura and add to trigger queue.

        // todo resolve should do the calculation

        if(!array_get($minion->getTrigger(), 'aura')) {
            return;
        }

        $this->card    = $minion;
        $this->targets = $targets;

        App('TriggerQueue')->queue($this);
    }

    public function resolve() {
        $this->recalculateAura();
    }
}