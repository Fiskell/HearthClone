<?php namespace App\Game\Sequences\Phases;

use App\Game\Cards\Card;

class SubCardPhase extends CardPhase
{
    public function setPhaseName($phase_name) {
        $this->phase_name = $phase_name;
    }
    public function setCard($card) {
        $this->card = $card;
    }

    public function queue(Card $card, array $targets = []) {
        $this->card    = $card;
        $this->targets = $targets;
        App('TriggerQueue')->queue($this);
    }
}