<?php namespace App\Game\Sequences\Phases;

use App\Game\Cards\Card;

class SpellText extends CardPhase
{
    public $phase_name = 'spell_text_phase';

    function queue(Card $card, array $targets = []) {
        $this->card    = $card;
        $this->targets = $targets;

        App('TriggerQueue')->queue($this);
    }
}