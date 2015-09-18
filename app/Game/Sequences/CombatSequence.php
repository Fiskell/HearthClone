<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 2:20 PM
 */

namespace App\Game\Sequences;

use App\Game\Cards\Minion;

class CombatSequence extends AbstractSequence
{
    public function resolve(Minion $attacker, Minion $target) {
        $player = $attacker->getOwner();

        /* Preparation Phase */
        $attacker->resolvePreparationPhase($target);

        /* Check Win/Loss/Draw */
        $player->getGame()->checkForGameOver();

        /* Combat Phase */
        $attacker->resolveCombatPhase($target);


        App('AuraHealth')->queue($attacker, []);
        App('TriggerQueue')->resolveQueue();

        /* Check Win/Loss/Draw */
        $player->getGame()->resolveDeaths();

        App('AuraOther')->queue($attacker, []);
        App('TriggerQueue')->resolveQueue();

        /* Check Win/Loss/Draw */
        $player->getGame()->checkForGameOver();
    }
}