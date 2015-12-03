<?php namespace App\Game\Sequences\Phases;

use App\Exceptions\DumbassDeveloperException;
use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Card;
use App\Game\Cards\Heroes\AbstractHero;
use App\Game\Cards\Minion;
use App\Game\Cards\Triggers\TargetTypes;
use App\Game\Cards\Triggers\TriggerTypes;
use App\Game\Cards\Weapon;
use App\Game\Player;

abstract class CardPhase extends AbstractPhase
{
    /** @var Minion $minion */
    protected $card;
    protected $targets;
    public    $phase_name;

    public function queue(Card $card, array $targets = []) {
        throw new DumbassDeveloperException('Queue function not overridden');
    }

    public function queueAllForPlayer(Player $player) {
        throw new DumbassDeveloperException('QueueAll function not overridden');
    }

    public function resolve() {

        // todo clean up this mess
        $triggers = array_get($this->card->getTrigger(), $this->phase_name);
        $trigger  = null;
        if (array_get($this->card->getTrigger(), TriggerTypes::$CHOOSE_ONE . '.0')) {
            $trigger  = array_get($this->card->getTrigger(), TriggerTypes::$CHOOSE_ONE . '.0.' . ($this->card->getChooseOption() - 1));
            $triggers = [$trigger];
        }

        $overload_trigger = array_get($this->card->getTrigger(), TriggerTypes::$OVERLOAD);
        if ($overload_trigger) {
            $overload_value = array_get($this->card->getTrigger(), TriggerTypes::$OVERLOAD);
            $this->card->getOwner()->addLockedManaCrystalCount($overload_value);

            return;
        }

        if (is_null($triggers)) {
            throw new DumbassDeveloperException('Trigger not specified for ' . $this->card->getName());
        }

        if (!is_array($triggers)) {
            throw new DumbassDeveloperException('Triggers for ' . $this->card->getName() . ' is not an array');
        }

        foreach ($triggers as $trigger) {
            $targets     = [];
            $target_type = array_get($trigger, 'target_type');
            $target_race = array_get($trigger, 'target_race');
            if ($target_type) {
                $targets = TargetTypes::getTargets($this->card, $target_type, $target_race, $this->targets);
            }

            /* Check if race is correct */
            $this->validateRace($trigger, $targets);

            /* Destroy */
            $this->resolveDestroyTrigger($trigger, $targets);

            /* Freeze */
            $this->resolveFreezeTrigger($trigger, $targets);

            /* Silence */
            $this->resolveSilenceTrigger($trigger, $targets);

            /* Damage */
            $this->resolveDamageTrigger($trigger, $targets);

            /* Spell */
            $this->resolveSpellTrigger($trigger, $targets);

            /* Enchantment */
            $this->resolveEnchantmentTrigger($trigger, $targets);

            /* Discard */
            $this->resolveDiscardTrigger($trigger, $targets);

            /* Draw */
            $this->resolveDrawTrigger($trigger, $targets);

            /* Summon */
            $this->resolveSummonTrigger($trigger);

            /* Modify Mana Crystals */
            $this->modifyManaCrystals($trigger);
        }
    }

    /**
     * @param $trigger
     * @param $targets
     * @return int
     */
    private function resolveDrawTrigger($trigger, $targets) {
        $draw = array_get($trigger, 'draw');

        if (is_null($draw)) {
            return;
        }

        /** @var Player $target */
        foreach ($targets as $target) {
            for ($i = 0; $i < $draw; $i++) {
                $target->drawCard();
            }
        }
    }

    /**
     * @param $trigger
     */
    private function resolveSummonTrigger($trigger) {
        $summon_name     = array_get($trigger, 'summon.name');
        $summon_random   = array_get($trigger, 'summon.random');
        $summon_quantity = array_get($trigger, 'summon.quantity');

        if (is_null($summon_name) && is_null($summon_random)) {
            return;
        }

        if (!is_null($summon_random)) {
            $random_number = app('Random')->getFromRange(0, (count($summon_random) - 1));
            $summon_name   = $summon_random[$random_number];
        }

        /** @var Player $target */
        for ($i = 0; $i < $summon_quantity; $i++) {
            $tmp_minion = app('Minion', [$this->card->getOwner(), $summon_name]);
            $this->card->getOwner()->play($tmp_minion);
        }
    }

    /**
     * @param $trigger
     * @param $targets
     * @throws DumbassDeveloperException
     */
    private function resolveDiscardTrigger($trigger, $targets) {
        $discard = array_get($trigger, 'discard');

        if (is_null($discard)) {
            return;
        }

        /** @var Player $target */
        foreach ($targets as $target) {
            $type     = array_get($discard, 'type');
            $quantity = array_get($discard, 'quantity');
            switch ($type) {
                case 'random':
                    $target->discardRandom($quantity);
                    break;
                default:
                    throw new DumbassDeveloperException('Unknown discard type ' . $type);
            }
        }
    }

