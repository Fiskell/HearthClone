<?php namespace App\Game\Sequences\Phases;

use App\Game\Player;

class EndOfTurn extends CardPhase
{
    public $phase_name = 'end_of_turn';

    public function queueAllForPlayer(Player $player) {
        $minions = $player->getMinionsInPlay();

        foreach ($minions as $single_minion) {
            if (!array_get($single_minion->getTrigger(), $this->phase_name)) {
                continue;
            }

            $tmp_end_of_turn          = App('EndOfTurn');
            $tmp_end_of_turn->card    = $single_minion;
            App('TriggerQueue')->queue($tmp_end_of_turn);
        }
    }

}