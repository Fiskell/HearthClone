<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 6:25 PM
 */

namespace App\Game\Sequences\Phases;

use App\Game\Cards\Minion;

class SpellText extends CardPhase
{
    public $phase_name = 'spell_text_phase';

    function queue(Minion $minion, array $targets = []) {
        $this->card    = $minion;
        $this->targets = $targets;

        App('TriggerQueue')->queue($this);
    }
}