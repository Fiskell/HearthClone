<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/26/15
 * Time: 1:28 AM
 */

namespace App\Models;

use Illuminate\Support\Facades\App;

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

    public function __construct(Player $player1, Player $player2) {
        $this->player1 = $player1;
        $this->player1->setPlayerId(1);
        App::instance('Player1', $this->getPlayer1());

        $this->player2 = $player2;
        $this->player2->setPlayerId(2);
        App::instance('Player2', $this->getPlayer2());

        //TODO for now hard coding player 1 as default active player
        $this->active_player    = $this->player1;
        $this->active_player->startTurn();

        $this->defending_player = $this->player2;
    }

    public function init(Deck $player1, Deck $player2) {
        /** @var Game $game */
        $game = app('Game');
        $game->getPlayer1()->setDeck($player1);
        $game->getPlayer2()->setDeck($player2);
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
        $this->losing_player  = app('Player1');
        if($winning_player->getPlayerId() == 1) {
            $this->losing_player  = app('Player2');
        }
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
        $winning_player = app('Player1');
        if($losing_player->getPlayerId() == 1) {
            $winning_player = app('Player2');
        }

        $this->gameOver($winning_player);
    }

    /**
     * Set the winning player and end the game.
     * @param Player $winning_player
     */
    public function setWinner(Player $winning_player) {
        $this->gameOver($winning_player);
    }

}