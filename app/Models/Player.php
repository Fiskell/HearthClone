<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 9:52 PM
 */

namespace App\Models;


use App\Exceptions\InvalidTargetException;
use Illuminate\Support\Facades\App;

class Player
{
    /** @var  int $player_id */
    protected $player_id;

    /** @var  Card[] $creatures_in_play */
    protected $creatures_in_play = [];

    /** @var  Card[] $graveyard */
    protected $graveyard = [];

    /** @var array $active_mechanics */
    protected $active_mechanics = [];

    /**
     * @param Player $attacking_player
     * @return Player
     */
    public static function getDefendingPlayer(Player $attacking_player) {
        if ($attacking_player->getPlayerId() == 1) {
            return App::make('Player2');
        }

        return App::make('Player1');
    }

    /**
     * @return mixed
     */
    public function getPlayerId() {
        return $this->player_id;
    }

    /**
     * @param mixed $player_id
     */
    public function setPlayerId($player_id) {
        $this->player_id = $player_id;
    }

    /**
     * @return Card[]
     */
    public function getGraveyard() {
        return $this->graveyard;
    }

    /**
     * @param Card $dead_card
     */
    public function addToGraveyard(Card $dead_card) {
        $this->graveyard[] = $dead_card;
    }

    /**
     * Add a card to the board.
     *
     * @param Card $card
     * @param Card[] $targets
     * @throws InvalidTargetException
     */
    public function play(Card $card, array $targets = []) {
        $card->setOwner($this);
        $this->creatures_in_play[$card->getId()] = $card;
        $this->active_mechanics                  = array_merge($this->active_mechanics, $card->getMechanics());

        if ($card->hasMechanic(Mechanics::$BATTLECRY)) {
            $this->resolveBattlecry($card, $targets);
        }
    }

    /**
     * @return Card[]
     */
    public function getCreaturesInPlay() {
        return $this->creatures_in_play;
    }

    /**
     * @param $_mechanic
     * @return bool
     */
    public function hasMechanic($_mechanic) {
        return array_search($_mechanic, $this->active_mechanics) !== false;
    }

    /**
     * Remove the card from the board.
     *
     * @param $card_id
     */
    public function removeFromBoard($card_id) {
        $this->addToGraveyard($this->creatures_in_play[$card_id]);
        unset($this->creatures_in_play[$card_id]);
    }

    /**
     * Recalculate our cache of current board mechanics
     */
    public function recalculateActiveMechanics() {
        $this->active_mechanics = [];
        foreach ($this->creatures_in_play as $creature) {
            $this->active_mechanics = array_merge($this->active_mechanics, $creature->getMechanics());
        }
    }

    /**
     * @param Card $card
     * @param array $targets
     * @throws InvalidTargetException
     */
    private function resolveBattlecry(Card $card, array $targets) {
        $card_sub_mechanics      = $card->getSubMechanics();
        $card_battlecry_mechanic = array_get($card_sub_mechanics, Mechanics::$BATTLECRY . '.0');

        if (is_null($card_sub_mechanics)) {
            return;
        }

        switch ($card_battlecry_mechanic) {
            case Mechanics::$SILENCE:
                if (count($targets) > 1) {
                    throw new InvalidTargetException('Silence can only target one creature');
                }

                /** @var Card $target */
                $target = current($targets);
                $target->removeAllMechanics();

                break;
        }
    }

    /**
     * Pass the turn to the other player and resolve any end of turn effects.
     */
    public function passTurn() {
        foreach($this->creatures_in_play as $creature) {
            $creature->wakeUp();
        }

        /** @var Game $game */
        $game = app('Game');
        $game->toggleActivePlayer();
    }

}