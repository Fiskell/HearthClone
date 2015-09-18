<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/26/15
 * Time: 1:28 AM
 */

namespace App\Game;

use App\Game\Cards\Minion;
use App\Models\TriggerQueue;

class Game
{
    protected $player1;
    protected $player2;

    /** @var  Player $active_player */
    protected $active_player;

    /** @var  Player $defending_player */
    protected $defending_player;

    protected $active_game = true;

    /** @var Player $winning_player */
    protected $winning_player;

    /** @var Player $losing_player */
    protected $losing_player;

    /** @var  int $cards_played_this_game */
    protected $cards_played_this_game;

    public function __construct(Player $player1, Player $player2) {
        $this->player1 = $player1;
        $this->player1->setPlayerId(1);
        $this->player1->setGame($this);
        $app = app();
        $app->instance('Player1', $this->getPlayer1());

        $this->player2 = $player2;
        $this->player2->setPlayerId(2);
        $this->player2->setGame($this);
        $app->instance('Player2', $this->getPlayer2());

        //TODO for now hard coding player 1 as default active player
        $this->active_player    = $this->player1;
        $this->defending_player = $this->player2;
    }

    public function init(Deck $player1, Deck $player2) {
        /** @var Game $game */
        $this->getPlayer1()->setDeck($player1);
        $this->getPlayer2()->setDeck($player2);

        App('TurnSequence')->resolveTurnOne($this->getPlayer1());
    }

    /**
     * @return Player
     */
    public function getPlayer1() {
        return $this->player1;
    }

    /**
     * @param Player $player1
     */
    public function setPlayer1(Player $player1) {
        $this->player1 = $player1;
    }

    /**
     * @return Player
     */
    public function getPlayer2() {
        return $this->player2;
    }

    /**
     * @param Player $player2
     */
    public function setPlayer2(Player $player2) {
        $this->player2 = $player2;
    }

    /**
     * @return Player
     */
    public function getActivePlayer() {
        return $this->active_player;
    }

    public function getDefendingPlayer() {
        return $this->defending_player;
    }

    /**
     * Change active and defending players.
     */
    public function toggleActivePlayer() {
        $old_active_player      = $this->active_player;
        $this->active_player    = $this->getDefendingPlayer();
        $this->defending_player = $old_active_player;
    }

    /**
     * End the game and assign the winner and loser.
     * @param Player $winning_player
     */
    public function gameOver(Player $winning_player) {
        $this->winning_player = $winning_player;
        $this->losing_player  = $winning_player->getOtherPlayer();
    }

    /**
     * @return bool
     */
    public function isOver() {
        return $this->active_game;
    }

    /**
     * @return Player
     */
    public function getWinningPlayer() {
        return $this->winning_player;
    }

    /**
     * @return Player
     */
    public function getLosingPlayer() {
        return $this->losing_player;
    }

    /**
     * Find the winning player and end the game
     * @param Player $losing_player
     */
    public function setLoser(Player $losing_player) {
        /** @var Player $winning_player */
        $winning_player = $losing_player->getOtherPlayer();

        $this->gameOver($winning_player);
    }

    /**
     * Set the winning player and end the game.
     * @param Player $winning_player
     */
    public function setWinner(Player $winning_player) {
        $this->gameOver($winning_player);
    }

    /**
     * Increment the value storing the number of cards played throughout the game.
     */
    public function incrementCardsPlayedThisGame() {
        $this->cards_played_this_game++;
    }

    /**
     * Get number of cards played this game.
     * @return int
     */
    public function getCardsPlayedThisGame() {
        return $this->cards_played_this_game;
    }

    /**
     * Phase which checks if the game is over, and assigns winner/losers/draw accordingly
     */
    public function checkForGameOver() {
        // TODO need to account for draw
        if ($this->player1->getHero()->getHealth() <= 0) {
            $this->gameOver($this->player2);

            return true;
        }

        if ($this->player2->getHero()->getHealth() <= 0) {
            $this->gameOver($this->player1);

            return true;
        }

        return false;
    }

    /**
     * Phase which resolves the deaths that may have resulted from damage/triggers.
     */
    public function resolveDeaths() {
        $this->resolveDeathsForPlayer($this->player1);
        $this->resolveDeathsForPlayer($this->player2);

        App('TriggerQueue')->resolveQueue();
    }

    /**
     * Recalculate minion positions since minions may have been
     * removed from the board recently.
     *
     * @param Player $player
     */
    private function recalculateMinionPositions(Player $player) {
        $minion_positions = [];

        $player_minions = $player->getMinionsInPlay();

        /** @var Minion $player1_minion */

        foreach ($player_minions as $player_minion) {
            $minion_positions[$player_minion->getPosition()] = $player_minion;
        }

        ksort($minion_positions);

        $count = 1;
        /** @var Minion $minion */
        foreach ($minion_positions as $minion) {
            $minion->setPosition($count);
            $count++;
        }
    }

    /**
     * Resolve the deaths for a specific player.
     *
     * @param Player $player
     * @return bool
     */
    private function resolveDeathsForPlayer(Player $player) {
        $recalculate_minion_positions = false;

        $player_minions = $player->getMinionsInPlay();

        /* Mark minions as killed and remove from board */
        /** @var Minion $player1_minion */
        foreach ($player_minions as $player_minion) {
            if ($player_minion->getHealth() > 0) {
                continue;
            }

            $player_minion->setHealth(0);
            $player_minion->killed();
            $recalculate_minion_positions = true;
        }

        /* If minions were killed we need to update positions */
        if ($recalculate_minion_positions) {
            $this->recalculateMinionPositions($player);
        }

        $player->recalculateActiveMechanics();
    }

}