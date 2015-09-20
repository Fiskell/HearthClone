<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 2:17 PM
 */

namespace App\Game\Sequences;

use App\Game\Player;

class TurnSequence extends AbstractSequence
{
    /** @var Player $player */
    private $player;
    
    public function resolve(Player $player) {
        $this->player = $player;

        App('EndOfTurn')->queueAllForPlayer($player);
        App('TriggerQueue')->resolveQueue();

        $this->updateBoardStates();

        $this->resetTurnCounters();

        $player->getGame()->toggleActivePlayer();

        /* Switch active player */
        $this->player = $this->player->getOtherPlayer();

        $this->startTurn();
    }

    public function resolveTurnOne(Player $player) {
        $this->player = $player;
        $this->startTurn();
    }

    /**
     * Start of a players turn
     */
    private function startTurn() {
        $this->player->incrementManaCrystalCount();
        $this->player->resetManaCrystalsUsed();
        $this->player->setManaCrystalsUsed($this->player->getLockedManaCrystalCount());
        $this->player->resetLockedManaCrystalCount();
        $this->player->getHero()->resetHeroPower();
    }

    /**
     * Update minions and character states
     */
    private function updateBoardStates() {
        $minions_in_play = $this->player->getMinionsInPlay();
        foreach ($minions_in_play as $minion) {
            $minion->wakeUp();
            $minion->thaw();
            $minion->resetTimesAttackedThisTurn();
        }
    }

    /**
     * Reset the turn counters
     */
    private function resetTurnCounters() {
        $this->player->resetCardsPlayedThisTurn();
    }
}