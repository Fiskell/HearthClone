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
    /** @var  int $player_id */
    protected $player_id;

    /** @var  Card[] $creatures_in_play */
    protected $creatures_in_play;

    /** @var  Card[] $graveyard */
    protected $graveyard;

    protected $active_mechanics = [];

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

    /**
     * @return Card[]
     */
    public function getGraveyard()
    {
        return $this->graveyard;
    }

    /**
     * @param Card $dead_card
     */
    public function addToGraveyard(Card $dead_card) {
        $this->graveyard[] = $dead_card;
    }

    public function play(Card $card) {
        $card->setOwner($this);
        $this->creatures_in_play[$card->getId()] = $card;
        $this->active_mechanics = array_merge($this->active_mechanics, $card->getMechanics());
    }

    /**
     * @return Card[]
     */
    public function getCreaturesInPlay()
    {
        return $this->creatures_in_play;
    }

    public function hasMechanic($_mechanic) {
        return array_search($_mechanic, $this->active_mechanics) !== false;
    }

    /**
     * Remove the card from the board.
     *
     * @param $card_id
     */
    public function removeFromBoard($card_id)
    {
        $this->addToGraveyard($this->creatures_in_play[$card_id]);
        unset($this->creatures_in_play[$card_id]);
    }

    /**
     * Recalculate our cache of current board mechanics
     */
    public function recalculateActiveMechanics()
    {
        $this->active_mechanics = [];
        foreach($this->creatures_in_play as $creature) {
            $this->active_mechanics = array_merge($this->active_mechanics, $creature->getMechanics());
        }
    }

}