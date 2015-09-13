<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 6:58 PM
 */

namespace App\Game\Sequences\Phases;

use App\Game\Cards\Minion;

class SubCardPhase extends CardPhase
{
    public function setPhaseName($phase_name) {
        $this->phase_name = $phase_name;
    }

    function queue(Minion $minion, array $targets = []) {
        $this->card    = $minion;
        $this->targets = $targets;
        App('TriggerQueue')->queue($this);
    }
}