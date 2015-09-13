<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 7:24 PM
 */

namespace App\Game\Sequences\Phases;

use App\Game\Cards\Mechanics;
use App\Game\Cards\Minion;

class DeathProcessing extends CardPhase
{
    function queue(Minion $minion, array $targets = []) {
        $this->card    = $minion;
        $this->targets = $targets;
        App('TriggerQueue')->queue($this);
    }

    public function resolve() {

        if (!$this->card->hasMechanic(Mechanics::$DEATHRATTLE)) {
            return;
        }

        switch ($this->card->getName()) {
            case 'Loot Hoarder':
                $this->card->getOwner()->drawCard();
        }
    }
}