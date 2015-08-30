<?php namespace App\Models;

use App\Exceptions\InvalidTargetException;
use App\Exceptions\MinionAlreadyAttackedException;
use App\Exceptions\MissingCardNameException;
use Exceptions\UndefinedBattleCryMechanicException;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:07 PM
 */
class Card
{
    protected $id;
    protected $name;
    protected $attack;
    protected $health;
    protected $type;
    protected $alive;
    protected $mechanics     = [];
    protected $sub_mechanics = [];
    protected $owner         = null;
    protected $game;
    protected $sleeping;
    /** @var CardSets $card_sets */
    protected $card_sets;
    protected $frozen                   = false;
    protected $times_attacked_this_turn = 0;

    public function __construct(Game $game) {
        $this->game = $game;
    }

    public function load($name = null) {
        if (is_null($name)) {
            throw new MissingCardNameException();
        }

        /** @var CardSets $card_sets */
        $card_sets = app('CardSets');
        $card_json = $card_sets->findCard($name);

        $this->id        = rand(1, 1000000);
        $this->name      = $name;
        $this->attack    = array_get($card_json, 'attack');
        $this->health    = array_get($card_json, 'health');
        $this->type      = array_get($card_json, 'type');
        $this->mechanics = array_get($card_json, 'mechanics', []);

        // TODO fix this jank
        if(strpos(array_get($card_json, 'text', ''), 'Choose One')) {
            $this->mechanics[] = Mechanics::$CHOOSE;
        }

        switch ($card_json['name']) {
            case 'Spellbreaker':
                $this->sub_mechanics = [Mechanics::$BATTLECRY => [Mechanics::$SILENCE]];
        }

        $this->sleeping = !$this->hasMechanic(Mechanics::$CHARGE);
        $this->alive    = true;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return null
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAttack() {
        return $this->attack;
    }


    /**
     * @param $new_attack
     */
    public function setAttack($new_attack) {
        $this->attack = $new_attack;
    }

    /**
     * @return mixed
     */
    public function getHealth() {
        return $this->health;
    }

    /**
     * @param mixed $new_health
     */
    public function setHealth($new_health) {
        $this->health = $new_health;
        if ($this->health <= 0) {
            $this->health = 0;
            $this->killed();
        }
    }

    /**
     * @param $damage
     */
    public function takeDamage($damage) {
        $this->setHealth($this->getHealth() - $damage);
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function isAlive() {
        return $this->alive;
    }

    /**
     * Kill the card and remove it from the board.
     */
    public function killed() {
        $this->alive = false;
        $player      = $this->getOwner();

        if ($this->hasMechanic(Mechanics::$DEATHRATTLE)) {
            $this->resolveDeathrattle();
        }

        $player->removeFromBoard($this->getId());
        $player->recalculateActiveMechanics();
    }

    /**
     * @return Player
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * @param Player|null $owner
     */
    public function setOwner(Player $owner) {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getGame() {
        return $this->game;
    }

    /**
     * @return array
     */
    public function getMechanics() {
        return $this->mechanics;
    }

    /**
     * @return array
     */
    public function getSubMechanics() {
        return $this->sub_mechanics;
    }

    /**
     * @param string $mechanic
     * @return bool
     */
    public function hasMechanic($mechanic) {
        return array_search($mechanic, $this->mechanics) !== false;
    }

    public function removeMechanic($mechanic) {
        $this->mechanics = array_diff($this->mechanics, [$mechanic]);
    }

    public function removeAllMechanics() {
        $this->mechanics = [];
    }

    /**
     * Card instance attacks the target, dealing damage and potentially killing.
     *
     * @param Card $target
     * @throws InvalidTargetException
     * @throws MinionAlreadyAttackedException
     */
    public function attack(Card $target) {

        if ($this->isSleeping()) {
            throw new InvalidTargetException('This minion cannot attack because it is asleep');
        }

        if ($this->isFrozen()) {
            throw new InvalidTargetException('This minion cannot attack because it is frozen');
        }

        if ($this->alreadyAttacked()) {
            throw new MinionAlreadyAttackedException('This minion has already attacked this turn');
        }

        $attacking_player = $this->getOwner();
        $defending_player = Player::getDefendingPlayer($attacking_player);

        /* Taunt */
        $target_has_taunt = $target->hasMechanic(Mechanics::$TAUNT);
        $player_has_taunt = $defending_player->hasMechanic(Mechanics::$TAUNT);

        if (!$target_has_taunt && $player_has_taunt) {
            throw new InvalidTargetException('You may only attack a minion with taunt');
        }

        /* Stealth */
        if ($target->hasMechanic(Mechanics::$STEALTH)) {
            throw new InvalidTargetException('You cannot attack a stealth minion');
        }

        if ($this->hasMechanic(Mechanics::$STEALTH)) {
            $this->removeMechanic(Mechanics::$STEALTH);
        }

        /* Divine Shield */
        $target_has_divine_shield = $target->hasMechanic(Mechanics::$DIVINE_SHIELD);
        if ($target_has_divine_shield) {
            $target->removeMechanic(Mechanics::$DIVINE_SHIELD);
        }

        $attacker_has_divine_shield = $this->hasMechanic(Mechanics::$DIVINE_SHIELD);
        if ($attacker_has_divine_shield) {
            $this->removeMechanic(Mechanics::$DIVINE_SHIELD);
        }

        /* Enrage */
        if ($target->hasMechanic(Mechanics::$ENRAGE)) {
            $target->setAttack($target->getAttack() + 3);
        }

        if ($this->hasMechanic(Mechanics::$ENRAGE)) {
            $this->setAttack($this->getAttack() + 3);
        }

        if (!$attacker_has_divine_shield) {

            $this->setHealth($this->getHealth() - $target->getAttack());

            if ($target->hasMechanic(Mechanics::$FREEZE)) {
                $this->freeze();
            }
        }

        if (!$target_has_divine_shield) {

            $target->setHealth($target->getHealth() - $this->getAttack());

            if ($this->hasMechanic(Mechanics::$FREEZE)) {
                $target->freeze();
            }
        }

        $this->incrementTimesAttackedThisTurn();
    }

    public function isSleeping() {
        return $this->sleeping;
    }

    public function wakeUp() {
        $this->sleeping = false;
    }

    public function isFrozen() {
        return $this->frozen;
    }

    public function freeze() {
        $this->frozen = true;
    }

    public function thaw() {
        $this->frozen = false;
    }

    public function resolveDeathrattle() {
        switch ($this->name) {
            case 'Loot Hoarder':
                $this->getOwner()->drawCard();
        }
    }

    /**
     * @return int
     */
    public function getTimesAttackedThisTurn() {
        return $this->times_attacked_this_turn;
    }

    /**
     * Increment number of times attacked this turn by one.
     * Minions without windfury can only attack once per turn.
     * Minions with windfury can attack twice per turn.
     */
    public function incrementTimesAttackedThisTurn() {
        $this->times_attacked_this_turn++;
    }

    /**
     * Sets times attacked back to zero
     */
    public function resetTimesAttackedThisTurn() {
        $this->times_attacked_this_turn = 0;
    }

    public function alreadyAttacked() {
        if ($this->hasMechanic(Mechanics::$WINDFURY)) {
            return $this->getTimesAttackedThisTurn() == 2;
        }

        return $this->getTimesAttackedThisTurn() == 1;
    }

    /**
     * @param Card[] $targets
     * @throws InvalidTargetException
     */
    public function resolveCombo($targets) {
        switch ($this->name) {
            case 'SI:7 Agent':
                if (count($targets) != 1) {
                    throw new InvalidTargetException('Must choose a target to do damage on');
                }

                /** @var Card $target */
                $target = current($targets);
                $target->takeDamage(2);
        }
    }

    /**
     * @param array $targets
     * @throws InvalidTargetException
     * @throws UndefinedBattleCryMechanicException
     */
    public function resolveBattlecry(array $targets) {
        $card_sub_mechanics      = $this->getSubMechanics();
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
     * @param Card[] $targets
     * @param $chosen_value
     * @throws InvalidTargetException
     */
    public function resolveChoose(array $targets, $chosen_value) {
        switch($this->getName()) {
            case 'Keeper of the Grove':
                if (count($targets) != 1) {
                    throw new InvalidTargetException('Must choose a target to apply combo to');
                }

                /** @var Card $target */
                $target = current($targets);
                if($chosen_value == 1) {
                    $target->takeDamage(2);
                }

                if($chosen_value == 2) {
                    $target->removeAllMechanics();
                }

                break;
        }
    }


}