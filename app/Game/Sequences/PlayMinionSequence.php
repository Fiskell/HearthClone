<?php namespace App\Game\Sequences;

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

        /* Initial Aura Queue */
        App('AuraHealth')->queue($card, $targets);
        App('AuraOther')->queue($card, $targets);
        $trigger_queue->resolveQueue();

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
        /** @var Minion $card */

        $player = $card->getOwner();

        /* Overload */
        if (array_get($card->getTrigger(), TriggerTypes::$OVERLOAD)) {
            $this->resolveSubPhase($card, $targets, TriggerTypes::$OVERLOAD);
        }

        /* Spell Power */
        if ($card->hasMechanic(Mechanics::$SPELL_POWER)) {
            $player->recalculateSpellPower();
        }

        /* Choose One */
        if (array_get($card->getTrigger(), TriggerTypes::$CHOOSE_ONE)) {
            $this->resolveSubPhase($card, $targets, TriggerTypes::$CHOOSE_ONE);
        }

        /* Combo */
        if ($card->hasMechanic(Mechanics::$COMBO) && $player->getCardsPlayedThisTurn() > 0) {
            $card->resolveCombo($targets);
        }
    }

}