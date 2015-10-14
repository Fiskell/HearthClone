<?php namespace App\Game;

use App\Exceptions\BattlefieldFullException;
use App\Exceptions\InvalidTargetException;
use App\Exceptions\NotEnoughManaCrystalsException;
use App\Game\Cards\Card;
use App\Game\Cards\Heroes\AbstractHero;
use App\Game\Cards\Minion;

class Player
{
    /** @var  Game $game */
    protected $game;

    /* Zones */

    public static $MAX_MINIONS = 7;

    /** @var  Deck $deck */
    protected $deck;

    /** @var  Card[] $graveyard */
    protected $graveyard = [];

    /** @var int $hand_size */
    protected $hand_size = 0;

    /** @var  AbstractHero $hero */
    protected $hero;

    /** @var int $mana_crystal_count */
    protected $mana_crystal_count = 0;

    /** @var  Minion[] $minions_in_play */
    protected $minions_in_play = [];

    /* Player Attributes */

    /** @var array $active_mechanics */
    protected $active_mechanics = [];

    /** @var bool $alive */
    protected $alive = true;

    /** @var  int $player_id */
    protected $player_id;

    /** @var int $spell_power_modifier */
    protected $spell_power_modifier = 0;

    /* Internal Counters */

    /** @var int $cards_played_this_turn */
    protected $cards_played_this_turn = 0;

    /** @var int $locked_mana_crystal_count */
    protected $locked_mana_crystal_count = 0;

    /** @var int $mana_crystals_used_this_turn */
    protected $mana_crystals_used_this_turn = 0;

    /* ------ Getters and Setters ------- */

    /**
     * @return Game
     */
    public function getGame() {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame($game) {
        $this->game = $game;
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
     * Return the number of cards in the players hand
     * @return int
     */
    public function getHandSize() {
        return $this->hand_size;
    }

    /**
     * @return AbstractHero
     */
    public function getHero() {
        return $this->hero;
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
     * @return Minion[]
     */
    public function getMinionsInPlay() {
        return $this->minions_in_play;
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
     * @param $_mechanic
     * @return bool
     */
    public function hasMechanic($_mechanic) {
        return array_search($_mechanic, $this->active_mechanics) !== false;
    }

    /**
     * @return bool
     */
    public function isAlive() {
        return $this->alive;
    }

    /**
     * Kill the player, game over!
     */
    public function killed() {
        $this->alive = false;
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
     * @return Player
     */
    public function getOtherPlayer() {
        if ($this->getPlayerId() == 1) {
            return app('Player2');
        }

        return app('Player1');
    }

    /**
     * Recalculates the spell damage modifier based on the board
     */
    public function recalculateSpellPower() {
        $this->spell_power_modifier = 0;
        foreach ($this->minions_in_play as $minion) {
            $this->spell_power_modifier += array_get($minion->getTrigger(), 'spellpower');
        }
        // todo does not recalculate opponent spell power.
    }

    /**
     * @return int
     */
    public function getSpellPowerModifier() {
        return $this->spell_power_modifier;
    }

    /**
     * @return int
     */
    public function getCardsPlayedThisTurn() {
        return $this->cards_played_this_turn;
    }

    /**
     * Increase the cards played this turn by one
     */
    public function incrementCardsPlayedThisTurn() {
        $this->cards_played_this_turn++;
    }

    /**
     * Reset Cards Played this turn
     */
    public function resetCardsPlayedThisTurn() {
        $this->cards_played_this_turn = 0;
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
     * Reset mana crystals to 0
     */
    public function resetManaCrystalsUsed() {
        $this->setManaCrystalsUsed(0);
    }


    /**
     * @param Card $card
     */
    public function enterBattlefield(Card $card) {
        $this->minions_in_play[$card->getId()] = $card;
    }

    /**
     * @return array
     */
    public function getActiveMechanics() {
        return $this->active_mechanics;
    }

    /**
     * @param array $active_mechanics
     */
    public function setActiveMechanics($active_mechanics) {
        $this->active_mechanics = $active_mechanics;
    }
    /* ---------------------------------- */


    /* ----- Player Action Sequences ----- */
    /**
     * Pass the turn to the other player and resolve any end of turn effects.
     */
    public function passTurn() {
        App('TurnSequence')->resolve($this);
    }

    /**
     * Add a card to the board.
     *
     * @param Card $card
     * @param Minion[] $targets
     * @param int $position
     */
    public function play(Card $card, array $targets = [], $position=3) {
        App('CardSequence')->play($card, $targets, $position);
    }

    /**
     * Player initiated attack sequence.
     *
     * @param Minion $attacker
     * @param Minion $target
     * @throws InvalidTargetException
     * @throws \App\Exceptions\MinionAlreadyAttackedException
     */
    public function attack(Minion $attacker, Minion $target) {
        App('CombatSequence')->resolve($attacker, $target);
    }

    /**
     * @param array $targets
     */
    public function useAbility($targets = []) {
        App('HeroPowerSequence')->resolve($this->getHero(), $targets);
    }
    /* ---------------------------------- */


    /* --------- Helper Functions --------*/
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
     * Draw a card.
     */
    public function drawCard() {
        $this->hand_size++;
        $this->getDeck()->draw();
    }

    /**
     * Discard a specified number of random cards.
     *
     * @param $quantity
     */
    public function discardRandom($quantity) {
        // todo will need to test more
        $this->hand_size -= $quantity;
        if($this->hand_size < 0) {
            $this->hand_size = 0;
        }
    }

    /**
     * Return all the minions on the board.
     *
     * @return \App\Game\Cards\Minion[]
     */
    public function getAllMinions() {
        $player_minions   = $this->getMinionsInPlay();
        $opponent_minions = $this->getOtherPlayer()->getMinionsInPlay();
        $all_minions      = $player_minions + $opponent_minions;

        return $all_minions;
    }

}