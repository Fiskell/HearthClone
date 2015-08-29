<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 9:52 PM
 */

namespace App\Models;


use Illuminate\Support\Facades\App;

class Player
{
    protected $player_id;
    protected $creatures_in_play;
    protected $app;

    /**
     * @param Player $attacking_player
     * @return Player
     */
    public static function getDefendingPlayer(Player $attacking_player)
    {
        if($attacking_player->getPlayerId() == 1) {
            return App::make('Player2');
        }
        return App::make('Player1');
    }

    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->player_id;
    }

    /**
     * @param mixed $player_id
     */
    public function setPlayerId($player_id)
    {
        $this->player_id = $player_id;
    }

    public function play(Card $card) {
        $card->setOwner($this);
        $this->creatures_in_play[] = $card;
    }

    /**
     * @return Card[]
     */
    public function getCreaturesInPlay()
    {
        return $this->creatures_in_play;
    }
}