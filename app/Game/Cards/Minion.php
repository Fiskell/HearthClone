<?php namespace App\Game\Cards;

use App\Exceptions\InvalidTargetException;
use App\Exceptions\MinionAlreadyAttackedException;
use App\Game\Cards\Triggers\TriggerTypes;
use App\Game\Player;
use App\Game\Sequences\Phases\CardPhase;
use App\Game\Sequences\Phases\SubCardPhase;

class Minion extends Card
{
    protected $attack;
    protected $health;
    protected $max_health;
    protected $race;
    protected $alive;
    protected $sleeping;
    protected $frozen                   = false;
    protected $times_attacked_this_turn = 0;
    protected $enchantments             = [];
    protected $auras                    = [];
    protected $position;

    /**
     * @return array
     */
    public function getAuras() {
        return $this->auras;
    }

    /**
     * @param array $auras
     */
    public function setAuras($auras) {
        $this->auras = $auras;
    }

    public function __construct(Player $player, $name = null) {
        parent::__construct($player, $name);

        $this->attack = array_get($this->card_json, 'attack', 0);
        $this->health = array_get($this->card_json, 'health', 0);
        $this->race   = array_get($this->card_json, 'race');

        $this->max_health = $this->health;
        $this->sleeping   = !$this->hasMechanic(Mechanics::$CHARGE);
        $this->alive      = true;
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
        $this->health = ($new_health > $this->max_health) ? $this->max_health : $new_health;
    }

    /**
     * @return mixed
     */
    public function getMaxHealth() {
        return $this->max_health;
    }

    /**
     * Set upper bound for health.
     *
     * @param $new_health
     */
    public function setMaxHealth($new_health) {
        $health_diff      = ($new_health - $this->max_health);
        $this->max_health = $new_health;
        $this->setHealth($this->getHealth() + $health_diff);

    }

    /**
     * @param $damage
     */
    public function takeDamage($damage) {
        if($this->hasTrigger(TriggerTypes::$ON_DAMAGE)) {
            // TODO check if this is where this happens

            /** @var SubCardPhase $trigger */
            $trigger = App('SubCardPhase');
            $trigger->setCard($this);
            $trigger->setPhaseName('on_damage');

            $triggerQueue = App('TriggerQueue');
            $triggerQueue->queue($trigger);
            $triggerQueue->resolveQueue();
        }
        $this->setHealth($this->getHealth() - $damage);
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
        $this->getOwner()->removeFromBoard($this->getId());
        App('DeathProcessing')->queue($this);
    }

    /**
     * Syntactic sugar for initiating the player attack sequence.
     *
     * @param Minion $target
     * @throws InvalidTargetException
     * @throws MinionAlreadyAttackedException
     */
    public function attack(Minion $target) {
        $this->getOwner()->attack($this, $target);
    }

    /**
     * @param Minion $target
     * @throws InvalidTargetException
     * @throws MinionAlreadyAttackedException
     */
    public function resolveCombatPhase(Minion $target) {
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
        $defending_player = $attacking_player->getOtherPlayer();

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
            $this->takeDamage($target->getAttack());

            if ($target->hasMechanic(Mechanics::$FREEZE)) {
                $this->freeze();
            }
        }

        if (!$target_has_divine_shield) {

            $target->takeDamage($this->getAttack());

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
     * @param Minion[] $targets
     * @throws InvalidTargetException
     */
    public function resolveCombo($targets) {
        switch ($this->name) {
            case 'SI:7 Agent':
                if (count($targets) != 1) {
                    throw new InvalidTargetException('Must choose a target to do damage on');
                }

                /** @var Minion $target */
                $target = current($targets);
                $target->takeDamage(2);
        }
    }

    /**
     * Resolve the preparation phase of the player initiated attack sequence.
     *
     * @param $target
     */
    public function resolvePreparationPhase($target) {
        return;
    }

    /**
     * Phase to heal the target.
     *
     * @param $heal_value
     */
    public function heal($heal_value) {
        // todo this is stupid, make it a phase
        $player_minions = $this->getOwner()->getMinionsInPlay();
        foreach($player_minions as $minion) {
            if(!$minion->hasTrigger(TriggerTypes::$ON_MINION_HEALED)) {
                continue;
            }

            if($this->getHealth() == $this->getMaxHealth()) {
                continue;
            }

            /** @var SubCardPhase $phase */
            $phase = App('SubCardPhase');
            $phase->setCard($minion);
            $phase->setPhaseName(TriggerTypes::$ON_MINION_HEALED);

            $triggerQueue = App('TriggerQueue');
            $triggerQueue->queue($phase);
            $triggerQueue->resolveQueue();
        }

        $this->setHealth($this->getHealth() + $heal_value);
    }

    /**
     * @return mixed
     */
    public function getRace() {
        return $this->race;
    }

    /**
     * @param mixed $race
     */
    public function setRace($race) {
        $this->race = $race;
    }

    /**
     * Apply aura to this card.
     *
     * @param Aura $aura
     */
    public function addAura(Aura $aura) {
        // An aura from a single minion only targets a minion once.
        if (isset($this->auras[$aura->getSourceCard()->getId()])) {
            return;
        }

        $this->auras[$aura->getSourceCard()->getId()] = $aura;
        $this->setAttack($this->getAttack() + $aura->getModifiedAttack());

        // todo this won't work for non-charge mechanics
        $modified_mechanics = $aura->getModifiedMechanics();
        $this->mechanics = array_merge($this->mechanics, $modified_mechanics);
    }

    /**
     * Set the position of the minion on the board
     * @param $position
     */
    public function setPosition($position) {
        $minions        = $this->getOwner()->getMinionsInPlay();
        $this->position = $position;

        $minion_map = [];
        // TODO fix, this is lame
        foreach ($minions as $minion) {
            if ($minion->getId() == $this->getId()) {
                continue;
            }
            $minion_map[$minion->getPosition()] = $minion;
        }

        /** @var Minion $minion */
        foreach ($minion_map as $minion) {
            if ($minion->getPosition() < $this->getPosition()) {
                continue;
            }

            $minion->position++;
        }
    }

    /**
     * Get the position of the minion on the board.
     *
     * @return mixed
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * Returns bool based on if minion has specified trigger.
     *
     * @param $trigger_type
     * @return bool
     */
    public function hasTrigger($trigger_type) {
        return !!array_get($this->getTrigger(), $trigger_type);
    }

}