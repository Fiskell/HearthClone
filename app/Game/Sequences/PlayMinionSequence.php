<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 2:11 PM
 */

namespace App\Game\Sequences;

use App\Events\AfterSummonPhaseEvent;
use App\Events\BattlecryPhaseEvent;
use App\Events\OnPlayPhaseEvent;
use App\Exceptions\BattlefieldFullException;
use App\Exceptions\InvalidTargetException;
use App\Exceptions\NotEnoughManaCrystalsException;
use App\Game\Cards\Minion;
use App\Game\Player;
use App\Models\TriggerQueue;
use Exceptions\UndefinedBattleCryMechanicException;

class PlayMinionSequence extends SummonMinionSequence
{
    /**
     * Player initiated sequence of summoning a minion.
     * @param Minion $card
     * @param array $targets
     * @throws BattlefieldFullException
     * @throws InvalidTargetException
     * @throws NotEnoughManaCrystalsException
     * @throws UndefinedBattleCryMechanicException
     * @throws \App\Exceptions\DumbassDeveloperException
     */
    public function resolve(Minion $card, array $targets = []) {
        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');
        $player = $card->getOwner();

        $count_minions = count($player->getMinionsInPlay());
        if ($count_minions == Player::$MAX_MINIONS) {
            throw new BattlefieldFullException();
        }

        /* Remove from hand and enter battlefield */
        $player->enterBattlefield($card);

        $player->setActiveMechanics(array_merge($player->getActiveMechanics(), $card->getMechanics()));


        /* Early on Summon Phase */
        // todo

        /* On Play Phase */
        event(new OnPlayPhaseEvent($card, $targets));
        $trigger_queue->resolveQueue();

        /* Late On Summon Phase */
        // todo

        /* Battlecry Phase */
        event(new BattlecryPhaseEvent($card, $targets));
        $trigger_queue->resolveQueue();

        /* Secret Activation Phase */
        // todo

        /* After Summon Phase */
        event(new AfterSummonPhaseEvent($card, $targets));
        $trigger_queue->resolveQueue();

        /* Check Game Over Phase */
        // todo
    }
}