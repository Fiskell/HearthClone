<?php namespace App\Game\Sequences;

use App\Game\Cards\Minion;

class CombatSequence extends AbstractSequence
{
    public function resolve(Minion $attacker, Minion $target) {
        $player = $attacker->getOwner();

        /* Preparation Phase */
//        $attacker->resolvePreparationPhase($target);

        /* Check Win/Loss/Draw */
        $player->getGame()->checkForGameOver();

        /* Combat Phase */
        $attacker->resolveCombatPhase($target);


        App('AuraHealth')->queueAllForPlayer($attacker->getOwner());
        App('TriggerQueue')->resolveQueue();

        /* Check Win/Loss/Draw */
        $player->getGame()->resolveDeaths();

        App('AuraOther')->queueAllForPlayer($attacker->getOwner());
        App('TriggerQueue')->resolveQueue();

        /* Check Win/Loss/Draw */
        $player->getGame()->checkForGameOver();
    }
}