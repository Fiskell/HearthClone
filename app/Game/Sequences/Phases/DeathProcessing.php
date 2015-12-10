<?php namespace App\Game\Sequences\Phases;

use App\Game\Cards\Card;
use App\Game\Cards\Mechanics;
use App\Game\Cards\Minion;

class DeathProcessing extends CardPhase
{
    public function queue(Card $minion, array $targets = []) {
        /** @var Minion $minion */
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