<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 9:52 PM
 */

namespace App\Models;

use App\Exceptions\InvalidTargetException;
use App\Exceptions\NotEnoughManaCrystalsException;
use Exceptions\UndefinedBattleCryMechanicException;

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

    /** @var int $cards_played_this_turn */
    protected $cards_played_this_turn = 0;

    /** @var int $mana_crystal_count */
    protected $mana_crystal_count = 0;

    /** @var int $locked_mana_crystal_count */
    protected $locked_mana_crystal_count = 0;

    /** @var int $mana_crystals_used_this_turn */
    protected $mana_crystals_used_this_turn = 0;

    /** @var  Deck $deck */
    protected $deck;

    /** @var  AbstractHero $hero */
    protected $hero;

    /** @var bool $alive */
    protected $alive = true;

    public function isAlive() {
        return $this->alive;
    }

    /**
     * Kill the player, game over!
     */
    public function killed() {
        $this->alive = false;

        /** @var Game $game */
        $game = app('Game');
        $game->setLoser($this);
    }

    /**
     * @return Player
     */
    public function getOtherPlayer() {
        if ($this->getPlayerId() == 1) {
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
     * @param null $choose_mechanic
     * @throws InvalidTargetException
     * @throws NotEnoughManaCrystalsException
     * @throws UndefinedBattleCryMechanicException
     */
    public function play(Card $card, array $targets = [], $choose_mechanic = null) {

        $remaining_mana_crystals = $this->getManaCrystalCount() - $this->getManaCrystalsUsed();
        if (($remaining_mana_crystals - $card->getCost()) < 0) {
            throw new NotEnoughManaCrystalsException('Cost of ' . $card->getName() . ' is ' . $card->getCost() . ' you have ' . $remaining_mana_crystals);
        }

        $card->setOwner($this);
        $this->minions_in_play[$card->getId()] = $card;
        $this->active_mechanics                = array_merge($this->active_mechanics, $card->getMechanics());

        $this->setManaCrystalsUsed($this->getManaCrystalsUsed() + $card->getCost());

        if ($card->hasMechanic(Mechanics::$OVERLOAD)) {
            // TODO I hate this
            $this->addLockedManaCrystalCount($card->getOverloadValue());
        }

        if ($card->hasMechanic(Mechanics::$SPELL_POWER)) {
            $this->recalculateSpellPower();
        }

        if ($card->hasMechanic(Mechanics::$CHOOSE)) {
            $card->resolveChoose($targets, $choose_mechanic);
        }

        if ($card->hasMechanic(Mechanics::$BATTLECRY)) {
            $card->resolveBattlecry($targets);
        }

        if ($card->hasMechanic(Mechanics::$COMBO) && $this->getCardsPlayedThisTurn() > 0) {
            $card->resolveCombo($targets);
        }

        $this->incrementCardsPlayedThisTurn();

        /** @var Game $game */
        $game = app('Game');
        $game->incrementCardsPlayedThisGame();

        $card->setPlayOrderId($game->getCardsPlayedThisGame());
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
     * Pass the turn to the other player and resolve any end of turn effects.
     */
    public function passTurn() {
        $game = app('Game');

        $this->updateBoardStates();

        $this->resetTurnCounters();

        /** @var Game $game */
        $game->toggleActivePlayer();

        $game->getActivePlayer()->startTurn();
    }

    /**
     * Start of a players turn
     */
    public function startTurn() {
        $this->incrementManaCrystalCount();
        $this->resetManaCrystalsUsed();
        $this->setManaCrystalsUsed($this->getLockedManaCrystalCount());
        $this->resetLockedManaCrystalCount();
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

    /**
     * @return int
     */
    public function getCardsPlayedThisTurn() {
        return $this->cards_played_this_turn;
    }

    /**
     * Reset Cards Played this turn
     */
    public function resetCardsPlayedThisTurn() {
        $this->cards_played_this_turn = 0;
    }

    /**
     */
    public function incrementCardsPlayedThisTurn() {
        $this->cards_played_this_turn++;
    }

    /**
     * @return int
     */
    public function getManaCrystalCount() {
        return $this->mana_crystal_count;
    }

    /**
     * @param int $mana_crystal_count
     */
    public function setManaCrystalCount($mana_crystal_count) {
        $this->mana_crystal_count = $mana_crystal_count;
    }

    /**
     * Add one mana crystal
     */
    public function incrementManaCrystalCount() {
        $this->mana_crystal_count++;
    }

    /**
     * @return int
     */
    public function getManaCrystalsUsed() {
        return $this->mana_crystals_used_this_turn;
    }

    /**
     * @param int $mana_crystals_used_this_turn
     */
    public function setManaCrystalsUsed($mana_crystals_used_this_turn) {
        $this->mana_crystals_used_this_turn = $mana_crystals_used_this_turn;
    }

    /**
     * Update minions and character states
     */
    private function updateBoardStates() {
        foreach ($this->minions_in_play as $minion) {
            $minion->wakeUp();
            $minion->thaw();
            $minion->resetTimesAttackedThisTurn();
        }
    }

    /**
     * Reset the turn counters
     */
    private function resetTurnCounters() {
        $this->resetCardsPlayedThisTurn();
    }

    /**
     * Reset mana crystals to 0
     */
    private function resetManaCrystalsUsed() {
        $this->setManaCrystalsUsed(0);
    }

    /**
     * @return int
     */
    public function getLockedManaCrystalCount() {
        return $this->locked_mana_crystal_count;
    }

    /**
     * @param int $add_locked_mana_crystals
     */
    public function addLockedManaCrystalCount($add_locked_mana_crystals) {
        $this->locked_mana_crystal_count += $add_locked_mana_crystals;
    }

    /**
     * Reset number of locked mana crystals to 0
     */
    public function resetLockedManaCrystalCount() {
        $this->locked_mana_crystal_count = 0;
    }

    /**
     * @return Deck
     */
    public function getDeck() {
        return $this->deck;
    }

    /**
     * @param Deck $deck
     */
    public function setDeck(Deck $deck) {
        $this->deck = $deck;
        $this->hero = $deck->getHero();
    }

    /**
     * @return AbstractHero
     */
    public function getHero() {
        return $this->hero;
    }

    /**
     * @param array $targets
     */
    public function useAbility($targets = []) {
        $defending_player = $this->getOtherPlayer($this);
        $this->hero->useAbility($this, $defending_player, $targets);
        if (!$defending_player->getHero()->isAlive()) {
            $defending_player->killed();
        }

        if (!$this->getHero()->isAlive()) {
            $this->killed();
        }
    }

}