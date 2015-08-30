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

    public function __construct(Player $player1, Player $player2) {
        $this->player1 = $player1;
        $this->player1->setPlayerId(1);
        App::instance('Player1', $this->getPlayer1());

        $this->player2 = $player2;
        $this->player2->setPlayerId(2);
        App::instance('Player2', $this->getPlayer2());

        //TODO for now hard coding player 1 as default active player
        $this->active_player    = $this->player1;
        $this->defending_player = $this->player2;
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

}