    /**
     * @param $trigger
     * @param Minion[] $targets
     * @throws DumbassDeveloperException
     */
    private function resolveEnchantmentTrigger($trigger, $targets) {
        if (array_get($trigger, 'buff') != 'enchantment') {
            return;
        }

        foreach ($targets as $target) {
            $target->setMechanics(array_get($trigger, 'mechanics', []));

            $delta_attack = array_get($trigger, 'attack', 0);
            $target->setAttack($target->getAttack() + $delta_attack);

            // TODO jank
            if ($target instanceof Weapon) {
                continue;
            }

            $delta_max_health = array_get($trigger, 'max_health', 0);
            if ($delta_max_health) {
                if ($delta_max_health == 'double') {
                    $target->setMaxHealth($target->getMaxHealth() + $target->getMaxHealth());
                } else {
                    $target->setMaxHealth($target->getMaxHealth() + $delta_max_health);
                }
            }

            $delta_health = array_get($trigger, 'health', 0);
            $target->setHealth($target->getHealth() + $delta_health);

            $set_health = array_get($trigger, 'set_health');
            if ($set_health) {
                $target->setHealth($set_health);
            }

            $armor = array_get($trigger, 'armor');
            if ($armor && $target instanceof AbstractHero) {
                $target->gainArmor($armor);
            }
        }

        $attack_by_count = array_get($trigger, 'attack_by_count');
        if (!is_null($attack_by_count)) {
            $delta_attack = count(TargetTypes::getTargets($this->card, $attack_by_count, "", $this->targets));
            $this->card->setAttack($this->card->getAttack() + $delta_attack);
        }

        $health_by_count = array_get($trigger, 'health_by_count');
        if (!is_null($health_by_count)) {
            $delta_health = count(TargetTypes::getTargets($this->card, $health_by_count, "", $this->targets));
            $this->card->setMaxHealth($this->card->getHealth() + $delta_health);
        }
    }

    /**
     * @param $trigger
     * @param Minion[] $targets
     * @throws DumbassDeveloperException
     */
    private function resolveSpellTrigger($trigger, $targets) {
        if (array_get($trigger, 'buff') != 'spell') {
            return;
        }

        foreach ($targets as $target) {
            $target->setMechanics(array_get($trigger, 'mechanics', []));

            $delta_attack = array_get($trigger, 'attack', 0);
            $target->setAttack($target->getAttack() + $delta_attack);

            $set_attack = array_get($trigger, 'set_attack');
            if ($set_attack) {
                $target->setAttack($set_attack);
            }

            $delta_health = array_get($trigger, 'health', 0);
            $target->setHealth($target->getHealth() + $delta_health);

            $full_health = array_get($trigger, 'full_health');
            if ($full_health) {
                $target->setHealth($target->getMaxHealth());
            }

            $armor = array_get($trigger, 'armor');
            if ($armor && $target instanceof AbstractHero) {
                $target->gainArmor($armor);
            }
        }

        $attack_by_count = array_get($trigger, 'attack_by_count');
        if (!is_null($attack_by_count)) {
            $delta_attack = count(TargetTypes::getTargets($this->card, $attack_by_count, "", $this->targets));
            $this->card->setAttack($this->card->getAttack() + $delta_attack);
        }

        $health_by_count = array_get($trigger, 'health_by_count');
        if (!is_null($health_by_count)) {
            $delta_health = count(TargetTypes::getTargets($this->card, $health_by_count, "", $this->targets));
            $this->card->setHealth($this->card->getHealth() + $delta_health);
        }
    }

    /**
     * @param $trigger
     * @param Minion[] $targets
     */
    private function resolveSilenceTrigger($trigger, $targets) {
        $silence = array_get($trigger, 'silence');

        if (is_null($silence)) {
            return;
        }

        foreach ($targets as $target) {
            $target->removeAllMechanics();
        }
    }

    /**
     * @param $trigger
     * @param $targets
     * @throws InvalidTargetException
     */
    private function resolveDestroyTrigger($trigger, $targets) {
        $destroy = array_get($trigger, 'destroy');

        if (is_null($destroy)) {
            return;
        }

        foreach ($targets as $target) {
            if ($target instanceof AbstractHero) {
                throw new InvalidTargetException('You are not allowed to directly destroy a hero');
            }

            if ($target instanceof Weapon) {
                $target->getHero()->destroyWeapon();
            }

            if ($target instanceof Minion) {
                $target->killed();
            }
        }
    }

    /**
     * @param $trigger
     * @param $targets
     * @throws InvalidTargetException
     */
    private function validateRace($trigger, $targets) {
        $required_race = array_get($trigger, 'target_race');

        if (!$required_race) {
            return;
        }

        /** @var Minion $target */
        foreach ($targets as $target) {
            if (strtolower($target->getRace()) != strtolower($required_race)) {
                throw new InvalidTargetException('Target must be a ' . $required_race . ' ' . $target->getRace() . ' given');
            }
        }
    }

    /**
     * @param Minion $card
     */
    public function setCard($card) {
        $this->card = $card;
    }

    /**
     * @param mixed $targets
     */
    public function setTargets($targets) {
        $this->targets = $targets;
    }

    /**
     * @param mixed $phase_name
     */
    public function setPhaseName($phase_name) {
        $this->phase_name = $phase_name;
    }

    /**
     * Damage a given target.
     *
     * @param $trigger
     * @param $targets
     */
    private function resolveDamageTrigger($trigger, $targets) {
        $damage = array_get($trigger, 'damage');
        if (!$damage) {
            return;
        }

        /** @var Minion $target */
        foreach ($targets as $target) {
            $target->takeDamage($damage);
        }
    }

    /**
     * @param $trigger
     * @param $targets
     */
    private function resolveFreezeTrigger($trigger, $targets) {
        $freeze = array_get($trigger, 'freeze');
        if (!$freeze) {
            return;
        }

        /** @var Minion $target */
        foreach ($targets as $target) {
            $target->freeze();
        }
    }

    /**
     * @param $trigger
     */
    private function modifyManaCrystals($trigger) {
        $create_mana_crystals = array_get($trigger, 'create_mana_crystals');
        if ($create_mana_crystals) {
            $player = $this->card->getOwner();
            $player->setManaCrystalCount($player->getManaCrystalCount() + $create_mana_crystals);
        }
    }

}