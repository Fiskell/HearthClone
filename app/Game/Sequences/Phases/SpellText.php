<?php namespace App\Game\Sequences\Phases;

use App\Game\Cards\Card;
use App\Game\Cards\Minion;

class SpellText extends CardPhase
{
    public $phase_name = 'spell_text_phase';

    public function queue(Card $card, array $targets = []) {
        /** @var Minion $card */
        $this->card    = $card;
        $this->targets = $targets;

        App('TriggerQueue')->queue($this);
    }
}