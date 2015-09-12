<?php namespace App\Game;

use App\Events\AfterSummonPhaseEvent;
use App\Events\BattlecryPhaseEvent;
use App\Events\OnPlayPhaseEvent;
use App\Events\SpellTextPhaseEvent;
use App\Exceptions\BattlefieldFullException;
use App\Exceptions\HeroPowerAlreadyFlippedException;
use App\Exceptions\InvalidTargetException;
use App\Exceptions\NotEnoughManaCrystalsException;
use App\Game\Cards\Card;
use App\Game\Cards\CardType;
use App\Game\Cards\Heroes\AbstractHero;
use App\Game\Cards\Mechanics;
use App\Game\Cards\Minion;
use App\Models\TriggerQueue;
use Exceptions\UndefinedBattleCryMechanicException;

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
    private function resetManaCrystalsUsed() {
        $this->setManaCrystalsUsed(0);
    }
    /* ---------------------------------- */


    /* ----- Player Action Sequences ----- */

    /**
     * Start of a players turn
     */
    public function startTurn() {
        $this->incrementManaCrystalCount();
        $this->resetManaCrystalsUsed();
        $this->setManaCrystalsUsed($this->getLockedManaCrystalCount());
        $this->resetLockedManaCrystalCount();
        $this->getHero()->resetHeroPower();
    }

    /**
     * Pass the turn to the other player and resolve any end of turn effects.
     */
    public function passTurn() {
        $this->updateBoardStates();

        $this->resetTurnCounters();

        $this->game->toggleActivePlayer();

        $this->game->getActivePlayer()->startTurn();
    }

    /**
     * Add a card to the board.
     *
     * @param Card $card
     * @param Minion[] $targets
     * @param null $choose_mechanic
     * @throws BattlefieldFullException
     * @throws NotEnoughManaCrystalsException
     */
    public function play(Card $card, array $targets = [], $choose_mechanic = null) {
        $remaining_mana_crystals = $this->getManaCrystalCount() - $this->getManaCrystalsUsed();
        if (($remaining_mana_crystals - $card->getCost()) < 0) {
            throw new NotEnoughManaCrystalsException('Cost of ' . $card->getName() . ' is ' . $card->getCost() . ' you have ' . $remaining_mana_crystals);
        }

        $this->setManaCrystalsUsed($this->getManaCrystalsUsed() + $card->getCost());

        switch ($card->getType()) {
            case CardType::$MINION:
                /** @var Minion $card */
                $this->playMinion($card, $targets, $choose_mechanic);
                break;
            case CardType::$SPELL:
                $this->playSpell($card, $targets, $choose_mechanic);
                break;
            case CardType::$WEAPON:
                $this->playWeapon($card, $targets);
                break;
        }

        $this->incrementCardsPlayedThisTurn();

        $this->game->resolveDeaths();
    }

    public function playSpell(Minion $card, array $targets = [], $choose_mechanic = null) {
        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');

        /* On Play Phase */

        /* Dragonkin Sorcerer Phase */

        /* Spellbender Phase */

        /* Spell Text Phase */
        event(new SpellTextPhaseEvent($card, $targets));
        $trigger_queue->resolveQueue();

        /* After Spell Phase */


        /* Check Game Over*/
    }

    public function playWeapon(Card $card, array $targets = []) {
        $this->getHero()->equipWeapon($card, $targets);
    }

    /**
     * Player initiated sequence of summoning a minion.
     * @param Minion $card
     * @param array $targets
     * @param null $choose_mechanic
     * @throws BattlefieldFullException
     * @throws InvalidTargetException
     * @throws NotEnoughManaCrystalsException
     * @throws UndefinedBattleCryMechanicException
     * @throws \App\Exceptions\DumbassDeveloperException
     */
    public function playMinion(Minion $card, array $targets = [], $choose_mechanic = null) {
        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');

        $count_minions = count($this->getMinionsInPlay());
        if ($count_minions == Player::$MAX_MINIONS) {
            throw new BattlefieldFullException();
        }

        /* Remove from hand and enter battlefield */
        $this->minions_in_play[$card->getId()] = $card;

        $this->active_mechanics = array_merge($this->active_mechanics, $card->getMechanics());


        /* Early on Summon Phase */

        /* On Play Phase */

        event(new OnPlayPhaseEvent($card, $targets, $choose_mechanic));
        $trigger_queue->resolveQueue();

        /* Late On Summon Phase */

        /* Battlecry Phase */
        event(new BattlecryPhaseEvent($card, $targets));
        $trigger_queue->resolveQueue();

        /* Secret Activation Phase */

        /* After Summon Phase */
        event(new AfterSummonPhaseEvent($card, $targets));
        $trigger_queue->resolveQueue();

        /* Check Game Over Phase */
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
        $attacker->resolvePreparationPhase($target);
        $this->game->checkForGameOver();
        $attacker->resolveCombatPhase($target);
        $this->game->resolveDeaths();
        $this->endSequence();
    }

    /**
     * @param array $targets
     */
    public function useAbility($targets = []) {
        $this->flipHeroPower();
        $this->resolveHeroPower($targets);
        $this->game->resolveDeaths();
        $this->game->checkForGameOver();
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
     * @param $targets
     */
    private function resolveHeroPower($targets) {
        $defending_player = $this->getOtherPlayer();
        $this->hero->useAbility($this, $defending_player, $targets);
        if (!$defending_player->getHero()->isAlive()) {
            $defending_player->killed();
        }

        if (!$this->getHero()->isAlive()) {
            $this->killed();
        }
    }

    /**
     * Phase which flips the hero power so it cannot be used again
     * @throws HeroPowerAlreadyFlippedException
     */
    private function flipHeroPower() {
        if ($this->getHero()->powerIsFlipped()) {
            throw new HeroPowerAlreadyFlippedException('You have already used your ability this turn');
        }

        $this->getHero()->flipHeroPower();
    }

    private function endSequence() {
        $this->game->resolveDeaths();
        $this->game->checkForGameOver();
    }

    /* ---------------------------------- */
}