<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 9:52 PM
 */

namespace App\Models;


use App\Exceptions\InvalidTargetException;
use Exceptions\UndefinedBattleCryMechanicException;
use Illuminate\Support\Facades\App;

class Player
{
    /** @var  int $player_id */
    protected $player_id;

    /** @var  Card[] $minions_in_play */
    protected $minions_in_play = [];

    /** @var  Card[] $graveyard */
    protected $graveyard = [];

    /** @var array $active_mechanics */
    protected $active_mechanics = [];

    /** @var int $spell_power_modifier */
    protected $spell_power_modifier = 0;

    /** @var int $hand_size */
    protected $hand_size = 0;

    /**
     * @param Player $attacking_player
     * @return Player
     */
    public static function getDefendingPlayer(Player $attacking_player) {
        if ($attacking_player->getPlayerId() == 1) {
            return app('Player2');
        }

        return app('Player1');
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
        $this->minions_in_play[$card->getId()] = $card;
        $this->active_mechanics                = array_merge($this->active_mechanics, $card->getMechanics());

        if ($card->hasMechanic(Mechanics::$BATTLECRY)) {
            $this->resolveBattlecry($card, $targets);
        }

        if ($card->hasMechanic(Mechanics::$SPELL_POWER)) {
            $this->recalculateSpellPower();
        }
    }

    /**
     * @return Card[]
     */
    public function getMinionsInPlay() {
        return $this->minions_in_play;
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
        $this->addToGraveyard($this->minions_in_play[$card_id]);
        unset($this->minions_in_play[$card_id]);
    }

    /**
     * Recalculate our cache of current board mechanics
     */
    public function recalculateActiveMechanics() {
        $this->active_mechanics = [];
        foreach ($this->minions_in_play as $minion) {
            $this->active_mechanics = array_merge($this->active_mechanics, $minion->getMechanics());
        }
    }

    /**
     * @param Card $card
     * @param array $targets
     * @throws InvalidTargetException
     * @throws UndefinedBattleCryMechanicException
     */
    private function resolveBattlecry(Card $card, array $targets) {
        $card_sub_mechanics      = $card->getSubMechanics();
        $card_battlecry_mechanic = array_get($card_sub_mechanics, Mechanics::$BATTLECRY . '.0');

        if (is_null($card_battlecry_mechanic)) {
            throw new UndefinedBattleCryMechanicException('No battle cry mechanic specified');
        }

        if (is_null($card_sub_mechanics)) {
            return;
        }

        switch ($card_battlecry_mechanic) {
            case Mechanics::$SILENCE:
                if (count($targets) > 1) {
                    throw new InvalidTargetException('Silence can only target one minion');
                }

                /** @var Card $target */
                $target = current($targets);

                if ($target->hasMechanic(Mechanics::$STEALTH)) {
                    throw new InvalidTargetException('Cannot silence stealth minion');
                }

                $target->removeAllMechanics();

                break;
        }
    }

    /**
     * Pass the turn to the other player and resolve any end of turn effects.
     */
    public function passTurn() {
        foreach ($this->minions_in_play as $minion) {
            $minion->wakeUp();
            $minion->thaw();
        }

        /** @var Game $game */
        $game = app('Game');
        $game->toggleActivePlayer();
    }

    /**
     * Recalculates the spell damage modifier based on the board
     */
    private function recalculateSpellPower() {
        $this->spell_power_modifier = 0;
        foreach ($this->minions_in_play as $minion) {
            if ($minion->hasMechanic(Mechanics::$SPELL_POWER)) {
                $this->spell_power_modifier++;
            }
        }
    }

    /**
     * @return int
     */
    public function getSpellPowerModifier() {
        return $this->spell_power_modifier;
    }

    public function getHandSize() {
        return $this->hand_size;
    }

    public function drawCard() {
        $this->hand_size++;
    }

}