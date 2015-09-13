<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 2:11 PM
 */

namespace App\Game\Sequences;

use App\Events\OnPlayPhaseEvent;
use App\Exceptions\BattlefieldFullException;
use App\Exceptions\InvalidTargetException;
use App\Exceptions\NotEnoughManaCrystalsException;
use App\Game\Cards\Card;
use App\Game\Cards\Mechanics;
use App\Game\Cards\Minion;
use App\Game\Cards\Triggers\TriggerTypes;
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
        $this->resolveOnPlayPhase($card, $targets);

        /* Late On Summon Phase */
        // todo

        /* Battlecry Phase */
        App('Battlecry')->queue($card, $targets);
        $trigger_queue->resolveQueue();

        /* Secret Activation Phase */
        // todo

        /* After Summon Phase */

        App('AfterSummon')->queue($card, $targets);
        $trigger_queue->resolveQueue();

        /* Check Game Over Phase */
        // todo
    }

    private function resolveOnPlayPhase(Card $card, $targets) {
        $player = $card->getOwner();

        /** @var Minion $card */
        if ($card->hasMechanic(Mechanics::$OVERLOAD)) {

            $player->addLockedManaCrystalCount($card->getOverloadValue());
        }

        if ($card->hasMechanic(Mechanics::$SPELL_POWER)) {
            $player->recalculateSpellPower();
        }

        if (array_get($card->getTrigger(), TriggerTypes::$CHOOSE_ONE)) {
            $card->resolveChoose($targets);
        }

        if ($card->hasMechanic(Mechanics::$COMBO) && $player->getCardsPlayedThisTurn() > 0) {
            $card->resolveCombo($targets);
        }
    }
}